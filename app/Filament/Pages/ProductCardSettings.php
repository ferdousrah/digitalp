<?php

namespace App\Filament\Pages;

use App\Services\SettingService;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ProductCardSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-squares-plus';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Product Card';
    protected static ?int    $navigationSort  = 8;
    protected static string  $view            = 'filament.pages.product-card-settings';

    public array $data = [];

    public static function defaults(): array
    {
        return [
            // Layout
            'pc_border_radius'       => '16',
            'pc_image_ratio'         => '1/1',
            'pc_hover_lift'          => '6',
            // Content visibility
            'pc_show_brand'          => '1',
            'pc_show_compare_price'  => '1',
            'pc_show_sale_badge'     => '1',
            'pc_show_featured_badge' => '1',
            // Quick action sidebar
            'pc_show_wishlist_btn'   => '1',
            'pc_show_compare_btn'    => '1',
            'pc_show_quickview_btn'  => '1',
            // Hover buttons
            'pc_show_cart_btn'       => '1',
            'pc_show_order_btn'      => '1',
            'pc_btn_reveal_speed'    => '0.6',
            'pc_btn_radius'          => '8',
            // Image hover zoom
            'pc_image_zoom'          => '1',
            // Card colors
            'pc_card_bg'             => '#ffffff',
            'pc_card_border'         => '#e5e7eb',
            'pc_card_hover_border'   => '#16a34a',
            'pc_card_hover_shadow'   => 'rgba(0,0,0,0.12)',
            // Text colors
            'pc_name_color'          => '#111827',
            'pc_name_hover_color'    => '#16a34a',
            'pc_brand_color'         => '#16a34a',
            'pc_price_color'         => '#16a34a',
            'pc_compare_price_color' => '#9ca3af',
            // Badge colors
            'pc_sale_badge_bg'       => '#ef4444',
            'pc_sale_badge_text'     => '#ffffff',
            'pc_featured_badge_bg'   => '#16a34a',
            'pc_featured_badge_text' => '#ffffff',
            // Out of stock
            'pc_oos_bg'              => 'rgba(0,0,0,0.6)',
            'pc_oos_text'            => '#ffffff',
        ];
    }

    public function mount(): void
    {
        $defaults = self::defaults();
        $filled = [];
        foreach ($defaults as $key => $default) {
            $filled[$key] = SettingService::get($key, $default);
        }
        $this->form->fill($filled);
    }

    public function form(Form $form): Form
    {
        return $form->schema([

            Section::make('Card Layout')
                ->columns(3)
                ->schema([
                    TextInput::make('pc_border_radius')
                        ->label('Card border radius (px)')
                        ->numeric()->suffix('px')->minValue(0)->maxValue(40),

                    Select::make('pc_image_ratio')
                        ->label('Image aspect ratio')
                        ->options([
                            '1/1'   => 'Square (1:1)',
                            '4/3'   => 'Landscape (4:3)',
                            '3/4'   => 'Portrait (3:4)',
                            '16/9'  => 'Wide (16:9)',
                        ]),

                    TextInput::make('pc_hover_lift')
                        ->label('Hover lift (px)')
                        ->numeric()->suffix('px')->minValue(0)->maxValue(20),
                ]),

            Section::make('Content Visibility')
                ->columns(2)
                ->schema([
                    Toggle::make('pc_show_brand')         ->label('Show brand name'),
                    Toggle::make('pc_show_compare_price') ->label('Show original / compare price'),
                    Toggle::make('pc_show_sale_badge')    ->label('Show sale % badge'),
                    Toggle::make('pc_show_featured_badge')->label('Show "Featured" badge'),
                    Toggle::make('pc_image_zoom')         ->label('Zoom image on hover'),
                ]),

            Section::make('Quick Action Buttons (hover sidebar)')
                ->description('Small icon buttons that slide in from the right when hovering the image.')
                ->columns(3)
                ->schema([
                    Toggle::make('pc_show_wishlist_btn')  ->label('Show Wishlist button'),
                    Toggle::make('pc_show_compare_btn')   ->label('Show Compare button'),
                    Toggle::make('pc_show_quickview_btn') ->label('Show Quick View button'),
                ]),

            Section::make('Action Buttons (hover reveal)')
                ->description('Add to Cart and Order Now buttons that appear below the product info on hover.')
                ->columns(4)
                ->schema([
                    Toggle::make('pc_show_cart_btn')  ->label('Show "Add to Cart"'),
                    Toggle::make('pc_show_order_btn') ->label('Show "Order Now"'),

                    TextInput::make('pc_btn_reveal_speed')
                        ->label('Reveal speed (seconds)')
                        ->numeric()->suffix('s')->minValue(0.1)->maxValue(2)->step(0.1),

                    TextInput::make('pc_btn_radius')
                        ->label('Button border radius (px)')
                        ->numeric()->suffix('px')->minValue(0)->maxValue(30),
                ]),

            Section::make('Card Colors')
                ->columns(4)
                ->schema([
                    ColorPicker::make('pc_card_bg')           ->label('Card Background'),
                    ColorPicker::make('pc_card_border')       ->label('Card Border'),
                    ColorPicker::make('pc_card_hover_border') ->label('Card Hover Border'),
                    ColorPicker::make('pc_card_hover_shadow') ->label('Hover Shadow Color'),
                ]),

            Section::make('Text Colors')
                ->columns(5)
                ->schema([
                    ColorPicker::make('pc_name_color')          ->label('Product Name'),
                    ColorPicker::make('pc_name_hover_color')    ->label('Product Name Hover'),
                    ColorPicker::make('pc_brand_color')         ->label('Brand Name'),
                    ColorPicker::make('pc_price_color')         ->label('Price'),
                    ColorPicker::make('pc_compare_price_color') ->label('Original Price'),
                ]),

            Section::make('Badge Colors')
                ->columns(4)
                ->schema([
                    ColorPicker::make('pc_sale_badge_bg')       ->label('Sale Badge Background'),
                    ColorPicker::make('pc_sale_badge_text')     ->label('Sale Badge Text'),
                    ColorPicker::make('pc_featured_badge_bg')   ->label('Featured Badge Background'),
                    ColorPicker::make('pc_featured_badge_text') ->label('Featured Badge Text'),
                ]),

            Section::make('Out of Stock')
                ->columns(2)
                ->schema([
                    ColorPicker::make('pc_oos_bg')   ->label('Out of Stock Overlay Background'),
                    ColorPicker::make('pc_oos_text') ->label('Out of Stock Text Color'),
                ]),

        ])->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            // Cast booleans from toggle
            if (is_bool($value)) {
                $value = $value ? '1' : '0';
            }
            SettingService::set($key, (string) $value, 'product_card', 'text');
        }

        Notification::make()->title('Product card settings saved!')->success()->send();
    }
}
