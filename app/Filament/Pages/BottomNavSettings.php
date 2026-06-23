<?php

namespace App\Filament\Pages;

use App\Services\SettingService;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
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

class BottomNavSettings extends Page implements HasForms
{
    use InteractsWithForms;

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && $user->can('bottom_nav.view');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    protected static ?string $navigationIcon  = 'heroicon-o-device-phone-mobile';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Mobile Bottom Nav';
    protected static ?int    $navigationSort  = 9;
    protected static string  $view            = 'filament.pages.bottom-nav-settings';

    public array $data = [];

    /** Scalar (color / toggle) settings with their default values. */
    public static function defaults(): array
    {
        return [
            'bottom_nav_enabled'      => '1',
            'bottom_nav_bg'           => '#ffffff',
            'bottom_nav_border'       => '#e5e7eb',
            'bottom_nav_text_color'   => '#6b7280',
            'bottom_nav_icon_color'   => '#6b7280',
            'bottom_nav_active_color' => '#16a34a',
            'bottom_nav_badge_bg'     => '#f97316',
            'bottom_nav_badge_text'   => '#ffffff',
        ];
    }

    /** Default tab list — used on first load and as a fallback. */
    public static function defaultItems(): array
    {
        return [
            ['label' => 'Home',       'icon' => 'fi-rr-home',          'action' => 'link',    'url' => '/'],
            ['label' => 'Categories', 'icon' => 'fi-rr-apps',          'action' => 'link',    'url' => '/categories/all'],
            ['label' => 'Offers',     'icon' => 'fi-rr-tags',          'action' => 'link',    'url' => '/offers'],
            ['label' => 'Cart',       'icon' => 'fi-rr-shopping-cart', 'action' => 'cart',    'url' => '#cart'],
            ['label' => 'Profile',    'icon' => 'fi-rr-user',          'action' => 'account', 'url' => '/account'],
        ];
    }

    public function mount(): void
    {
        $filled = [];
        foreach (self::defaults() as $key => $default) {
            $value = SettingService::get($key, $default);
            $filled[$key] = $key === 'bottom_nav_enabled'
                ? in_array($value, ['1', 1, true, 'true'], true)
                : $value;
        }

        $itemsRaw = SettingService::get('bottom_nav_items');
        $items    = $itemsRaw ? json_decode($itemsRaw, true) : null;
        $filled['bottom_nav_items'] = (is_array($items) && count($items)) ? $items : self::defaultItems();

        $this->form->fill($filled);
    }

    public function form(Form $form): Form
    {
        return $form->schema([

            Section::make('General')
                ->schema([
                    Toggle::make('bottom_nav_enabled')
                        ->label('Enable mobile bottom navigation')
                        ->helperText('Shows the app-style bar on screens narrower than 1024px.'),
                ]),

            Section::make('Colors')
                ->columns(4)
                ->schema([
                    ColorPicker::make('bottom_nav_bg')          ->label('Background'),
                    ColorPicker::make('bottom_nav_border')      ->label('Top border'),
                    ColorPicker::make('bottom_nav_text_color')  ->label('Label (idle)'),
                    ColorPicker::make('bottom_nav_icon_color')  ->label('Icon (idle)'),
                    ColorPicker::make('bottom_nav_active_color')->label('Active / hover'),
                    ColorPicker::make('bottom_nav_badge_bg')    ->label('Badge background'),
                    ColorPicker::make('bottom_nav_badge_text')  ->label('Badge text'),
                ]),

            Section::make('Items')
                ->description('Each tab in the bar (max 6). Icon = one of the built-in names: home, apps, tags, gift, star, search, heart, user, shopping-cart, shopping-bag, box-open, marker, phone-call, envelope, link. (Legacy "fi-rr-…" values still work; unknown names show a link icon.)')
                ->schema([
                    Repeater::make('bottom_nav_items')
                        ->hiddenLabel()
                        ->reorderable()
                        ->collapsible()
                        ->cloneable()
                        ->itemLabel(fn (array $state): ?string => $state['label'] ?? 'Item')
                        ->columns(4)
                        ->schema([
                            TextInput::make('label')
                                ->label('Label')->required()->maxLength(20),

                            TextInput::make('icon')
                                ->label('Icon name')->required()
                                ->placeholder('home')->default('link'),

                            Select::make('action')
                                ->label('Action')->required()->default('link')
                                ->live()
                                ->options([
                                    'link'    => 'Open link',
                                    'cart'    => 'Open cart drawer (+ badge)',
                                    'account' => 'Account / Login',
                                ]),

                            TextInput::make('url')
                                ->label('Link URL')
                                ->placeholder('/products')
                                ->helperText('Relative path or full URL. Supports query strings, e.g. /products?sale=1')
                                ->visible(fn (Get $get): bool => $get('action') === 'link')
                                ->required(fn (Get $get): bool => $get('action') === 'link'),
                        ])
                        ->minItems(1)
                        ->maxItems(6)
                        ->addActionLabel('Add tab'),
                ]),

        ])->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $items = array_values($data['bottom_nav_items'] ?? []);
        unset($data['bottom_nav_items']);

        foreach ($data as $key => $value) {
            if (is_bool($value)) {
                $value = $value ? '1' : '0';
            }
            SettingService::set($key, (string) $value, 'bottom_nav', 'text');
        }

        SettingService::set('bottom_nav_items', json_encode($items), 'bottom_nav', 'json');

        Notification::make()->title('Bottom navigation saved!')->success()->send();
    }
}
