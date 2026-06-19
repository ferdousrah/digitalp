<?php

namespace App\Filament\Pages;

use App\Services\SettingService;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Str;

class ProductCardSettings extends Page implements HasForms
{
    use InteractsWithForms;

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && $user->can('product_card.view');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }
    protected static ?string $navigationIcon  = 'heroicon-o-squares-plus';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Product Card';
    protected static ?int    $navigationSort  = 8;
    protected static string  $view            = 'filament.pages.product-card-settings';

    public array $data = [];

    /** Devices the card can be tuned for (key => label). */
    public const DEVICES = ['desktop' => 'Desktop', 'tablet' => 'Tablet', 'mobile' => 'Mobile'];

    /** Per-device fields and their [desktop, tablet, mobile] default values. */
    protected static function perDeviceDefaults(): array
    {
        return [
            'pc_columns'             => ['4', '3', '2'],
            'pc_border_radius'       => ['16', '16', '12'],
            'pc_image_ratio'         => ['1/1', '1/1', '1/1'],
            'pc_hover_lift'          => ['6', '4', '0'],
            'pc_btn_reveal_speed'    => ['0.6', '0.6', '0.4'],
            'pc_btn_radius'          => ['8', '8', '8'],
            'pc_show_brand'          => ['1', '1', '1'],
            'pc_show_compare_price'  => ['1', '1', '1'],
            'pc_show_sale_badge'     => ['1', '1', '1'],
            'pc_show_featured_badge' => ['1', '1', '0'],
            'pc_image_zoom'          => ['1', '1', '0'],
            'pc_show_wishlist_btn'   => ['1', '1', '1'],
            'pc_show_compare_btn'    => ['1', '1', '0'],
            'pc_show_quickview_btn'  => ['1', '1', '0'],
            'pc_show_cart_btn'       => ['1', '1', '1'],
            'pc_show_order_btn'      => ['1', '1', '1'],
        ];
    }

    public static function defaults(): array
    {
        $out = [];
        $devices = array_keys(self::DEVICES);
        foreach (self::perDeviceDefaults() as $key => $values) {
            foreach ($devices as $i => $dev) {
                $out["{$key}_{$dev}"] = $values[$i];
            }
        }

        // Global colors (shared across all devices)
        return array_merge($out, [
            'pc_card_bg'             => '#ffffff',
            'pc_card_border'         => '#e5e7eb',
            'pc_card_hover_border'   => '#16a34a',
            'pc_card_hover_shadow'   => 'rgba(0,0,0,0.12)',
            'pc_name_color'          => '#111827',
            'pc_name_hover_color'    => '#16a34a',
            'pc_brand_color'         => '#16a34a',
            'pc_price_color'         => '#16a34a',
            'pc_compare_price_color' => '#9ca3af',
            'pc_sale_badge_bg'       => '#ef4444',
            'pc_sale_badge_text'     => '#ffffff',
            'pc_featured_badge_bg'   => '#16a34a',
            'pc_featured_badge_text' => '#ffffff',
            'pc_oos_bg'              => 'rgba(0,0,0,0.6)',
            'pc_oos_text'            => '#ffffff',
        ]);
    }

    /** A key is a boolean toggle if it controls visibility / zoom. */
    protected static function isToggleKey(string $key): bool
    {
        return Str::contains($key, ['_show_', 'image_zoom']);
    }

    public function mount(): void
    {
        $filled = [];
        foreach (self::defaults() as $key => $default) {
            $value = SettingService::get($key, $default);
            $filled[$key] = self::isToggleKey($key)
                ? in_array($value, ['1', 1, true, 'true'], true)
                : $value;
        }
        $this->form->fill($filled);
    }

    /** Layout + visibility controls for one device (keys suffixed with _$dev). */
    protected static function deviceSchema(string $dev): array
    {
        return [
            Section::make('Layout')
                ->columns(3)
                ->schema([
                    Select::make("pc_columns_{$dev}")
                        ->label('Products per row')
                        ->options([1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6']),

                    Select::make("pc_image_ratio_{$dev}")
                        ->label('Image aspect ratio')
                        ->options([
                            '1/1'  => 'Square (1:1)',
                            '4/3'  => 'Landscape (4:3)',
                            '3/4'  => 'Portrait (3:4)',
                            '16/9' => 'Wide (16:9)',
                        ]),

                    TextInput::make("pc_border_radius_{$dev}")
                        ->label('Border radius (px)')
                        ->numeric()->suffix('px')->minValue(0)->maxValue(40),

                    TextInput::make("pc_hover_lift_{$dev}")
                        ->label('Hover lift (px)')
                        ->numeric()->suffix('px')->minValue(0)->maxValue(20),

                    TextInput::make("pc_btn_reveal_speed_{$dev}")
                        ->label('Button reveal speed (s)')
                        ->numeric()->suffix('s')->minValue(0.1)->maxValue(2)->step(0.1),

                    TextInput::make("pc_btn_radius_{$dev}")
                        ->label('Button radius (px)')
                        ->numeric()->suffix('px')->minValue(0)->maxValue(30),
                ]),

            Section::make('Visibility')
                ->columns(2)
                ->schema([
                    Toggle::make("pc_show_brand_{$dev}")         ->label('Show brand name'),
                    Toggle::make("pc_show_compare_price_{$dev}") ->label('Show original / compare price'),
                    Toggle::make("pc_show_sale_badge_{$dev}")    ->label('Show sale % badge'),
                    Toggle::make("pc_show_featured_badge_{$dev}")->label('Show "Featured" badge'),
                    Toggle::make("pc_image_zoom_{$dev}")         ->label('Zoom image on hover'),
                    Toggle::make("pc_show_wishlist_btn_{$dev}")  ->label('Show Wishlist button'),
                    Toggle::make("pc_show_compare_btn_{$dev}")   ->label('Show Compare button'),
                    Toggle::make("pc_show_quickview_btn_{$dev}") ->label('Show Quick View button'),
                    Toggle::make("pc_show_cart_btn_{$dev}")      ->label('Show "Add to Cart"'),
                    Toggle::make("pc_show_order_btn_{$dev}")     ->label('Show "Buy Now"'),
                ]),
        ];
    }

    public function form(Form $form): Form
    {
        return $form->schema([

            Tabs::make('Devices')
                ->columnSpanFull()
                ->tabs([
                    Tabs\Tab::make('Desktop')->icon('heroicon-o-computer-desktop')->schema(self::deviceSchema('desktop')),
                    Tabs\Tab::make('Tablet')->icon('heroicon-o-device-tablet')->schema(self::deviceSchema('tablet')),
                    Tabs\Tab::make('Mobile')->icon('heroicon-o-device-phone-mobile')->schema(self::deviceSchema('mobile')),
                ]),

            Section::make('Colors (shared across all devices)')
                ->collapsible()
                ->schema([
                    Section::make('Card')->columns(4)->schema([
                        ColorPicker::make('pc_card_bg')           ->label('Card Background'),
                        ColorPicker::make('pc_card_border')       ->label('Card Border'),
                        ColorPicker::make('pc_card_hover_border') ->label('Card Hover Border'),
                        ColorPicker::make('pc_card_hover_shadow') ->label('Hover Shadow Color'),
                    ]),
                    Section::make('Text')->columns(5)->schema([
                        ColorPicker::make('pc_name_color')          ->label('Product Name'),
                        ColorPicker::make('pc_name_hover_color')    ->label('Product Name Hover'),
                        ColorPicker::make('pc_brand_color')         ->label('Brand Name'),
                        ColorPicker::make('pc_price_color')         ->label('Price'),
                        ColorPicker::make('pc_compare_price_color') ->label('Original Price'),
                    ]),
                    Section::make('Badges')->columns(4)->schema([
                        ColorPicker::make('pc_sale_badge_bg')       ->label('Sale Badge Background'),
                        ColorPicker::make('pc_sale_badge_text')     ->label('Sale Badge Text'),
                        ColorPicker::make('pc_featured_badge_bg')   ->label('Featured Badge Background'),
                        ColorPicker::make('pc_featured_badge_text') ->label('Featured Badge Text'),
                    ]),
                    Section::make('Out of Stock')->columns(2)->schema([
                        ColorPicker::make('pc_oos_bg')   ->label('Overlay Background'),
                        ColorPicker::make('pc_oos_text') ->label('Text Color'),
                    ]),
                ]),

        ])->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            if (is_bool($value)) {
                $value = $value ? '1' : '0';
            }
            SettingService::set($key, (string) $value, 'product_card', 'text');
        }

        Notification::make()->title('Product card settings saved!')->success()->send();
    }
}
