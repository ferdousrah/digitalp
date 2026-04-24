<?php

namespace App\Filament\Pages;

use App\Services\SettingService;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class TemplateSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-swatch';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Template / Colors';
    protected static ?int    $navigationSort  = 7;
    protected static string  $view            = 'filament.pages.template-settings';

    public array $data = [];

    // All defaults in one place
    public static function defaults(): array
    {
        return [
            // Top bar
            'color_top_bar_bg'          => '#16a34a',
            'color_top_bar_text'        => '#ffffff',
            // Header
            'color_header_bg'           => '#ffffff',
            'color_header_text'         => '#374151',
            'color_header_icon_hover'   => '#16a34a',
            // Nav bar
            'color_nav_bg'              => '#0d1f2d',
            'color_nav_text'            => '#e2e8f0',
            'color_nav_hover_bg'        => '#1e3a5f',
            'color_nav_hover_text'      => '#ffffff',
            // Nav submenu / dropdown
            'color_nav_submenu_bg'          => '#ffffff',
            'color_nav_submenu_text'        => '#374151',
            'color_nav_submenu_border'      => '#f97316',
            'color_nav_submenu_hover_bg'    => '#f0fdf4',
            'color_nav_submenu_hover_text'  => '#16a34a',
            // Brand
            'color_primary'             => '#16a34a',
            'color_primary_text'        => '#ffffff',
            'color_primary_hover'       => '#15803d',
            'color_accent'              => '#f97316',
            'color_accent_text'         => '#ffffff',
            'color_accent_hover'        => '#ea6c0a',
            // Add to Cart button
            'color_btn_cart_bg'         => '#f97316',
            'color_btn_cart_text'       => '#ffffff',
            'color_btn_cart_hover_bg'   => '#ea6c0a',
            'color_btn_cart_hover_text' => '#ffffff',
            // Buy Now button
            'color_btn_buy_bg'          => '#111827',
            'color_btn_buy_text'        => '#ffffff',
            'color_btn_buy_hover_bg'    => '#16a34a',
            'color_btn_buy_hover_text'  => '#ffffff',
            // WhatsApp button
            'color_btn_wa_bg'           => '#25d366',
            'color_btn_wa_text'         => '#ffffff',
            'color_btn_wa_hover_bg'     => '#1ebe5a',
            'color_btn_wa_hover_text'   => '#ffffff',
            // Call button
            'color_btn_call_bg'         => '#2563eb',
            'color_btn_call_text'       => '#ffffff',
            'color_btn_call_hover_bg'   => '#1d4ed8',
            'color_btn_call_hover_text' => '#ffffff',
            // Footer
            'color_footer_bg'           => '#171717',
            'color_footer_text'         => '#a3a3a3',
            'color_footer_heading'      => '#ffffff',
            'color_footer_link'         => '#a3a3a3',
            'color_footer_link_hover'   => '#16a34a',
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

            Section::make('Top Bar')
                ->columns(2)
                ->schema([
                    ColorPicker::make('color_top_bar_bg')   ->label('Background'),
                    ColorPicker::make('color_top_bar_text') ->label('Text / Link Color'),
                ]),

            Section::make('Header Bar')
                ->description('Main bar containing logo, search, and icons.')
                ->columns(3)
                ->schema([
                    ColorPicker::make('color_header_bg')         ->label('Background'),
                    ColorPicker::make('color_header_text')       ->label('Icon & Label Color'),
                    ColorPicker::make('color_header_icon_hover') ->label('Icon Hover Color'),
                ]),

            Section::make('Navigation / Menu Bar')
                ->description('The sticky category/menu bar.')
                ->columns(4)
                ->schema([
                    ColorPicker::make('color_nav_bg')         ->label('Background'),
                    ColorPicker::make('color_nav_text')       ->label('Link Color'),
                    ColorPicker::make('color_nav_hover_bg')   ->label('Link Hover BG'),
                    ColorPicker::make('color_nav_hover_text') ->label('Link Hover Text'),
                ]),

            Section::make('Navigation Submenu / Dropdown')
                ->description('Colors for the dropdown menu that appears under parent categories.')
                ->columns(5)
                ->schema([
                    ColorPicker::make('color_nav_submenu_bg')         ->label('Background'),
                    ColorPicker::make('color_nav_submenu_text')       ->label('Text Color'),
                    ColorPicker::make('color_nav_submenu_border')    ->label('Top Border'),
                    ColorPicker::make('color_nav_submenu_hover_bg')   ->label('Hover BG'),
                    ColorPicker::make('color_nav_submenu_hover_text') ->label('Hover Text'),
                ]),

            Section::make('Brand Colors')
                ->description('Primary and accent colors used for badges, borders, and highlights.')
                ->columns(3)
                ->schema([
                    ColorPicker::make('color_primary')       ->label('Primary Background'),
                    ColorPicker::make('color_primary_text')  ->label('Primary Text'),
                    ColorPicker::make('color_primary_hover') ->label('Primary Hover'),

                    ColorPicker::make('color_accent')        ->label('Accent Background'),
                    ColorPicker::make('color_accent_text')   ->label('Accent Text'),
                    ColorPicker::make('color_accent_hover')  ->label('Accent Hover'),
                ]),

            Section::make('Add to Cart Button')
                ->columns(4)
                ->schema([
                    ColorPicker::make('color_btn_cart_bg')         ->label('Background'),
                    ColorPicker::make('color_btn_cart_text')       ->label('Text Color'),
                    ColorPicker::make('color_btn_cart_hover_bg')   ->label('Hover Background'),
                    ColorPicker::make('color_btn_cart_hover_text') ->label('Hover Text Color'),
                ]),

            Section::make('Buy Now Button')
                ->columns(4)
                ->schema([
                    ColorPicker::make('color_btn_buy_bg')         ->label('Background'),
                    ColorPicker::make('color_btn_buy_text')       ->label('Text Color'),
                    ColorPicker::make('color_btn_buy_hover_bg')   ->label('Hover Background'),
                    ColorPicker::make('color_btn_buy_hover_text') ->label('Hover Text Color'),
                ]),

            Section::make('WhatsApp Button')
                ->columns(4)
                ->schema([
                    ColorPicker::make('color_btn_wa_bg')         ->label('Background'),
                    ColorPicker::make('color_btn_wa_text')       ->label('Text Color'),
                    ColorPicker::make('color_btn_wa_hover_bg')   ->label('Hover Background'),
                    ColorPicker::make('color_btn_wa_hover_text') ->label('Hover Text Color'),
                ]),

            Section::make('Call Button')
                ->columns(4)
                ->schema([
                    ColorPicker::make('color_btn_call_bg')         ->label('Background'),
                    ColorPicker::make('color_btn_call_text')       ->label('Text Color'),
                    ColorPicker::make('color_btn_call_hover_bg')   ->label('Hover Background'),
                    ColorPicker::make('color_btn_call_hover_text') ->label('Hover Text Color'),
                ]),

            Section::make('Footer')
                ->columns(5)
                ->schema([
                    ColorPicker::make('color_footer_bg')         ->label('Background'),
                    ColorPicker::make('color_footer_text')       ->label('Body Text'),
                    ColorPicker::make('color_footer_heading')    ->label('Headings'),
                    ColorPicker::make('color_footer_link')       ->label('Links'),
                    ColorPicker::make('color_footer_link_hover') ->label('Link Hover'),
                ]),

        ])->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        foreach ($data as $key => $value) {
            SettingService::set($key, $value, 'template', 'color');
        }

        Notification::make()->title('Template colors saved!')->success()->send();
    }
}
