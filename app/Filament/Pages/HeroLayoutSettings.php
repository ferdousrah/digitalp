<?php

namespace App\Filament\Pages;

use App\Services\SettingService;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class HeroLayoutSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-squares-2x2';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Hero Layout';
    protected static ?int    $navigationSort  = 3;
    protected static string  $view            = 'filament.pages.hero-layout-settings';

    // Form state
    public bool $hero_show_banners     = true;
    public int  $hero_slider_height    = 420;
    public int  $hero_slider_col_width = 75;
    public int  $hero_banner_col_width = 25;

    public function mount(): void
    {
        $this->form->fill([
            'hero_show_banners'     => (bool) SettingService::get('hero_show_banners', '1'),
            'hero_slider_height'    => (int)  SettingService::get('hero_slider_height', 420),
            'hero_slider_col_width' => (int)  SettingService::get('hero_slider_col_width', 75),
            'hero_banner_col_width' => (int)  SettingService::get('hero_banner_col_width', 25),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([

            Section::make('Banner Visibility')
                ->description('Show or hide the right-side banner column.')
                ->schema([
                    Toggle::make('hero_show_banners')
                        ->label('Show banner column')
                        ->helperText('Turn off to display the slider full-width across the entire hero section.')
                        ->default(true)
                        ->live(),
                ]),

            Section::make('Hero Height')
                ->description('Controls the height of the hero section in pixels.')
                ->schema([
                    TextInput::make('hero_slider_height')
                        ->label('Hero height (px)')
                        ->numeric()
                        ->minValue(200)
                        ->maxValue(800)
                        ->suffix('px')
                        ->helperText('Recommended: 380–500 px for desktop. On mobile it auto-scales to 240 px.')
                        ->default(420),
                ]),

            Section::make('Column Widths')
                ->description('Set the proportional width of each column (like WordPress column controls). Values act as percentages — they do not need to add up to 100, but a balanced ratio is recommended.')
                ->columns(2)
                ->schema([
                    TextInput::make('hero_slider_col_width')
                        ->label('Slider column width (%)')
                        ->numeric()
                        ->minValue(10)
                        ->maxValue(90)
                        ->suffix('%')
                        ->helperText('e.g. 75 means the slider takes 75% of the row width.')
                        ->default(75),

                    TextInput::make('hero_banner_col_width')
                        ->label('Banner column width (%)')
                        ->numeric()
                        ->minValue(10)
                        ->maxValue(90)
                        ->suffix('%')
                        ->helperText('e.g. 25 means banners take 25% of the row width.')
                        ->default(25),
                ])
                ->visible(fn ($get) => (bool) $get('hero_show_banners')),

        ])->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        SettingService::set('hero_show_banners',     $data['hero_show_banners'] ? '1' : '0', 'layout', 'boolean');
        SettingService::set('hero_slider_height',    (string) $data['hero_slider_height'],    'layout', 'text');
        SettingService::set('hero_slider_col_width', (string) $data['hero_slider_col_width'], 'layout', 'text');
        SettingService::set('hero_banner_col_width', (string) $data['hero_banner_col_width'], 'layout', 'text');

        Notification::make()
            ->title('Hero layout saved!')
            ->success()
            ->send();
    }

    // Required by InteractsWithForms when using statePath
    public array $data = [];
}
