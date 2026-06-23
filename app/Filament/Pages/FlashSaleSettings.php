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
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class FlashSaleSettings extends Page implements HasForms
{
    use InteractsWithForms;

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && $user->can('template.view');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    protected static ?string $navigationIcon  = 'heroicon-o-bolt';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Flash Sale Button';
    protected static ?int    $navigationSort  = 8;
    protected static string  $view            = 'filament.pages.flash-sale-settings';

    public array $data = [];

    /** Boolean-style keys (stored as '1'/'0', shown as toggles). */
    public const TOGGLES = [
        'flash_btn_enabled', 'flash_btn_new_tab', 'flash_btn_show_icon',
        'flash_btn_uppercase', 'flash_btn_pulse',
    ];

    public static function defaults(): array
    {
        return [
            'flash_btn_enabled'     => '1',
            'flash_btn_text'        => 'FLASH SALE',
            'flash_btn_url'         => '/products?sale=1',
            'flash_btn_new_tab'     => '0',
            'flash_btn_show_icon'   => '1',
            'flash_btn_icon'        => 'bolt',
            'flash_btn_bg'          => '#f97316',
            'flash_btn_text_color'  => '#ffffff',
            'flash_btn_hover_bg'    => '#ea6c0a',
            'flash_btn_hover_text'  => '#ffffff',
            'flash_btn_font_size'   => '13.5',  // px
            'flash_btn_font_weight' => '700',
            'flash_btn_radius'      => '6',      // px — 0 = flat, large = pill
            'flash_btn_uppercase'   => '0',
            'flash_btn_letter_spacing' => '0.06', // em
            'flash_btn_padding_y'   => '8',     // px
            'flash_btn_padding_x'   => '18',    // px
            'flash_btn_pulse'       => '0',
        ];
    }

    public function mount(): void
    {
        $filled = [];
        foreach (self::defaults() as $key => $default) {
            $value = SettingService::get($key, $default);
            $filled[$key] = in_array($key, self::TOGGLES, true)
                ? in_array($value, ['1', 1, true, 'true'], true)
                : $value;
        }
        $this->form->fill($filled);
    }

    public function form(Form $form): Form
    {
        return $form->schema([

            Section::make('Content')
                ->columns(2)
                ->schema([
                    Toggle::make('flash_btn_enabled')
                        ->label('Show the Flash Sale button')
                        ->columnSpanFull(),
                    TextInput::make('flash_btn_text')
                        ->label('Button text')->required()->maxLength(30)->placeholder('FLASH SALE'),
                    TextInput::make('flash_btn_url')
                        ->label('Link URL')->required()
                        ->placeholder('/products?sale=1')
                        ->helperText('Relative path or full URL. Query strings supported.'),
                    Toggle::make('flash_btn_new_tab')->label('Open in new tab'),
                    Toggle::make('flash_btn_uppercase')->label('Force UPPERCASE text'),
                ]),

            Section::make('Icon')
                ->columns(2)
                ->schema([
                    Toggle::make('flash_btn_show_icon')->label('Show icon')->live(),
                    Select::make('flash_btn_icon')
                        ->label('Icon')
                        ->visible(fn (Get $get): bool => (bool) $get('flash_btn_show_icon'))
                        ->options([
                            'bolt' => '⚡ Bolt', 'star' => '★ Star', 'tags' => '🏷 Tag',
                            'gift' => '🎁 Gift', 'heart' => '♥ Heart',
                            'shopping-cart' => '🛒 Cart', 'shopping-bag' => '🛍 Bag', 'box-open' => '📦 Box',
                        ]),
                ]),

            Section::make('Colors')
                ->columns(4)
                ->schema([
                    ColorPicker::make('flash_btn_bg')->label('Background'),
                    ColorPicker::make('flash_btn_text_color')->label('Text'),
                    ColorPicker::make('flash_btn_hover_bg')->label('Hover background'),
                    ColorPicker::make('flash_btn_hover_text')->label('Hover text'),
                ]),

            Section::make('Shape & Typography')
                ->columns(3)
                ->schema([
                    TextInput::make('flash_btn_radius')
                        ->label('Corner radius (px)')->numeric()->minValue(0)->maxValue(999)
                        ->helperText('0 = flat/square · 6 = rounded · 999 = pill'),
                    TextInput::make('flash_btn_font_size')
                        ->label('Font size (px)')->numeric()->minValue(8)->maxValue(40),
                    Select::make('flash_btn_font_weight')
                        ->label('Font weight')
                        ->options([
                            '400' => 'Normal (400)', '500' => 'Medium (500)',
                            '600' => 'Semibold (600)', '700' => 'Bold (700)', '800' => 'Extra-bold (800)',
                        ]),
                    TextInput::make('flash_btn_letter_spacing')
                        ->label('Letter spacing (em)')->numeric()->step('0.01'),
                    TextInput::make('flash_btn_padding_y')
                        ->label('Vertical padding (px)')->numeric()->minValue(0)->maxValue(40),
                    TextInput::make('flash_btn_padding_x')
                        ->label('Horizontal padding (px)')->numeric()->minValue(0)->maxValue(60),
                ]),

            Section::make('Animation')
                ->schema([
                    Toggle::make('flash_btn_pulse')
                        ->label('Pulse / glow animation')
                        ->helperText('Subtle attention-grabbing pulse to draw the eye to the offer.'),
                ]),

        ])->statePath('data');
    }

    public function save(): void
    {
        foreach ($this->form->getState() as $key => $value) {
            if (is_bool($value)) {
                $value = $value ? '1' : '0';
            }
            SettingService::set($key, (string) $value, 'flash_sale', 'text');
        }

        Notification::make()->title('Flash Sale button saved!')->success()->send();
    }
}
