<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

use App\Filament\Concerns\AuthorizesWithPermission;
class SettingResource extends Resource
{
    use AuthorizesWithPermission;
    protected static ?string $permissionKey = 'settings';

    protected static ?string $model = Setting::class;
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Setting Details')->schema([
                Forms\Components\Select::make('group')->options([
                    'general' => 'General',
                    'branding' => 'Branding',
                    'contact' => 'Contact',
                    'social' => 'Social',
                    'seo' => 'SEO',
                    'layout' => 'Layout',
                ])->required()->columnSpan(1),

                Forms\Components\TextInput::make('key')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(1),

                Forms\Components\Select::make('type')->options([
                    'text' => 'Text',
                    'textarea' => 'Textarea',
                    'boolean' => 'Boolean',
                    'json' => 'JSON',
                    'image' => 'Image',
                ])->default('text')->live()->columnSpan(1),
            ])->columns(3),

            Forms\Components\Section::make('Value')->schema([
                // Text value
                Forms\Components\TextInput::make('value')
                    ->label('Value')
                    ->maxLength(65535)
                    ->hidden(fn (Get $get) => !in_array($get('type'), ['text', null, ''])),

                // Boolean toggle
                Forms\Components\Toggle::make('value')
                    ->label('Enabled')
                    ->onColor('success')
                    ->offColor('danger')
                    ->hidden(fn (Get $get) => $get('type') !== 'boolean')
                    ->afterStateHydrated(fn ($component, $state) => $component->state((bool)(int)$state))
                    ->dehydrateStateUsing(fn ($state) => $state ? '1' : '0'),

                // Textarea value
                Forms\Components\Textarea::make('value')
                    ->label('Value')
                    ->rows(4)
                    ->hidden(fn (Get $get) => $get('type') !== 'textarea'),

                // JSON value
                Forms\Components\Textarea::make('value')
                    ->label('Value (JSON)')
                    ->rows(6)
                    ->hidden(fn (Get $get) => $get('type') !== 'json'),

                // Image upload — uses a separate state key, synced to 'value' on save
                Forms\Components\FileUpload::make('image_upload')
                    ->label('Upload Image')
                    ->hidden(fn (Get $get) => $get('type') !== 'image')
                    ->image()
                    ->directory('settings')
                    ->disk('public')
                    ->maxSize(2048)
                    ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/svg+xml', 'image/x-icon', 'image/vnd.microsoft.icon', 'image/webp', 'image/gif'])
                    ->afterStateHydrated(function (Forms\Components\FileUpload $component, $state, $record) {
                        if ($record && $record->type === 'image' && $record->value) {
                            $component->state([$record->value]);
                        }
                    }),

                // Show current image preview when editing
                Forms\Components\Placeholder::make('current_image')
                    ->label('Current Image')
                    ->hidden(fn (Get $get, $record) => $get('type') !== 'image' || !$record?->value)
                    ->content(fn ($record) => $record?->value ? new \Illuminate\Support\HtmlString(
                        '<img src="' . Storage::disk('public')->url($record->value) . '" style="max-height: 80px; border-radius: 8px;">'
                    ) : ''),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('group')->badge()->sortable(),
            Tables\Columns\TextColumn::make('key')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('value')
                ->label('Value')
                ->limit(60)
                ->formatStateUsing(function ($state, $record) {
                    if ($record->type === 'image' && $state) {
                        return '🖼 ' . basename($state);
                    }
                    return $state;
                }),
            Tables\Columns\TextColumn::make('type')->badge()->color(fn (string $state): string => match ($state) {
                'image' => 'success',
                'text' => 'info',
                'textarea' => 'warning',
                default => 'gray',
            }),
        ])->filters([
            Tables\Filters\SelectFilter::make('group')->options([
                'general' => 'General',
                'branding' => 'Branding',
                'contact' => 'Contact',
                'social' => 'Social',
                'seo' => 'SEO',
                'layout' => 'Layout',
            ]),
        ])->actions([
            Tables\Actions\EditAction::make(),
        ])->bulkActions([
            Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
        ]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
