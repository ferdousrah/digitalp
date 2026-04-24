<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;

class PaymentSettings extends Page
{

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && $user->can('payment.view');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }
    protected static ?string $navigationIcon  = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Payment Settings';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int    $navigationSort  = 5;
    protected static string  $view            = 'filament.pages.payment-settings';

    public array $data = [];

    public function mount(): void
    {
        $this->data = [
            'bkash_sandbox'    => config('bkash.sandbox') ? '1' : '0',
            'bkash_app_key'    => config('bkash.app_key'),
            'bkash_app_secret' => config('bkash.app_secret'),
            'bkash_username'   => config('bkash.username'),
            'bkash_password'   => config('bkash.password'),
            'ssl_sandbox'      => config('sslcommerz.sandbox') ? '1' : '0',
            'ssl_store_id'     => config('sslcommerz.store_id'),
            'ssl_store_passwd' => config('sslcommerz.store_passwd'),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('bKash Payment Gateway')
                    ->description('Configure bKash Tokenized Checkout API credentials.')
                    ->icon('heroicon-o-device-phone-mobile')
                    ->schema([
                        Forms\Components\Toggle::make('bkash_sandbox')
                            ->label('Sandbox Mode')
                            ->helperText('Enable for testing. Disable for live payments.')
                            ->default(true),

                        Forms\Components\TextInput::make('bkash_app_key')
                            ->label('App Key')
                            ->required()
                            ->placeholder('Your bKash App Key'),

                        Forms\Components\TextInput::make('bkash_app_secret')
                            ->label('App Secret')
                            ->password()
                            ->revealable()
                            ->required()
                            ->placeholder('Your bKash App Secret'),

                        Forms\Components\TextInput::make('bkash_username')
                            ->label('Username')
                            ->required()
                            ->placeholder('Your bKash Merchant Username'),

                        Forms\Components\TextInput::make('bkash_password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->required()
                            ->placeholder('Your bKash Merchant Password'),
                    ])->columns(2),

                Forms\Components\Section::make('SSLCommerz Payment Gateway')
                    ->description('Configure SSLCommerz Online Payment credentials.')
                    ->icon('heroicon-o-credit-card')
                    ->schema([
                        Forms\Components\Toggle::make('ssl_sandbox')
                            ->label('Sandbox Mode')
                            ->helperText('Enable for testing. Disable for live payments.')
                            ->default(true),

                        Forms\Components\TextInput::make('ssl_store_id')
                            ->label('Store ID')
                            ->required()
                            ->placeholder('Your SSLCommerz Store ID'),

                        Forms\Components\TextInput::make('ssl_store_passwd')
                            ->label('Store Password')
                            ->password()
                            ->revealable()
                            ->required()
                            ->placeholder('Your SSLCommerz Store Password'),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $envPath = base_path('.env');
        $env     = file_get_contents($envPath);

        $replacements = [
            'BKASH_SANDBOX'             => $data['bkash_sandbox'] ? 'true' : 'false',
            'BKASH_APP_KEY'             => $data['bkash_app_key'],
            'BKASH_APP_SECRET'          => $data['bkash_app_secret'],
            'BKASH_USERNAME'            => $data['bkash_username'],
            'BKASH_PASSWORD'            => $data['bkash_password'],
            'SSLCOMMERZ_SANDBOX'        => $data['ssl_sandbox'] ? 'true' : 'false',
            'SSLCOMMERZ_STORE_ID'       => $data['ssl_store_id'],
            'SSLCOMMERZ_STORE_PASSWORD' => $data['ssl_store_passwd'],
        ];

        foreach ($replacements as $key => $value) {
            if (preg_match("/^{$key}=/m", $env)) {
                $env = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $env);
            } else {
                $env .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envPath, $env);

        // Clear config cache
        Artisan::call('config:clear');
        // Clear bKash token cache
        cache()->forget('bkash_access_token');

        Notification::make()
            ->title('Payment settings saved')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('save')
                ->label('Save Settings')
                ->action('save'),
        ];
    }
}
