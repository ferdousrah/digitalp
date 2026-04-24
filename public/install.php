<?php
/**
 * Digital Support — Web Installer
 * Place in public/install.php (or public_html/install.php on cPanel).
 * Access: https://yourdomain.com/install.php
 * DELETE THIS FILE AFTER INSTALLATION.
 */

session_start();
@set_time_limit(300);

// Suppress deprecation / notice / strict warnings. On PHP 8.5 the Laravel
// framework still uses the old PDO::MYSQL_ATTR_SSL_CA constant (deprecated,
// but still works). If we let those print, they break the header() redirect.
error_reporting(E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
@ini_set('zend.assertions', '-1');

// Buffer all output. Any stray warning during step processing gets discarded
// before we redirect; real rendered HTML is flushed at the end.
ob_start();

// ── Locate project root (handles standard Laravel + cPanel sibling layouts) ──
$ROOT = null;
foreach ([
    dirname(__DIR__),
    dirname(__DIR__) . '/digitalp',
    __DIR__ . '/..',
    __DIR__ . '/../digitalp',
] as $candidate) {
    $real = realpath($candidate);
    if ($real && file_exists($real . '/bootstrap/app.php')) {
        $ROOT = $real;
        break;
    }
}
if (!$ROOT) {
    die('<h2 style="font-family:sans-serif;padding:40px;">Cannot find project root. Place install.php inside the Laravel project\'s <code>public/</code> folder, or next to a sibling <code>digitalp/</code> folder.</h2>');
}

define('ROOT', $ROOT);
define('ENV_PATH', ROOT . '/.env');
define('INSTALLED_FLAG', ROOT . '/storage/installed');

// ── Block re-run after successful install ──
if (file_exists(INSTALLED_FLAG) && ($_GET['step'] ?? '') !== 'done') {
    render_locked();
    exit;
}

// ── Helpers ──
function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function step_url($step, $extra = []) {
    $q = array_merge(['step' => $step], $extra);
    return basename(__FILE__) . '?' . http_build_query($q);
}

function redirect($step, $extra = []) {
    // Discard any buffered output (deprecation notices, etc.) before sending headers
    while (ob_get_level() > 0) { ob_end_clean(); }
    header('Location: ' . step_url($step, $extra));
    exit;
}

function flash_set($type, $msg) { $_SESSION['flash'][] = compact('type', 'msg'); }
function flash_get() {
    $out = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $out;
}

function generate_app_key() {
    return 'base64:' . base64_encode(random_bytes(32));
}

// Parse a Laravel-style .env file. parse_ini_file() does not handle base64:
// values, ${VAR} references, or equal-signs in values, so we roll our own.
function parse_env_file($path) {
    $out = [];
    if (!is_readable($path)) return $out;
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        if (strpos($line, '=') === false) continue;
        [$k, $v] = explode('=', $line, 2);
        $k = trim($k);
        $v = trim($v);
        if (strlen($v) >= 2) {
            $first = $v[0]; $last = $v[strlen($v) - 1];
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                $v = substr($v, 1, -1);
            }
        }
        $out[$k] = $v;
    }
    return $out;
}

function write_env(array $data) {
    $map = [
        'APP_NAME'         => $data['app_name'] ?? 'Digital Support',
        'APP_ENV'          => 'production',
        'APP_KEY'          => $data['app_key'] ?? generate_app_key(),
        'APP_DEBUG'        => 'false',
        'APP_URL'          => $data['app_url'],
        'LOG_CHANNEL'      => 'stack',
        'LOG_LEVEL'        => 'error',
        'DB_CONNECTION'    => 'mysql',
        'DB_HOST'          => $data['db_host'],
        'DB_PORT'          => $data['db_port'],
        'DB_DATABASE'      => $data['db_database'],
        'DB_USERNAME'      => $data['db_username'],
        'DB_PASSWORD'      => $data['db_password'],
        'BROADCAST_DRIVER' => 'log',
        'CACHE_DRIVER'     => 'file',
        'FILESYSTEM_DISK'  => 'public',
        'QUEUE_CONNECTION' => 'sync',
        'SESSION_DRIVER'   => 'file',
        'SESSION_LIFETIME' => '120',
        'MAIL_MAILER'      => $data['mail_mailer'] ?? 'log',
        'MAIL_HOST'        => $data['mail_host'] ?? '',
        'MAIL_PORT'        => $data['mail_port'] ?? '587',
        'MAIL_USERNAME'    => $data['mail_username'] ?? '',
        'MAIL_PASSWORD'    => $data['mail_password'] ?? '',
        'MAIL_ENCRYPTION'  => $data['mail_encryption'] ?? 'tls',
        'MAIL_FROM_ADDRESS'=> $data['mail_from'] ?? 'noreply@example.com',
        'MAIL_FROM_NAME'   => '${APP_NAME}',
    ];
    $out = '';
    foreach ($map as $k => $v) {
        // Always quote — safest with base64, special chars, spaces, ${VAR} refs, etc.
        // Escape any literal double-quotes inside the value.
        $escaped = str_replace('"', '\\"', (string)$v);
        $out .= $k . '="' . $escaped . '"' . "\n";
    }
    return file_put_contents(ENV_PATH, $out) !== false;
}

