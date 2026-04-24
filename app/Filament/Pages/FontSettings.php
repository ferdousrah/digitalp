<?php

namespace App\Filament\Pages;

use App\Services\SettingService;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class FontSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-language';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Font Settings';
    protected static ?int    $navigationSort  = 6;
    protected static string  $view            = 'filament.pages.font-settings';

    public array $data = [];

    // English Google Fonts
    public const ENGLISH_FONTS = [
        'Inter'          => 'Inter',
        'Roboto'         => 'Roboto',
        'Poppins'        => 'Poppins',
        'Open Sans'      => 'Open Sans',
        'Lato'           => 'Lato',
        'Nunito'         => 'Nunito',
        'Raleway'        => 'Raleway',
        'Montserrat'     => 'Montserrat',
        'Source Sans 3'  => 'Source Sans 3',
        'Ubuntu'         => 'Ubuntu',
        'Outfit'         => 'Outfit',
        'DM Sans'        => 'DM Sans',
        'Figtree'        => 'Figtree',
        'Plus Jakarta Sans' => 'Plus Jakarta Sans',
    ];

    // Bangla Google Fonts
    public const BANGLA_FONTS = [
        'Hind Siliguri'   => 'Hind Siliguri',
        'Noto Sans Bengali' => 'Noto Sans Bengali',
        'Baloo Da 2'      => 'Baloo Da 2',
        'Tiro Bangla'     => 'Tiro Bangla',
        'Galada'          => 'Galada',
        'Anek Bangla'     => 'Anek Bangla',
        'Murecho'         => 'Murecho',
        'Siyam Rupali'    => 'Siyam Rupali',
        'Balooda Chettan 2' => 'Baloo Da 2',
        'Sholar Bangla'   => 'Noto Serif Bengali',
    ];

    public function mount(): void
    {
        $this->form->fill([
            'font_english'      => SettingService::get('font_english', 'Inter'),
            'font_bangla'       => SettingService::get('font_bangla', 'Hind Siliguri'),
            'font_size_base'    => SettingService::get('font_size_base', '16'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([

            Section::make('English Font')
                ->description('Applied to all Latin/English text on the website.')
                ->schema([
                    Select::make('font_english')
                        ->label('English Font Family')
                        ->options(self::ENGLISH_FONTS)
                        ->searchable()
                        ->required()
                        ->helperText('Select a Google Font for English/Latin text.'),
                ]),

            Section::make('Bangla Font')
                ->description('Applied to Bengali/Bangla text on the website.')
                ->schema([
                    Select::make('font_bangla')
                        ->label('Bangla Font Family')
                        ->options([
                            'Hind Siliguri'     => 'Hind Siliguri',
                            'Noto Sans Bengali' => 'Noto Sans Bengali',
                            'Noto Serif Bengali' => 'Noto Serif Bengali',
                            'Baloo Da 2'        => 'Baloo Da 2',
                            'Tiro Bangla'       => 'Tiro Bangla',
                            'Galada'            => 'Galada',
                            'Anek Bangla'       => 'Anek Bangla',
                            'Murecho'           => 'Murecho',
                            'Siyam Rupali'      => 'Siyam Rupali',
                        ])
                        ->searchable()
                        ->required()
                        ->helperText('Select a Google Font that supports Bangla script.'),
                ]),

            Section::make('Base Font Size')
                ->description('The root font size in pixels. All rem-based sizes scale from this.')
                ->schema([
                    TextInput::make('font_size_base')
                        ->label('Base font size')
                        ->numeric()
                        ->minValue(12)
                        ->maxValue(24)
                        ->suffix('px')
                        ->default('16')
                        ->helperText('Default is 16px. Increase to make all text larger site-wide.'),
                ]),

        ])->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        SettingService::set('font_english',   $data['font_english'],   'fonts', 'text');
        SettingService::set('font_bangla',    $data['font_bangla'],    'fonts', 'text');
        SettingService::set('font_size_base', $data['font_size_base'], 'fonts', 'text');

        Notification::make()
            ->title('Font settings saved!')
            ->success()
            ->send();
    }
}
