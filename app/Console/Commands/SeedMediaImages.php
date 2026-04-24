<?php

namespace App\Console\Commands;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Console\Command;

class SeedMediaImages extends Command
{
    protected $signature = 'seed:media-images {--products} {--brands} {--categories} {--all} {--fresh : Clear existing media first}';
    protected $description = 'Generate professional placeholder images and attach via Spatie Media Library';

    private string $fontBold = 'C:/Windows/Fonts/segoeuib.ttf';
    private string $fontRegular = 'C:/Windows/Fonts/segoeui.ttf';
    private string $fontLight = 'C:/Windows/Fonts/segoeuil.ttf';

    private array $categoryThemes = [
        'Laptop'             => ['accent' => [37, 99, 235],  'bg2' => [239, 246, 255]],
        'Desktop PC'         => ['accent' => [30, 64, 175],  'bg2' => [238, 242, 255]],
        'Desktop Component'  => ['accent' => [234, 88, 12],  'bg2' => [255, 247, 237]],
        'Monitor'            => ['accent' => [124, 58, 237], 'bg2' => [245, 243, 255]],
        'Printer'            => ['accent' => [13, 148, 136], 'bg2' => [240, 253, 250]],
        'Networking'         => ['accent' => [22, 163, 74],  'bg2' => [240, 253, 244]],
        'Storage'            => ['accent' => [220, 38, 38],  'bg2' => [254, 242, 242]],
        'Accessories'        => ['accent' => [219, 39, 119], 'bg2' => [253, 242, 248]],
        'Gaming'             => ['accent' => [220, 38, 38],  'bg2' => [24, 24, 27]],
        'Software'           => ['accent' => [79, 70, 229],  'bg2' => [238, 242, 255]],
        'Camera'             => ['accent' => [75, 85, 99],   'bg2' => [249, 250, 251]],
    ];

    private array $brandColors = [
        'HP'              => [0, 96, 170],
        'Dell'            => [0, 120, 215],
        'Lenovo'          => [232, 17, 35],
        'ASUS'            => [0, 47, 108],
        'Acer'            => [131, 178, 0],
        'MSI'             => [255, 0, 0],
        'Gigabyte'        => [33, 72, 148],
        'Samsung'         => [20, 40, 160],
        'Canon'           => [188, 0, 45],
        'Epson'           => [0, 55, 120],
        'Brother'         => [0, 101, 163],
        'Logitech'        => [0, 178, 89],
        'Razer'           => [68, 215, 86],
        'TP-Link'         => [74, 179, 156],
        'Intel'           => [0, 113, 197],
        'AMD'             => [237, 28, 36],
        'BenQ'            => [89, 35, 120],
        'Corsair'         => [242, 200, 8],
        'Western Digital'  => [0, 53, 85],
        'Apple'           => [51, 51, 51],
        'Huawei'          => [207, 9, 33],
        'Cooler Master'   => [128, 0, 128],
        'NZXT'            => [34, 34, 34],
        'Dahua'           => [229, 0, 18],
    ];