function boot_laravel() {
    static $app = null;
    if ($app) return $app;
    require ROOT . '/vendor/autoload.php';
    $app = require ROOT . '/bootstrap/app.php';
    $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    return $app;
}

function run_artisan(string $command, array $params = []) {
    $app = boot_laravel();
    $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
    $output = new \Symfony\Component\Console\Output\BufferedOutput();
    $code = $kernel->call($command, $params, $output);
    return ['code' => $code, 'output' => $output->fetch()];
}

function test_db_connection($host, $port, $db, $user, $pass) {
    try {
        $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        return ['ok' => true];
    } catch (Throwable $e) {
        return ['ok' => false, 'error' => $e->getMessage()];
    }
}

function requirements_check() {
    $exts = ['pdo_mysql','mbstring','gd','zip','fileinfo','bcmath','intl','exif','curl','openssl','tokenizer','xml','ctype','json'];
    $items = [
        ['label' => 'PHP ≥ 8.2', 'ok' => version_compare(PHP_VERSION, '8.2.0', '>='), 'detail' => PHP_VERSION],
    ];
    foreach ($exts as $e) {
        $items[] = ['label' => "ext: {$e}", 'ok' => extension_loaded($e), 'detail' => extension_loaded($e) ? 'loaded' : 'missing'];
    }
    $writables = [
        ROOT . '/storage'         => 'storage/',
        ROOT . '/storage/framework' => 'storage/framework/',
        ROOT . '/storage/logs'    => 'storage/logs/',
        ROOT . '/bootstrap/cache' => 'bootstrap/cache/',
        ROOT                      => ROOT . ' (to write .env)',
    ];
    foreach ($writables as $path => $label) {
        if (!is_dir($path)) { @mkdir($path, 0755, true); }
        $items[] = ['label' => "writable: {$label}", 'ok' => is_writable($path), 'detail' => is_writable($path) ? 'ok' : 'not writable — chmod 755'];
    }
    $items[] = ['label' => 'vendor/ installed', 'ok' => file_exists(ROOT . '/vendor/autoload.php'), 'detail' => file_exists(ROOT . '/vendor/autoload.php') ? 'ok' : 'run composer install'];
    return $items;
}

// ── POST handlers ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $step = $_POST['step'] ?? '';

    if ($step === 'env') {
        $data = [
            'app_name'    => trim($_POST['app_name'] ?? 'Digital Support'),
            'app_url'     => rtrim(trim($_POST['app_url'] ?? ''), '/'),
            'db_host'     => trim($_POST['db_host'] ?? '127.0.0.1'),
            'db_port'     => trim($_POST['db_port'] ?? '3306'),
            'db_database' => trim($_POST['db_database'] ?? ''),
            'db_username' => trim($_POST['db_username'] ?? ''),
            'db_password' => $_POST['db_password'] ?? '',
            'mail_mailer' => $_POST['mail_mailer'] ?? 'log',
            'mail_host'   => trim($_POST['mail_host'] ?? ''),
            'mail_port'   => trim($_POST['mail_port'] ?? '587'),
            'mail_username' => trim($_POST['mail_username'] ?? ''),
            'mail_password' => $_POST['mail_password'] ?? '',
            'mail_encryption' => $_POST['mail_encryption'] ?? 'tls',
            'mail_from'   => trim($_POST['mail_from'] ?? ''),
        ];

        $test = test_db_connection($data['db_host'], $data['db_port'], $data['db_database'], $data['db_username'], $data['db_password']);
        if (!$test['ok']) {
            flash_set('error', 'Database connection failed: ' . $test['error']);
            $_SESSION['env_data'] = $data;
            redirect('env');
        }

        $data['app_key'] = file_exists(ENV_PATH) ? (parse_env_file(ENV_PATH)['APP_KEY'] ?? generate_app_key()) : generate_app_key();
        if (!preg_match('~^base64:~', $data['app_key'])) $data['app_key'] = generate_app_key();

        if (!write_env($data)) {
            flash_set('error', 'Could not write .env file. Check directory permissions.');
            redirect('env');
        }
        flash_set('success', '.env saved and database connection verified.');
        unset($_SESSION['env_data']);
        redirect('migrate');
    }

    if ($step === 'migrate') {
        try {
            $result = run_artisan('migrate', ['--force' => true]);
            $_SESSION['migrate_output'] = $result['output'];
            if ($result['code'] !== 0) {
                flash_set('error', 'Migrations failed. See output below.');
                redirect('migrate');
            }
            if (!empty($_POST['run_seeders'])) {
                $seed = run_artisan('db:seed', ['--force' => true]);
                $_SESSION['migrate_output'] .= "\n--- seeders ---\n" . $seed['output'];
            }
            flash_set('success', 'Migrations complete.');
            redirect('admin');
        } catch (Throwable $e) {
            flash_set('error', 'Error: ' . $e->getMessage());
            redirect('migrate');
        }
    }

    if ($step === 'admin') {
        try {
            boot_laravel();
            $name     = trim($_POST['name'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
                flash_set('error', 'Please enter a name, valid email, and password of at least 8 characters.');
                redirect('admin');
            }
            $user = \App\Models\User::updateOrCreate(
                ['email' => $email],
                ['name' => $name, 'password' => \Illuminate\Support\Facades\Hash::make($password)]
            );
            flash_set('success', 'Admin user created: ' . $email);
            redirect('finish');
        } catch (Throwable $e) {
            flash_set('error', 'Could not create admin: ' . $e->getMessage());
            redirect('admin');
        }
    }

    if ($step === 'finish') {
        try {
            run_artisan('storage:link', ['--force' => true]);
            run_artisan('config:cache');
            run_artisan('route:cache');
            run_artisan('view:cache');
            @file_put_contents(INSTALLED_FLAG, date('c'));
            redirect('done');
        } catch (Throwable $e) {
            flash_set('error', 'Finalization error: ' . $e->getMessage());
            redirect('finish');
        }
    }

    redirect('welcome');
}

// ── Render ──
$step = $_GET['step'] ?? 'welcome';
render_page($step);