    public function handle(): int
    {
        $all = $this->option('all');

        if ($this->option('fresh')) {
            $this->info('Clearing existing media...');
            \Spatie\MediaLibrary\MediaCollections\Models\Media::truncate();
            // Clean media directories
            $mediaPath = storage_path('app/public');
            $dirs = glob($mediaPath . '/[0-9]*', GLOB_ONLYDIR);
            foreach ($dirs as $dir) {
                $this->deleteDirectory($dir);
            }
            $this->info('Existing media cleared.');
        }

        if ($all || $this->option('products')) {
            $this->seedProductImages();
        }
        if ($all || $this->option('brands')) {
            $this->seedBrandLogos();
        }
        if ($all || $this->option('categories')) {
            $this->seedCategoryImages();
        }

        if (!$all && !$this->option('products') && !$this->option('brands') && !$this->option('categories')) {
            $this->info('No option specified. Use --all, --products, --brands, or --categories.');
            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Media image seeding complete!');
        return self::SUCCESS;
    }

    private function seedProductImages(): void
    {
        $products = Product::with(['categories', 'brand'])->get();
        $this->info("Generating images for {$products->count()} products...");
        $bar = $this->output->createProgressBar($products->count());

        $tempDir = storage_path('app/temp-images');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        foreach ($products as $product) {
            if ($product->hasMedia('product_images')) {
                $bar->advance();
                continue;
            }

            $parentCat = $this->getParentCategoryName($product);
            $subCat = $this->getSubcategoryName($product);
            $theme = $this->categoryThemes[$parentCat] ?? ['accent' => [107, 114, 128], 'bg2' => [248, 250, 252]];

            // Main product image
            $mainPath = $this->generateProductImage($product, $parentCat, $subCat, $theme, 600, 600, $tempDir);
            $product->addMedia($mainPath)->toMediaCollection('product_images');

            // Second angle
            $altPath = $this->generateProductImage($product, $parentCat, $subCat, $theme, 600, 600, $tempDir, true);
            $product->addMedia($altPath)->toMediaCollection('product_images');

            // Thumbnail
            $thumbPath = $this->generateProductImage($product, $parentCat, $subCat, $theme, 300, 300, $tempDir);
            $product->addMedia($thumbPath)->toMediaCollection('product_thumbnail');

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Product images generated.');
        $this->cleanupTemp($tempDir);
    }

    private function seedBrandLogos(): void
    {
        $brands = Brand::all();
        $this->info("Generating logos for {$brands->count()} brands...");
        $bar = $this->output->createProgressBar($brands->count());

        $tempDir = storage_path('app/temp-images');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        foreach ($brands as $brand) {
            if ($brand->hasMedia('brand_logo')) {
                $bar->advance();
                continue;
            }

            $color = $this->brandColors[$brand->name] ?? [107, 114, 128];
            $path = $this->generateBrandLogo($brand->name, $color, 300, 200, $tempDir);
            $brand->addMedia($path)->toMediaCollection('brand_logo');

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Brand logos generated.');
        $this->cleanupTemp($tempDir);
    }

    private function seedCategoryImages(): void
    {
        $categories = Category::whereNull('parent_id')->get();
        $this->info("Generating images for {$categories->count()} root categories...");
        $bar = $this->output->createProgressBar($categories->count());

        $tempDir = storage_path('app/temp-images');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        foreach ($categories as $category) {
            if ($category->hasMedia('category_image')) {
                $bar->advance();
                continue;
            }

            $theme = $this->categoryThemes[$category->name] ?? ['accent' => [107, 114, 128], 'bg2' => [248, 250, 252]];
            $path = $this->generateCategoryImage($category->name, $theme, 800, 400, $tempDir);
            $category->addMedia($path)->toMediaCollection('category_image');

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Category images generated.');
        $this->cleanupTemp($tempDir);
    }

    // -------------------------------------------------------
    // Product Image Generator
    // -------------------------------------------------------
    private function generateProductImage(Product $product, string $parentCat, string $subCat, array $theme, int $w, int $h, string $tempDir, bool $altAngle = false): string
    {
        $img = imagecreatetruecolor($w, $h);
        imagesavealpha($img, true);
        imagealphablending($img, true);

        // Clean white/light background
        $isGaming = ($parentCat === 'Gaming');
        if ($isGaming) {
            $this->fillGradientV($img, $w, $h, [24, 24, 27], [40, 40, 45]);
        } else {
            $this->fillGradientV($img, $w, $h, [255, 255, 255], $theme['bg2']);
        }

        // Subtle corner accent
        $accentAlpha = imagecolorallocatealpha($img, $theme['accent'][0], $theme['accent'][1], $theme['accent'][2], 110);
        imagefilledellipse($img, 0, 0, (int)($w * 0.6), (int)($w * 0.6), $accentAlpha);
        if ($altAngle) {
            imagefilledellipse($img, $w, $h, (int)($w * 0.5), (int)($w * 0.5), $accentAlpha);
        }

        // Draw product silhouette
        $iconColor = $isGaming
            ? imagecolorallocate($img, $theme['accent'][0], $theme['accent'][1], $theme['accent'][2])
            : imagecolorallocate($img, (int)($theme['accent'][0] * 0.7), (int)($theme['accent'][1] * 0.7), (int)($theme['accent'][2] * 0.7));
        $iconLight = $isGaming
            ? imagecolorallocatealpha($img, 255, 255, 255, 90)
            : imagecolorallocatealpha($img, $theme['accent'][0], $theme['accent'][1], $theme['accent'][2], 90);

        $cx = (int)($w / 2);
        $cy = (int)($h * 0.38);
        $scale = $w / 600.0;

        if ($altAngle) {
            $cy += (int)(10 * $scale);
        }

        $this->drawProductIcon($img, $parentCat, $subCat, $cx, $cy, $scale, $iconColor, $iconLight, $altAngle);

        // Product name
        $textColor = $isGaming ? [255, 255, 255] : [31, 41, 55];
        $brandColor = $theme['accent'];
        $nameY = (int)($h * 0.72);

        // Brand name
        $brandName = $product->brand?->name ?? '';
        if ($brandName) {
            $this->drawCenteredText($img, $brandName, $this->fontBold, 11 * $scale, $cx, $nameY, $brandColor);
            $nameY += (int)(22 * $scale);
        }

        // Product name (wrap to 2 lines max)
        $productName = $product->name;
        $maxW = $w - (int)(60 * $scale);
        $lines = $this->wrapTextTTF($productName, $this->fontRegular, 10 * $scale, $maxW);
        $lines = array_slice($lines, 0, 2);
        if (count($lines) === 2 && strlen($lines[1]) > 35) {
            $lines[1] = substr($lines[1], 0, 32) . '...';
        }

        foreach ($lines as $line) {
            $this->drawCenteredText($img, $line, $this->fontRegular, 10 * $scale, $cx, $nameY, $textColor);
            $nameY += (int)(18 * $scale);
        }

        // Price
        $priceY = $nameY + (int)(6 * $scale);
        $price = number_format((float)$product->price, 0);
        $this->drawCenteredText($img, "BDT {$price}", $this->fontBold, 13 * $scale, $cx, $priceY, $theme['accent']);

        $filename = $tempDir . '/' . uniqid('product_') . '.png';
        imagepng($img, $filename, 6);
        imagedestroy($img);

        return $filename;
    }

    // -------------------------------------------------------
    // Draw Product Icon by Category
    // -------------------------------------------------------
    private function drawProductIcon(\GdImage $img, string $parentCat, string $subCat, int $cx, int $cy, float $s, int $color, int $lightColor, bool $alt): void
    {
        imagesetthickness($img, max(1, (int)(2 * $s)));

        match ($parentCat) {
            'Laptop' => $this->drawLaptop($img, $cx, $cy, $s, $color, $lightColor, $alt),
            'Desktop PC' => $this->drawDesktopPC($img, $cx, $cy, $s, $color, $lightColor),
            'Desktop Component' => $this->drawComponent($img, $subCat, $cx, $cy, $s, $color, $lightColor),
            'Monitor' => $this->drawMonitor($img, $cx, $cy, $s, $color, $lightColor, $alt),
            'Printer' => $this->drawPrinter($img, $cx, $cy, $s, $color, $lightColor),
            'Networking' => $this->drawRouter($img, $cx, $cy, $s, $color, $lightColor),
            'Storage' => $this->drawStorage($img, $subCat, $cx, $cy, $s, $color, $lightColor),
            'Accessories' => $this->drawAccessory($img, $subCat, $cx, $cy, $s, $color, $lightColor),
            'Gaming' => $this->drawGaming($img, $subCat, $cx, $cy, $s, $color, $lightColor),
            'Camera' => $this->drawCamera($img, $cx, $cy, $s, $color, $lightColor),
            default => $this->drawGenericProduct($img, $cx, $cy, $s, $color, $lightColor),
        };

        imagesetthickness($img, 1);
    }

    private function drawLaptop(\GdImage $img, int $cx, int $cy, float $s, int $color, int $light, bool $alt): void
    {
        $sw = (int)(140 * $s); $sh = (int)(90 * $s);
        $bh = (int)(12 * $s);
        $screenTop = $cy - (int)($sh / 2);

        // Screen
        imagefilledrectangle($img, $cx - $sw / 2, $screenTop, $cx + $sw / 2, $screenTop + $sh, $light);
        imagerectangle($img, $cx - $sw / 2, $screenTop, $cx + $sw / 2, $screenTop + $sh, $color);

        // Inner screen
        $pad = (int)(8 * $s);
        $innerColor = imagecolorallocatealpha($img, 200, 220, 255, 80);
        imagefilledrectangle($img, $cx - $sw / 2 + $pad, $screenTop + $pad, $cx + $sw / 2 - $pad, $screenTop + $sh - $pad, $innerColor);

        // Base / keyboard
        $baseTop = $screenTop + $sh;
        $bw = (int)(160 * $s);
        $points = [
            $cx - $sw / 2, $baseTop,
            $cx + $sw / 2, $baseTop,
            $cx + $bw / 2, $baseTop + $bh,
            $cx - $bw / 2, $baseTop + $bh,
        ];
        imagefilledpolygon($img, $points, $light);
        imagepolygon($img, $points, 4, $color);

        // Touchpad line
        $tpw = (int)(30 * $s);
        imageline($img, $cx - $tpw, $baseTop + $bh - (int)(3 * $s), $cx + $tpw, $baseTop + $bh - (int)(3 * $s), $color);

        // Keyboard dots
        for ($row = 0; $row < 2; $row++) {
            for ($col = 0; $col < 8; $col++) {
                $kx = $cx - (int)(50 * $s) + $col * (int)(14 * $s);
                $ky = $baseTop + (int)(3 * $s) + $row * (int)(4 * $s);
                imagefilledrectangle($img, $kx, $ky, $kx + (int)(8 * $s), $ky + (int)(2 * $s), $color);
            }
        }
    }

    private function drawDesktopPC(\GdImage $img, int $cx, int $cy, float $s, int $color, int $light): void
    {
        $tw = (int)(80 * $s); $th = (int)(130 * $s);
        $tx = $cx - $tw / 2; $ty = $cy - $th / 2;

        // Tower case
        imagefilledrectangle($img, $tx, $ty, $tx + $tw, $ty + $th, $light);
        imagerectangle($img, $tx, $ty, $tx + $tw, $ty + $th, $color);

        // Front panel divider
        imageline($img, $tx, $ty + (int)(30 * $s), $tx + $tw, $ty + (int)(30 * $s), $color);

        // DVD slot
        imagefilledrectangle($img, $tx + (int)(10 * $s), $ty + (int)(10 * $s), $tx + $tw - (int)(10 * $s), $ty + (int)(18 * $s), $color);

        // Power button
        imagefilledellipse($img, $tx + $tw - (int)(15 * $s), $ty + (int)(40 * $s), (int)(10 * $s), (int)(10 * $s), $color);

        // Ventilation lines
        for ($i = 0; $i < 5; $i++) {
            $vy = $ty + $th - (int)(40 * $s) + $i * (int)(6 * $s);
            imageline($img, $tx + (int)(15 * $s), $vy, $tx + $tw - (int)(15 * $s), $vy, $color);
        }
    }

    private function drawComponent(\GdImage $img, string $subCat, int $cx, int $cy, float $s, int $color, int $light): void
    {
        $sub = strtolower($subCat);
        if (str_contains($sub, 'processor') || str_contains($sub, 'cpu')) {
            $this->drawCPU($img, $cx, $cy, $s, $color, $light);
        } elseif (str_contains($sub, 'graphics') || str_contains($sub, 'gpu')) {
            $this->drawGPU($img, $cx, $cy, $s, $color, $light);
        } elseif (str_contains($sub, 'motherboard')) {
            $this->drawMotherboard($img, $cx, $cy, $s, $color, $light);
        } elseif (str_contains($sub, 'ram') || str_contains($sub, 'memory')) {
            $this->drawRAM($img, $cx, $cy, $s, $color, $light);
        } elseif (str_contains($sub, 'power') || str_contains($sub, 'psu')) {
            $this->drawPSU($img, $cx, $cy, $s, $color, $light);
        } elseif (str_contains($sub, 'casing') || str_contains($sub, 'case')) {
            $this->drawDesktopPC($img, $cx, $cy, $s, $color, $light);
        } elseif (str_contains($sub, 'cooler')) {
            $this->drawCooler($img, $cx, $cy, $s, $color, $light);
        } else {
            $this->drawCPU($img, $cx, $cy, $s, $color, $light);
        }
    }

    private function drawCPU(\GdImage $img, int $cx, int $cy, float $s, int $color, int $light): void
    {
        $size = (int)(70 * $s);

        // CPU body
        imagefilledrectangle($img, $cx - $size, $cy - $size, $cx + $size, $cy + $size, $light);
        imagerectangle($img, $cx - $size, $cy - $size, $cx + $size, $cy + $size, $color);

        // Heat spreader
        $hs = (int)(50 * $s);
        imagerectangle($img, $cx - $hs, $cy - $hs, $cx + $hs, $cy + $hs, $color);

        // Center text area
        $is = (int)(25 * $s);
        imagefilledrectangle($img, $cx - $is, $cy - $is, $cx + $is, $cy + $is, $color);

        // Pins on each side
        $pinLen = (int)(10 * $s); $pinGap = (int)(12 * $s);
        $numPins = (int)($size * 2 / $pinGap) - 1;
        for ($i = 0; $i < $numPins; $i++) {
            $p = $cy - $size + $pinGap + $i * $pinGap;
            // Left pins
            imageline($img, $cx - $size - $pinLen, $p, $cx - $size, $p, $color);
            // Right pins
            imageline($img, $cx + $size, $p, $cx + $size + $pinLen, $p, $color);
        }
        for ($i = 0; $i < $numPins; $i++) {
            $p = $cx - $size + $pinGap + $i * $pinGap;
            // Top pins
            imageline($img, $p, $cy - $size - $pinLen, $p, $cy - $size, $color);
            // Bottom pins
            imageline($img, $p, $cy + $size, $p, $cy + $size + $pinLen, $color);
        }
    }

    private function drawGPU(\GdImage $img, int $cx, int $cy, float $s, int $color, int $light): void
    {
        $w = (int)(160 * $s); $h = (int)(80 * $s);
        $x = $cx - $w / 2; $y = $cy - $h / 2;

        // PCB body
        imagefilledrectangle($img, $x, $y, $x + $w, $y + $h, $light);
        imagerectangle($img, $x, $y, $x + $w, $y + $h, $color);

        // Fan 1
        $fanR = (int)(30 * $s);
        imageellipse($img, $cx - (int)(35 * $s), $cy, $fanR * 2, $fanR * 2, $color);
        imageellipse($img, $cx - (int)(35 * $s), $cy, (int)($fanR * 0.4), (int)($fanR * 0.4), $color);
        // Fan blades
        for ($a = 0; $a < 360; $a += 60) {
            $rad = deg2rad($a);
            $fx = $cx - (int)(35 * $s);
            imageline($img, $fx, $cy, $fx + (int)(cos($rad) * $fanR * 0.9), $cy + (int)(sin($rad) * $fanR * 0.9), $color);
        }

        // Fan 2
        imageellipse($img, $cx + (int)(35 * $s), $cy, $fanR * 2, $fanR * 2, $color);
        imageellipse($img, $cx + (int)(35 * $s), $cy, (int)($fanR * 0.4), (int)($fanR * 0.4), $color);
        for ($a = 30; $a < 390; $a += 60) {
            $rad = deg2rad($a);
            $fx = $cx + (int)(35 * $s);
            imageline($img, $fx, $cy, $fx + (int)(cos($rad) * $fanR * 0.9), $cy + (int)(sin($rad) * $fanR * 0.9), $color);
        }

        // PCIe connector
        imagefilledrectangle($img, $x + (int)(10 * $s), $y + $h, $x + $w - (int)(30 * $s), $y + $h + (int)(8 * $s), $color);

        // Power connector
        for ($i = 0; $i < 3; $i++) {
            $px = $x + $w - (int)(20 * $s) + $i * (int)(6 * $s);
            imagefilledrectangle($img, $px, $y - (int)(6 * $s), $px + (int)(4 * $s), $y, $color);
        }
    }

    private function drawMotherboard(\GdImage $img, int $cx, int $cy, float $s, int $color, int $light): void
    {
        $w = (int)(120 * $s); $h = (int)(110 * $s);
        $x = $cx - $w / 2; $y = $cy - $h / 2;

        imagefilledrectangle($img, $x, $y, $x + $w, $y + $h, $light);
        imagerectangle($img, $x, $y, $x + $w, $y + $h, $color);

        // CPU socket
        $ss = (int)(25 * $s);
        imagerectangle($img, $cx - $ss, $cy - (int)(20 * $s) - $ss, $cx + $ss, $cy - (int)(20 * $s) + $ss, $color);

        // RAM slots
        for ($i = 0; $i < 2; $i++) {
            $rx = $cx + (int)(35 * $s) + $i * (int)(8 * $s);
            imagefilledrectangle($img, $rx, $y + (int)(10 * $s), $rx + (int)(4 * $s), $y + $h - (int)(20 * $s), $light);
            imagerectangle($img, $rx, $y + (int)(10 * $s), $rx + (int)(4 * $s), $y + $h - (int)(20 * $s), $color);
        }

        // PCIe slots
        for ($i = 0; $i < 3; $i++) {
            $sy = $cy + (int)(15 * $s) + $i * (int)(15 * $s);
            imagefilledrectangle($img, $x + (int)(10 * $s), $sy, $x + (int)(80 * $s), $sy + (int)(6 * $s), $color);
        }

        // I/O area
        imagefilledrectangle($img, $x, $y, $x + (int)(15 * $s), $y + (int)(35 * $s), $color);

        // Chipset
        imagefilledrectangle($img, $cx - (int)(10 * $s), $cy + (int)(5 * $s), $cx + (int)(10 * $s), $cy + (int)(15 * $s), $color);
    }

    private function drawRAM(\GdImage $img, int $cx, int $cy, float $s, int $color, int $light): void
    {
        $w = (int)(20 * $s); $h = (int)(120 * $s);
        $x = $cx - $w / 2; $y = $cy - $h / 2;

        // PCB
        imagefilledrectangle($img, $x, $y, $x + $w, $y + $h, $light);
        imagerectangle($img, $x, $y, $x + $w, $y + $h, $color);

        // Heatspreader
        $hx = $x - (int)(5 * $s);
        $hw = $w + (int)(10 * $s);
        imagerectangle($img, $hx, $y + (int)(5 * $s), $hx + $hw, $y + $h - (int)(15 * $s), $color);

        // Chips
        for ($i = 0; $i < 4; $i++) {
            $chipY = $y + (int)(20 * $s) + $i * (int)(22 * $s);
            imagefilledrectangle($img, $x + (int)(3 * $s), $chipY, $x + $w - (int)(3 * $s), $chipY + (int)(12 * $s), $color);
        }

        // Notch at bottom
        imagefilledrectangle($img, $cx - (int)(3 * $s), $y + $h - (int)(4 * $s), $cx + (int)(3 * $s), $y + $h, imagecolorallocate($img, 255, 255, 255));

        // Contact pins
        for ($i = 0; $i < 8; $i++) {
            $px = $x + (int)(2 * $s) + $i * (int)(2 * $s);
            imageline($img, $px, $y + $h, $px, $y + $h + (int)(5 * $s), $color);
        }
    }

    private function drawPSU(\GdImage $img, int $cx, int $cy, float $s, int $color, int $light): void
    {
        $w = (int)(120 * $s); $h = (int)(80 * $s);
        $x = $cx - $w / 2; $y = $cy - $h / 2;

        imagefilledrectangle($img, $x, $y, $x + $w, $y + $h, $light);
        imagerectangle($img, $x, $y, $x + $w, $y + $h, $color);

        // Fan grill
        $fanR = (int)(30 * $s);
        imageellipse($img, $cx, $cy, $fanR * 2, $fanR * 2, $color);
        // Grill pattern
        for ($i = 0; $i < 5; $i++) {
            $r = $fanR - $i * (int)(6 * $s);
            if ($r > 0) imageellipse($img, $cx, $cy, $r * 2, $r * 2, $color);
        }

        // Label area
        imagefilledrectangle($img, $x + (int)(5 * $s), $y + $h - (int)(18 * $s), $x + (int)(60 * $s), $y + $h - (int)(5 * $s), $color);

        // Power switch
        imagefilledrectangle($img, $x + $w - (int)(25 * $s), $y + (int)(5 * $s), $x + $w - (int)(5 * $s), $y + (int)(20 * $s), $color);
    }

    private function drawCooler(\GdImage $img, int $cx, int $cy, float $s, int $color, int $light): void
    {
        // Heatsink fins
        $w = (int)(90 * $s); $h = (int)(100 * $s);
        $x = $cx - $w / 2; $y = $cy - $h / 2;

        for ($i = 0; $i < 10; $i++) {
            $fx = $x + $i * (int)(9 * $s);
            imagefilledrectangle($img, $fx, $y, $fx + (int)(6 * $s), $y + $h, $light);
            imagerectangle($img, $fx, $y, $fx + (int)(6 * $s), $y + $h, $color);
        }

        // Fan overlay
        $fanR = (int)(40 * $s);
        imagefilledellipse($img, $cx, $cy, $fanR * 2, $fanR * 2, $light);
        imageellipse($img, $cx, $cy, $fanR * 2, $fanR * 2, $color);
        for ($a = 0; $a < 360; $a += 45) {
            $rad = deg2rad($a);
            imageline($img, $cx, $cy, $cx + (int)(cos($rad) * $fanR * 0.85), $cy + (int)(sin($rad) * $fanR * 0.85), $color);
        }
        imageellipse($img, $cx, $cy, (int)(15 * $s), (int)(15 * $s), $color);
    }

    private function drawMonitor(\GdImage $img, int $cx, int $cy, float $s, int $color, int $light, bool $alt): void
    {
        $sw = (int)(150 * $s); $sh = (int)(90 * $s);
        $screenX = $cx - $sw / 2;
        $screenY = $cy - $sh / 2 - (int)(10 * $s);

        // Bezel
        imagefilledrectangle($img, $screenX, $screenY, $screenX + $sw, $screenY + $sh, $color);

        // Screen area
        $bez = (int)(5 * $s);
        $screenColor = imagecolorallocatealpha($img, 200, 220, 255, 60);
        imagefilledrectangle($img, $screenX + $bez, $screenY + $bez, $screenX + $sw - $bez, $screenY + $sh - $bez - (int)(8 * $s), $screenColor);

        // Stand neck
        $nw = (int)(15 * $s); $nh = (int)(25 * $s);
        imagefilledrectangle($img, $cx - $nw / 2, $screenY + $sh, $cx + $nw / 2, $screenY + $sh + $nh, $color);

        // Stand base
        $bw = (int)(60 * $s);
        imagefilledellipse($img, $cx, $screenY + $sh + $nh + (int)(5 * $s), $bw, (int)(12 * $s), $color);
    }

    private function drawPrinter(\GdImage $img, int $cx, int $cy, float $s, int $color, int $light): void
    {
        $w = (int)(130 * $s); $h = (int)(70 * $s);
        $x = $cx - $w / 2; $y = $cy - $h / 2;

        // Body
        imagefilledrectangle($img, $x, $y, $x + $w, $y + $h, $light);
        imagerectangle($img, $x, $y, $x + $w, $y + $h, $color);

        // Paper input tray
        $tw = (int)(100 * $s);
        imagefilledrectangle($img, $cx - $tw / 2, $y - (int)(20 * $s), $cx + $tw / 2, $y, $light);
        imagerectangle($img, $cx - $tw / 2, $y - (int)(20 * $s), $cx + $tw / 2, $y, $color);

        // Paper coming out
        $pw = (int)(90 * $s);
        imagefilledrectangle($img, $cx - $pw / 2, $y + $h, $cx + $pw / 2, $y + $h + (int)(25 * $s), imagecolorallocate($img, 255, 255, 255));
        imagerectangle($img, $cx - $pw / 2, $y + $h, $cx + $pw / 2, $y + $h + (int)(25 * $s), $color);
        // Lines on paper
        for ($i = 0; $i < 3; $i++) {
            $ly = $y + $h + (int)(6 * $s) + $i * (int)(6 * $s);
            imageline($img, $cx - $pw / 2 + (int)(10 * $s), $ly, $cx + $pw / 2 - (int)(10 * $s), $ly, $color);
        }

        // Control panel
        imagefilledrectangle($img, $x + (int)(10 * $s), $y + (int)(10 * $s), $x + (int)(50 * $s), $y + (int)(25 * $s), $color);

        // Buttons
        imagefilledellipse($img, $x + $w - (int)(20 * $s), $y + (int)(18 * $s), (int)(10 * $s), (int)(10 * $s), $color);
    }

    private function drawRouter(\GdImage $img, int $cx, int $cy, float $s, int $color, int $light): void
    {
        $w = (int)(130 * $s); $h = (int)(30 * $s);
        $x = $cx - $w / 2; $y = $cy;

        // Body
        imagefilledrectangle($img, $x, $y, $x + $w, $y + $h, $light);
        imagerectangle($img, $x, $y, $x + $w, $y + $h, $color);

        // LEDs
        for ($i = 0; $i < 5; $i++) {
            $lx = $x + (int)(15 * $s) + $i * (int)(12 * $s);
            imagefilledellipse($img, $lx, $y + $h / 2, (int)(5 * $s), (int)(5 * $s), $color);
        }

        // Antennas
        $antennaH = (int)(80 * $s);
        $positions = [$cx - (int)(40 * $s), $cx, $cx + (int)(40 * $s)];
        foreach ($positions as $i => $ax) {
            $tilt = ($i - 1) * (int)(10 * $s);
            imagesetthickness($img, max(1, (int)(3 * $s)));
            imageline($img, $ax, $y, $ax + $tilt, $y - $antennaH, $color);
            imagefilledellipse($img, $ax + $tilt, $y - $antennaH, (int)(8 * $s), (int)(8 * $s), $color);
            imagesetthickness($img, max(1, (int)(2 * $s)));
        }
    }

    private function drawStorage(\GdImage $img, string $subCat, int $cx, int $cy, float $s, int $color, int $light): void
    {
        $sub = strtolower($subCat);
        if (str_contains($sub, 'ssd')) {
            // SSD - slim rectangle
            $w = (int)(100 * $s); $h = (int)(60 * $s);
            $x = $cx - $w / 2; $y = $cy - $h / 2;
            imagefilledrectangle($img, $x, $y, $x + $w, $y + $h, $light);
            imagerectangle($img, $x, $y, $x + $w, $y + $h, $color);
            // Label
            imagerectangle($img, $x + (int)(10 * $s), $y + (int)(10 * $s), $x + (int)(60 * $s), $y + (int)(30 * $s), $color);
            // Connector
            imagefilledrectangle($img, $x + $w - (int)(30 * $s), $y + $h - (int)(8 * $s), $x + $w - (int)(5 * $s), $y + $h, $color);
        } elseif (str_contains($sub, 'pen') || str_contains($sub, 'flash') || str_contains($sub, 'usb')) {
            // USB drive
            $w = (int)(80 * $s); $h = (int)(30 * $s);
            $x = $cx - $w / 2; $y = $cy - $h / 2;
            imagefilledrectangle($img, $x, $y, $x + $w, $y + $h, $light);
            imagerectangle($img, $x, $y, $x + $w, $y + $h, $color);
            // USB connector
            $uw = (int)(15 * $s);
            imagefilledrectangle($img, $x + $w, $y + (int)(5 * $s), $x + $w + $uw, $y + $h - (int)(5 * $s), $color);
            // LED
            imagefilledellipse($img, $x + (int)(15 * $s), $cy, (int)(6 * $s), (int)(6 * $s), $color);
        } else {
            // HDD - thicker rectangle
            $w = (int)(100 * $s); $h = (int)(75 * $s);
            $x = $cx - $w / 2; $y = $cy - $h / 2;
            imagefilledrectangle($img, $x, $y, $x + $w, $y + $h, $light);
            imagerectangle($img, $x, $y, $x + $w, $y + $h, $color);
            // Platter circle
            imageellipse($img, $cx, $cy, (int)(50 * $s), (int)(50 * $s), $color);
            imageellipse($img, $cx, $cy, (int)(15 * $s), (int)(15 * $s), $color);
            // Label
            imagefilledrectangle($img, $x + (int)(5 * $s), $y + (int)(5 * $s), $x + (int)(40 * $s), $y + (int)(15 * $s), $color);
        }
    }

    private function drawAccessory(\GdImage $img, string $subCat, int $cx, int $cy, float $s, int $color, int $light): void
    {
        $sub = strtolower($subCat);
        if (str_contains($sub, 'keyboard')) {
            $this->drawKeyboard($img, $cx, $cy, $s, $color, $light);
        } elseif (str_contains($sub, 'mouse')) {
            $this->drawMouse($img, $cx, $cy, $s, $color, $light);
        } elseif (str_contains($sub, 'headphone') || str_contains($sub, 'headset')) {
            $this->drawHeadphone($img, $cx, $cy, $s, $color, $light);
        } elseif (str_contains($sub, 'webcam')) {
            $this->drawWebcam($img, $cx, $cy, $s, $color, $light);
        } elseif (str_contains($sub, 'speaker')) {
            $this->drawSpeaker($img, $cx, $cy, $s, $color, $light);
        } elseif (str_contains($sub, 'ups')) {
            $this->drawPSU($img, $cx, $cy, $s, $color, $light);
        } else {
            $this->drawGenericProduct($img, $cx, $cy, $s, $color, $light);
        }
    }

    private function drawKeyboard(\GdImage $img, int $cx, int $cy, float $s, int $color, int $light): void
    {
        $w = (int)(160 * $s); $h = (int)(55 * $s);
        $x = $cx - $w / 2; $y = $cy - $h / 2;

        imagefilledrectangle($img, $x, $y, $x + $w, $y + $h, $light);
        imagerectangle($img, $x, $y, $x + $w, $y + $h, $color);

        // Key rows
        $keySize = (int)(8 * $s); $gap = (int)(2 * $s);
        for ($row = 0; $row < 4; $row++) {
            $cols = $row === 3 ? 10 : 13;
            $startX = $x + (int)(8 * $s) + $row * (int)(4 * $s);
            for ($col = 0; $col < $cols; $col++) {
                $kw = ($row === 3 && $col === 4) ? $keySize * 3 : $keySize;
                $kx = $startX + $col * ($keySize + $gap);
                $ky = $y + (int)(6 * $s) + $row * ($keySize + $gap);
                if ($kx + $kw <= $x + $w - (int)(5 * $s)) {
                    imagefilledrectangle($img, $kx, $ky, $kx + $kw, $ky + $keySize, $color);
                }
            }
        }
    }

    private function drawMouse(\GdImage $img, int $cx, int $cy, float $s, int $color, int $light): void
    {
        $w = (int)(50 * $s); $h = (int)(80 * $s);

        // Body
        imagefilledellipse($img, $cx, $cy, $w, $h, $light);
        imageellipse($img, $cx, $cy, $w, $h, $color);

        // Button divider
        imageline($img, $cx, $cy - (int)($h * 0.45), $cx, $cy - (int)(5 * $s), $color);

        // Scroll wheel
        imagefilledellipse($img, $cx, $cy - (int)(15 * $s), (int)(8 * $s), (int)(14 * $s), $color);
    }

    private function drawHeadphone(\GdImage $img, int $cx, int $cy, float $s, int $color, int $light): void
    {
        imagesetthickness($img, max(1, (int)(4 * $s)));

        // Headband
        imagearc($img, $cx, $cy - (int)(10 * $s), (int)(100 * $s), (int)(80 * $s), 180, 360, $color);

        imagesetthickness($img, max(1, (int)(2 * $s)));

        // Left ear cup
        imagefilledellipse($img, $cx - (int)(50 * $s), $cy + (int)(20 * $s), (int)(35 * $s), (int)(45 * $s), $light);
        imageellipse($img, $cx - (int)(50 * $s), $cy + (int)(20 * $s), (int)(35 * $s), (int)(45 * $s), $color);

        // Right ear cup
        imagefilledellipse($img, $cx + (int)(50 * $s), $cy + (int)(20 * $s), (int)(35 * $s), (int)(45 * $s), $light);
        imageellipse($img, $cx + (int)(50 * $s), $cy + (int)(20 * $s), (int)(35 * $s), (int)(45 * $s), $color);
    }

    private function drawWebcam(\GdImage $img, int $cx, int $cy, float $s, int $color, int $light): void
    {
        // Camera body (circle)
        $r = (int)(40 * $s);
        imagefilledellipse($img, $cx, $cy, $r * 2, $r * 2, $light);
        imageellipse($img, $cx, $cy, $r * 2, $r * 2, $color);

        // Lens
        imageellipse($img, $cx, $cy, (int)(50 * $s), (int)(50 * $s), $color);
        imagefilledellipse($img, $cx, $cy, (int)(20 * $s), (int)(20 * $s), $color);

        // Clip/mount
        imagefilledrectangle($img, $cx - (int)(15 * $s), $cy + $r, $cx + (int)(15 * $s), $cy + $r + (int)(20 * $s), $color);
    }

    private function drawSpeaker(\GdImage $img, int $cx, int $cy, float $s, int $color, int $light): void
    {
        $w = (int)(60 * $s); $h = (int)(100 * $s);
        $x = $cx - $w / 2; $y = $cy - $h / 2;

        imagefilledrectangle($img, $x, $y, $x + $w, $y + $h, $light);
        imagerectangle($img, $x, $y, $x + $w, $y + $h, $color);

        // Tweeter
        imageellipse($img, $cx, $y + (int)(25 * $s), (int)(25 * $s), (int)(25 * $s), $color);
        imagefilledellipse($img, $cx, $y + (int)(25 * $s), (int)(10 * $s), (int)(10 * $s), $color);

        // Woofer
        imageellipse($img, $cx, $y + (int)(65 * $s), (int)(40 * $s), (int)(40 * $s), $color);
        imageellipse($img, $cx, $y + (int)(65 * $s), (int)(20 * $s), (int)(20 * $s), $color);
        imagefilledellipse($img, $cx, $y + (int)(65 * $s), (int)(8 * $s), (int)(8 * $s), $color);
    }

    private function drawGaming(\GdImage $img, string $subCat, int $cx, int $cy, float $s, int $color, int $light): void
    {
        $sub = strtolower($subCat);
        if (str_contains($sub, 'chair')) {
            $this->drawGamingChair($img, $cx, $cy, $s, $color, $light);
        } elseif (str_contains($sub, 'keyboard')) {
            $this->drawKeyboard($img, $cx, $cy, $s, $color, $light);
        } elseif (str_contains($sub, 'mouse')) {
            $this->drawMouse($img, $cx, $cy, $s, $color, $light);
        } elseif (str_contains($sub, 'headset') || str_contains($sub, 'headphone')) {
            $this->drawHeadphone($img, $cx, $cy, $s, $color, $light);
        } else {
            $this->drawGamepad($img, $cx, $cy, $s, $color, $light);
        }
    }

    private function drawGamingChair(\GdImage $img, int $cx, int $cy, float $s, int $color, int $light): void
    {
        // Backrest
        $bw = (int)(60 * $s); $bh = (int)(90 * $s);
        $bx = $cx - $bw / 2; $by = $cy - (int)(50 * $s);
        imagefilledrectangle($img, $bx, $by, $bx + $bw, $by + $bh, $light);
        imagerectangle($img, $bx, $by, $bx + $bw, $by + $bh, $color);

        // Side wings
        imageline($img, $bx, $by + (int)(10 * $s), $bx - (int)(8 * $s), $by + (int)(30 * $s), $color);
        imageline($img, $bx + $bw, $by + (int)(10 * $s), $bx + $bw + (int)(8 * $s), $by + (int)(30 * $s), $color);

        // Headrest pillow
        imagefilledrectangle($img, $cx - (int)(20 * $s), $by + (int)(8 * $s), $cx + (int)(20 * $s), $by + (int)(18 * $s), $color);

        // Seat
        $sy = $by + $bh;
        imagefilledrectangle($img, $bx - (int)(5 * $s), $sy, $bx + $bw + (int)(5 * $s), $sy + (int)(15 * $s), $light);
        imagerectangle($img, $bx - (int)(5 * $s), $sy, $bx + $bw + (int)(5 * $s), $sy + (int)(15 * $s), $color);

        // Base stem
        imageline($img, $cx, $sy + (int)(15 * $s), $cx, $sy + (int)(35 * $s), $color);

        // Star base
        $baseY = $sy + (int)(35 * $s);
        imageline($img, $cx - (int)(35 * $s), $baseY + (int)(5 * $s), $cx + (int)(35 * $s), $baseY + (int)(5 * $s), $color);
        imageline($img, $cx - (int)(25 * $s), $baseY, $cx + (int)(25 * $s), $baseY + (int)(10 * $s), $color);
        imageline($img, $cx + (int)(25 * $s), $baseY, $cx - (int)(25 * $s), $baseY + (int)(10 * $s), $color);

        // Wheels
        imagefilledellipse($img, $cx - (int)(35 * $s), $baseY + (int)(8 * $s), (int)(8 * $s), (int)(8 * $s), $color);
        imagefilledellipse($img, $cx + (int)(35 * $s), $baseY + (int)(8 * $s), (int)(8 * $s), (int)(8 * $s), $color);
    }

    private function drawGamepad(\GdImage $img, int $cx, int $cy, float $s, int $color, int $light): void
    {
        $w = (int)(120 * $s); $h = (int)(70 * $s);

        imagefilledellipse($img, $cx, $cy, $w, $h, $light);
        imageellipse($img, $cx, $cy, $w, $h, $color);

        // D-pad
        $dx = $cx - (int)(25 * $s);
        $ds = (int)(6 * $s);
        imagefilledrectangle($img, $dx - $ds, $cy - $ds * 3, $dx + $ds, $cy + $ds * 3, $color);
        imagefilledrectangle($img, $dx - $ds * 3, $cy - $ds, $dx + $ds * 3, $cy + $ds, $color);

        // Buttons (ABXY)
        $bx = $cx + (int)(25 * $s);
        $bs = (int)(6 * $s);
        imagefilledellipse($img, $bx, $cy - $bs * 2, $bs * 2, $bs * 2, $color);
        imagefilledellipse($img, $bx, $cy + $bs * 2, $bs * 2, $bs * 2, $color);
        imagefilledellipse($img, $bx - $bs * 2, $cy, $bs * 2, $bs * 2, $color);
        imagefilledellipse($img, $bx + $bs * 2, $cy, $bs * 2, $bs * 2, $color);

        // Analog sticks
        imageellipse($img, $cx - (int)(10 * $s), $cy + (int)(15 * $s), (int)(15 * $s), (int)(15 * $s), $color);
        imageellipse($img, $cx + (int)(10 * $s), $cy + (int)(15 * $s), (int)(15 * $s), (int)(15 * $s), $color);
    }

    private function drawCamera(\GdImage $img, int $cx, int $cy, float $s, int $color, int $light): void
    {
        $w = (int)(120 * $s); $h = (int)(80 * $s);
        $x = $cx - $w / 2; $y = $cy - $h / 2;

        // Body
        imagefilledrectangle($img, $x, $y, $x + $w, $y + $h, $light);
        imagerectangle($img, $x, $y, $x + $w, $y + $h, $color);

        // Viewfinder hump
        imagefilledrectangle($img, $cx - (int)(15 * $s), $y - (int)(15 * $s), $cx + (int)(15 * $s), $y, $light);
        imagerectangle($img, $cx - (int)(15 * $s), $y - (int)(15 * $s), $cx + (int)(15 * $s), $y, $color);

        // Lens
        $lensR = (int)(30 * $s);
        imageellipse($img, $cx, $cy + (int)(5 * $s), $lensR * 2, $lensR * 2, $color);
        imageellipse($img, $cx, $cy + (int)(5 * $s), (int)($lensR * 1.3), (int)($lensR * 1.3), $color);
        imagefilledellipse($img, $cx, $cy + (int)(5 * $s), (int)($lensR * 0.5), (int)($lensR * 0.5), $color);

        // Flash
        imagefilledrectangle($img, $x + (int)(8 * $s), $y + (int)(8 * $s), $x + (int)(22 * $s), $y + (int)(16 * $s), $color);

        // Mode dial
        imageellipse($img, $x + $w - (int)(15 * $s), $y + (int)(12 * $s), (int)(14 * $s), (int)(14 * $s), $color);
    }

    private function drawGenericProduct(\GdImage $img, int $cx, int $cy, float $s, int $color, int $light): void
    {
        $size = (int)(60 * $s);
        imagefilledrectangle($img, $cx - $size, $cy - $size, $cx + $size, $cy + $size, $light);
        imagerectangle($img, $cx - $size, $cy - $size, $cx + $size, $cy + $size, $color);
        imageline($img, $cx - $size, $cy - $size, $cx + $size, $cy + $size, $color);
        imageline($img, $cx + $size, $cy - $size, $cx - $size, $cy + $size, $color);
    }

    // -------------------------------------------------------
    // Brand Logo Generator
    // -------------------------------------------------------
    private function generateBrandLogo(string $name, array $brandColor, int $w, int $h, string $tempDir): string
    {
        $img = imagecreatetruecolor($w, $h);
        imagesavealpha($img, true);
        imagealphablending($img, true);

        // White background
        $white = imagecolorallocate($img, 255, 255, 255);
        imagefill($img, 0, 0, $white);

        // Bottom accent bar
        $accentColor = imagecolorallocate($img, $brandColor[0], $brandColor[1], $brandColor[2]);
        imagefilledrectangle($img, 0, $h - 4, $w, $h, $accentColor);

        // Brand name in center
        $fontSize = 16;
        $bbox = imagettfbbox($fontSize, 0, $this->fontBold, $name);
        $textW = $bbox[2] - $bbox[0];
        while ($textW > $w - 40 && $fontSize > 10) {
            $fontSize--;
            $bbox = imagettfbbox($fontSize, 0, $this->fontBold, $name);
            $textW = $bbox[2] - $bbox[0];
        }
        $textX = (int)(($w - $textW) / 2);
        $textY = (int)($h / 2) + (int)(($bbox[1] - $bbox[7]) / 2);

        imagettftext($img, $fontSize, 0, $textX, $textY, $accentColor, $this->fontBold, $name);

        // Subtle underline
        $underY = $textY + 8;
        $ulW = min($textW + 20, $w - 60);
        imageline($img, (int)(($w - $ulW) / 2), $underY, (int)(($w + $ulW) / 2), $underY, imagecolorallocatealpha($img, $brandColor[0], $brandColor[1], $brandColor[2], 80));

        $filename = $tempDir . '/' . uniqid('brand_') . '.png';
        imagepng($img, $filename, 6);
        imagedestroy($img);

        return $filename;
    }

    // -------------------------------------------------------
    // Category Image Generator
    // -------------------------------------------------------
    private function generateCategoryImage(string $name, array $theme, int $w, int $h, string $tempDir): string
    {
        $img = imagecreatetruecolor($w, $h);
        imagesavealpha($img, true);
        imagealphablending($img, true);

        $accent = $theme['accent'];

        // Gradient background using accent color
        $this->fillGradientH($img, $w, $h, $accent, [
            min(255, $accent[0] + 60),
            min(255, $accent[1] + 60),
            min(255, $accent[2] + 60),
        ]);

        // Overlay pattern - large translucent circles
        $circleColor = imagecolorallocatealpha($img, 255, 255, 255, 110);
        imagefilledellipse($img, (int)($w * 0.75), (int)($h * 0.3), (int)($w * 0.5), (int)($w * 0.5), $circleColor);
        imagefilledellipse($img, (int)($w * 0.2), (int)($h * 0.8), (int)($w * 0.3), (int)($w * 0.3), $circleColor);

        // Category icon on left
        $iconColor = imagecolorallocatealpha($img, 255, 255, 255, 30);
        $iconLight = imagecolorallocatealpha($img, 255, 255, 255, 70);
        $iconCx = (int)($w * 0.25);
        $iconCy = (int)($h * 0.5);
        $iconScale = ($h / 600.0) * 1.2;
        $this->drawProductIcon($img, $name, $name, $iconCx, $iconCy, $iconScale, $iconColor, $iconLight, false);

        // Category name (right side)
        $white = [255, 255, 255];
        $titleSize = 24;
        $this->drawText($img, $name, $this->fontBold, $titleSize, (int)($w * 0.55), (int)($h * 0.42), $white);

        // Subtitle
        $subSize = 12;
        $this->drawText($img, 'Browse Collection', $this->fontLight, $subSize, (int)($w * 0.55), (int)($h * 0.58), [255, 255, 255]);

        // Decorative line
        $lineColor = imagecolorallocatealpha($img, 255, 255, 255, 40);
        imagesetthickness($img, 2);
        imageline($img, (int)($w * 0.55), (int)($h * 0.48), (int)($w * 0.55) + 60, (int)($h * 0.48), $lineColor);
        imagesetthickness($img, 1);

        $filename = $tempDir . '/' . uniqid('category_') . '.png';
        imagepng($img, $filename, 6);
        imagedestroy($img);

        return $filename;
    }

    // -------------------------------------------------------
    // Utility Methods
    // -------------------------------------------------------
    private function fillGradientV(\GdImage $img, int $w, int $h, array $from, array $to): void
    {
        for ($y = 0; $y < $h; $y++) {
            $r = (int)($from[0] + ($to[0] - $from[0]) * ($y / $h));
            $g = (int)($from[1] + ($to[1] - $from[1]) * ($y / $h));
            $b = (int)($from[2] + ($to[2] - $from[2]) * ($y / $h));
            $lineColor = imagecolorallocate($img, $r, $g, $b);
            imageline($img, 0, $y, $w, $y, $lineColor);
        }
    }

    private function fillGradientH(\GdImage $img, int $w, int $h, array $from, array $to): void
    {
        for ($x = 0; $x < $w; $x++) {
            $r = (int)($from[0] + ($to[0] - $from[0]) * ($x / $w));
            $g = (int)($from[1] + ($to[1] - $from[1]) * ($x / $w));
            $b = (int)($from[2] + ($to[2] - $from[2]) * ($x / $w));
            $lineColor = imagecolorallocate($img, $r, $g, $b);
            imageline($img, $x, 0, $x, $h, $lineColor);
        }
    }

    private function drawCenteredText(\GdImage $img, string $text, string $font, float $size, int $cx, int $y, array $rgb): void
    {
        $color = imagecolorallocate($img, $rgb[0], $rgb[1], $rgb[2]);
        $bbox = imagettfbbox($size, 0, $font, $text);
        $textW = $bbox[2] - $bbox[0];
        $x = $cx - (int)($textW / 2);
        imagettftext($img, $size, 0, $x, $y, $color, $font, $text);
    }

    private function drawText(\GdImage $img, string $text, string $font, float $size, int $x, int $y, array $rgb): void
    {
        $color = imagecolorallocate($img, $rgb[0], $rgb[1], $rgb[2]);
        imagettftext($img, $size, 0, $x, $y, $color, $font, $text);
    }

    private function wrapTextTTF(string $text, string $font, float $size, int $maxWidth): array
    {
        $words = explode(' ', $text);
        $lines = [];
        $currentLine = '';

        foreach ($words as $word) {
            $testLine = $currentLine ? "{$currentLine} {$word}" : $word;
            $bbox = imagettfbbox($size, 0, $font, $testLine);
            $testWidth = $bbox[2] - $bbox[0];

            if ($testWidth > $maxWidth && $currentLine !== '') {
                $lines[] = $currentLine;
                $currentLine = $word;
            } else {
                $currentLine = $testLine;
            }
        }
        if ($currentLine !== '') {
            $lines[] = $currentLine;
        }

        return $lines;
    }

    private function getParentCategoryName(Product $product): string
    {
        $category = $product->categories->first();
        if (!$category) return 'default';

        while ($category->parent_id) {
            $category = Category::find($category->parent_id);
            if (!$category) break;
        }
        return $category?->name ?? 'default';
    }

    private function getSubcategoryName(Product $product): string
    {
        $category = $product->categories->first();
        return $category?->name ?? 'default';
    }

    private function cleanupTemp(string $dir): void
    {
        if (is_dir($dir)) {
            $files = glob($dir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) @unlink($file);
            }
            @rmdir($dir);
        }
    }

    private function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) return;
        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            is_dir($path) ? $this->deleteDirectory($path) : @unlink($path);
        }
        @rmdir($dir);
    }
}