// ─────────────────────────────── Views ───────────────────────────────
function render_page($step) {
    // Drop any accidental output (deprecations, warnings) before starting HTML
    while (ob_get_level() > 0) { ob_end_clean(); }
    ob_start();
    $title = [
        'welcome'      => 'Welcome',
        'requirements' => 'System Requirements',
        'env'          => 'Configuration',
        'migrate'      => 'Database Migration',
        'admin'        => 'Admin Account',
        'finish'       => 'Finalize',
        'done'         => 'Installation Complete',
    ][$step] ?? 'Installer';

    $steps = ['welcome' => 'Start', 'requirements' => 'Requirements', 'env' => 'Config', 'migrate' => 'Database', 'admin' => 'Admin', 'finish' => 'Finalize'];
    $stepKeys = array_keys($steps);
    $currentIdx = array_search($step, $stepKeys);
    if ($currentIdx === false) $currentIdx = 0;

    ?><!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= h($title) ?> — Installer</title>
        <style>
            *{box-sizing:border-box;margin:0;padding:0}
            body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Inter,sans-serif;background:#f3f4f6;color:#111827;line-height:1.5;min-height:100vh;padding:40px 20px}
            .wrap{max-width:760px;margin:0 auto;background:#fff;border-radius:12px;box-shadow:0 4px 30px rgba(0,0,0,0.08);overflow:hidden}
            .head{padding:28px 32px;background:linear-gradient(135deg,#0d1f2d,#16a34a);color:#fff}
            .head h1{font-size:1.5rem;font-weight:700;margin-bottom:4px}
            .head p{font-size:0.9rem;opacity:0.85}
            .steps{display:flex;gap:0;padding:16px 32px;background:#f9fafb;border-bottom:1px solid #e5e7eb;overflow-x:auto}
            .steps .s{flex:1;min-width:100px;font-size:0.75rem;text-align:center;color:#9ca3af;position:relative;padding:8px 0;font-weight:600;letter-spacing:0.03em;text-transform:uppercase}
            .steps .s.active{color:#16a34a}
            .steps .s.done{color:#0d1f2d}
            .steps .s::before{content:'';position:absolute;left:0;right:0;bottom:0;height:3px;background:#e5e7eb}
            .steps .s.active::before,.steps .s.done::before{background:#16a34a}
            .body{padding:32px}
            h2{font-size:1.25rem;margin-bottom:8px}
            p.lede{color:#6b7280;margin-bottom:24px}
            .btn{display:inline-block;padding:10px 24px;background:#16a34a;color:#fff;border:none;border-radius:8px;font-size:0.95rem;font-weight:600;cursor:pointer;text-decoration:none;transition:background 0.2s}
            .btn:hover{background:#15803d}
            .btn-secondary{background:#6b7280}
            .btn-secondary:hover{background:#4b5563}
            .alert{padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:0.9rem}
            .alert-error{background:#fee2e2;color:#991b1b;border:1px solid #fca5a5}
            .alert-success{background:#dcfce7;color:#166534;border:1px solid #86efac}
            .req-list{list-style:none;border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;margin-bottom:24px}
            .req-list li{display:flex;justify-content:space-between;align-items:center;padding:12px 16px;border-bottom:1px solid #f3f4f6;font-size:0.9rem}
            .req-list li:last-child{border-bottom:none}
            .req-list .ok{color:#16a34a;font-weight:600}
            .req-list .bad{color:#dc2626;font-weight:600}
            .field{margin-bottom:16px}
            .field label{display:block;font-size:0.85rem;font-weight:600;margin-bottom:6px;color:#374151}
            .field input,.field select{width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:6px;font-size:0.9rem;font-family:inherit}
            .field input:focus,.field select:focus{outline:none;border-color:#16a34a;box-shadow:0 0 0 3px rgba(22,163,74,0.15)}
            .row{display:grid;grid-template-columns:1fr 1fr;gap:16px}
            fieldset{border:1px solid #e5e7eb;border-radius:8px;padding:16px;margin-bottom:20px}
            legend{font-size:0.85rem;font-weight:600;color:#374151;padding:0 8px}
            pre{background:#0d1f2d;color:#d1fae5;padding:16px;border-radius:8px;font-size:0.8rem;overflow:auto;max-height:320px;margin-bottom:20px}
            .muted{color:#6b7280;font-size:0.85rem}
            .links{display:flex;gap:12px;margin-top:16px;flex-wrap:wrap}
            .warn{background:#fef3c7;color:#92400e;border:1px solid #fcd34d;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:0.9rem}
            .actions{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-top:24px}
        </style>
    </head>
    <body>
        <div class="wrap">
            <div class="head">
                <h1>Digital Support Installer</h1>
                <p>Web-based setup wizard. Follow each step to configure your app.</p>
            </div>
            <div class="steps">
                <?php foreach ($stepKeys as $i => $k): ?>
                    <div class="s <?= $i < $currentIdx ? 'done' : ($i === $currentIdx ? 'active' : '') ?>"><?= h($steps[$k]) ?></div>
                <?php endforeach; ?>
            </div>
            <div class="body">
                <?php foreach (flash_get() as $f): ?>
                    <div class="alert alert-<?= h($f['type']) ?>"><?= h($f['msg']) ?></div>
                <?php endforeach; ?>
                <?php
                $fn = 'view_' . $step;
                if (function_exists($fn)) $fn(); else view_welcome();
                ?>
            </div>
        </div>
    </body>
    </html>
    <?php
}

function view_welcome() {
    ?>
    <h2>Welcome</h2>
    <p class="lede">This wizard will configure your Digital Support installation.</p>
    <p class="muted" style="margin-bottom:20px">Before continuing, make sure:</p>
    <ul style="margin-bottom:24px;padding-left:20px;color:#374151;font-size:0.9rem">
        <li>Composer dependencies are uploaded (<code>vendor/</code> folder present)</li>
        <li>You have a MySQL/MariaDB database created</li>
        <li>You know the database credentials</li>
    </ul>
    <a class="btn" href="<?= h(step_url('requirements')) ?>">Start Installation →</a>
    <?php
}

function view_requirements() {
    $items = requirements_check();
    $allOk = !in_array(false, array_column($items, 'ok'), true);
    ?>
    <h2>System Requirements</h2>
    <p class="lede">Checking PHP version, extensions, and directory permissions.</p>
    <ul class="req-list">
        <?php foreach ($items as $i): ?>
            <li>
                <span><?= h($i['label']) ?></span>
                <span class="<?= $i['ok'] ? 'ok' : 'bad' ?>"><?= $i['ok'] ? '✓ ' : '✗ ' ?><?= h($i['detail']) ?></span>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php if (!$allOk): ?>
        <div class="alert alert-error">Fix the failing items above, then refresh this page.</div>
        <a class="btn btn-secondary" href="<?= h(step_url('requirements')) ?>">Re-check</a>
    <?php else: ?>
        <div class="actions">
            <a class="btn btn-secondary" href="<?= h(step_url('welcome')) ?>">Back</a>
            <a class="btn" href="<?= h(step_url('env')) ?>">Continue →</a>
        </div>
    <?php endif; ?>
    <?php
}

function view_env() {
    $d = $_SESSION['env_data'] ?? [];
    $existing = file_exists(ENV_PATH) ? parse_env_file(ENV_PATH) : [];
    $defaultUrl = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
    $g = function ($k, $fb = '') use ($d, $existing) { return h($d[$k] ?? $existing[strtoupper($k)] ?? $fb); };
    ?>
    <h2>Configuration</h2>
    <p class="lede">Enter your application and database details. We'll write them to <code>.env</code>.</p>
    <form method="post">
        <input type="hidden" name="step" value="env">
        <fieldset>
            <legend>Application</legend>
            <div class="field">
                <label>App Name</label>
                <input type="text" name="app_name" value="<?= $g('app_name', 'Digital Support') ?>" required>
            </div>
            <div class="field">
                <label>App URL</label>
                <input type="url" name="app_url" value="<?= $g('app_url', $defaultUrl) ?>" required>
            </div>
        </fieldset>
        <fieldset>
            <legend>Database</legend>
            <div class="row">
                <div class="field">
                    <label>Host</label>
                    <input type="text" name="db_host" value="<?= $g('db_host', '127.0.0.1') ?>" required>
                </div>
                <div class="field">
                    <label>Port</label>
                    <input type="text" name="db_port" value="<?= $g('db_port', '3306') ?>" required>
                </div>
            </div>
            <div class="field">
                <label>Database Name</label>
                <input type="text" name="db_database" value="<?= $g('db_database') ?>" required>
            </div>
            <div class="row">
                <div class="field">
                    <label>Username</label>
                    <input type="text" name="db_username" value="<?= $g('db_username') ?>" required>
                </div>
                <div class="field">
                    <label>Password</label>
                    <input type="password" name="db_password" value="<?= $g('db_password') ?>">
                </div>
            </div>
        </fieldset>
        <fieldset>
            <legend>Mail (optional)</legend>
            <div class="field">
                <label>Mailer</label>
                <select name="mail_mailer">
                    <?php foreach (['log','smtp','sendmail'] as $m): ?>
                        <option value="<?= h($m) ?>" <?= ($d['mail_mailer'] ?? 'log') === $m ? 'selected' : '' ?>><?= h($m) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="row">
                <div class="field">
                    <label>SMTP Host</label>
                    <input type="text" name="mail_host" value="<?= $g('mail_host') ?>">
                </div>
                <div class="field">
                    <label>SMTP Port</label>
                    <input type="text" name="mail_port" value="<?= $g('mail_port', '587') ?>">
                </div>
            </div>
            <div class="row">
                <div class="field">
                    <label>SMTP Username</label>
                    <input type="text" name="mail_username" value="<?= $g('mail_username') ?>">
                </div>
                <div class="field">
                    <label>SMTP Password</label>
                    <input type="password" name="mail_password">
                </div>
            </div>
            <div class="field">
                <label>From Address</label>
                <input type="email" name="mail_from" value="<?= $g('mail_from') ?>" placeholder="noreply@example.com">
            </div>
        </fieldset>
        <div class="actions">
            <a class="btn btn-secondary" href="<?= h(step_url('requirements')) ?>">Back</a>
            <button class="btn" type="submit">Test & Save →</button>
        </div>
    </form>
    <?php
}

function view_migrate() {
    $output = $_SESSION['migrate_output'] ?? null;
    unset($_SESSION['migrate_output']);
    ?>
    <h2>Database Migration</h2>
    <p class="lede">Create database tables and (optionally) seed default content.</p>
    <?php if ($output): ?><pre><?= h($output) ?></pre><?php endif; ?>
    <form method="post">
        <input type="hidden" name="step" value="migrate">
        <div class="field">
            <label style="display:inline-flex;align-items:center;gap:8px;font-weight:400">
                <input type="checkbox" name="run_seeders" value="1" style="width:auto">
                Also run database seeders (loads default categories, settings, sample content)
            </label>
        </div>
        <div class="actions">
            <a class="btn btn-secondary" href="<?= h(step_url('env')) ?>">Back</a>
            <button class="btn" type="submit">Run Migrations →</button>
        </div>
    </form>
    <?php
}

function view_admin() {
    try {
        boot_laravel();
        $hasAdmin = \App\Models\User::query()->exists();
    } catch (Throwable $e) {
        $hasAdmin = false;
    }
    ?>
    <h2>Admin Account</h2>
    <p class="lede">Create or update the primary administrator account.</p>
    <?php if ($hasAdmin): ?>
        <div class="warn">An admin user already exists. Filling this form will update the existing user matching the email, or create a new one.</div>
    <?php endif; ?>
    <form method="post">
        <input type="hidden" name="step" value="admin">
        <div class="field">
            <label>Name</label>
            <input type="text" name="name" value="Admin" required>
        </div>
        <div class="field">
            <label>Email</label>
            <input type="email" name="email" value="admin@example.com" required>
        </div>
        <div class="field">
            <label>Password (min 8 characters)</label>
            <input type="password" name="password" minlength="8" required>
        </div>
        <div class="actions">
            <a class="btn btn-secondary" href="<?= h(step_url('migrate')) ?>">Back</a>
            <button class="btn" type="submit">Create Admin →</button>
        </div>
    </form>
    <?php
}

function view_finish() {
    ?>
    <h2>Finalize</h2>
    <p class="lede">Last step — link storage, cache config/routes/views, and lock the installer.</p>
    <form method="post">
        <input type="hidden" name="step" value="finish">
        <div class="actions">
            <a class="btn btn-secondary" href="<?= h(step_url('admin')) ?>">Back</a>
            <button class="btn" type="submit">Finalize Installation →</button>
        </div>
    </form>
    <?php
}

function view_done() {
    $appUrl = parse_env_file(ENV_PATH)['APP_URL'] ?? '/';
    $appUrl = rtrim($appUrl, '/');
    ?>
    <h2 style="color:#16a34a">✓ Installation Complete</h2>
    <p class="lede">Your application is ready.</p>
    <div class="warn">
        <strong>Important:</strong> Delete <code>install.php</code> from your server now for security.
    </div>
    <div class="links">
        <a class="btn" href="<?= h($appUrl) ?>">Visit Site</a>
        <a class="btn" href="<?= h($appUrl . '/admin/login') ?>">Admin Panel</a>
    </div>
    <p class="muted" style="margin-top:24px">A lock file was created at <code>storage/installed</code>. To re-run the installer, delete that file.</p>
    <?php
}

function render_locked() {
    while (ob_get_level() > 0) { ob_end_clean(); }
    ob_start();
    ?><!DOCTYPE html>
    <html><head><meta charset="UTF-8"><title>Installed</title>
    <style>body{font-family:sans-serif;background:#f3f4f6;color:#111827;padding:60px 20px;text-align:center}
    .box{max-width:520px;margin:0 auto;background:#fff;padding:40px;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,0.08)}
    h1{color:#16a34a;margin-bottom:12px}
    .warn{background:#fee2e2;color:#991b1b;padding:16px;border-radius:8px;margin-top:20px;font-size:0.9rem}
    </style></head><body>
    <div class="box">
        <h1>✓ Already Installed</h1>
        <p style="color:#6b7280">This application has already been installed.</p>
        <div class="warn"><strong>Delete install.php from your server now.</strong></div>
        <p style="margin-top:20px;color:#9ca3af;font-size:0.85rem">To re-run, delete <code>storage/installed</code> first.</p>
    </div></body></html>
    <?php
}
