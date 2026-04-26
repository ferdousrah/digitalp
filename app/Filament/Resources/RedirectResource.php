<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesWithPermission;
use App\Filament\Concerns\ExportsToCsv;
use App\Filament\Resources\RedirectResource\Pages;
use App\Models\Redirect as RedirectModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RedirectResource extends Resource
{
    use AuthorizesWithPermission;
    use ExportsToCsv;
    protected static ?string $permissionKey = 'redirects';

    protected static ?string $model = RedirectModel::class;
    protected static ?string $recordTitleAttribute = 'source_path';
    protected static ?string $navigationIcon = 'heroicon-o-arrow-right-circle';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'URL Redirects';
    protected static ?int $navigationSort = 6;

    public static function getGloballySearchableAttributes(): array
    {
        return ['source_path', 'target_path'];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Redirect')->columns(2)->schema([
                Forms\Components\TextInput::make('source_path')
                    ->label('From')
                    ->required()
                    ->prefix(rtrim(config('app.url'), '/'))
                    ->placeholder('/old-product-slug')
                    ->helperText('The old URL path that visitors hit. Must start with /')
                    ->dehydrateStateUsing(fn ($state) => RedirectModel::normalize((string) $state))
                    ->unique(ignoreRecord: true)
                    ->maxLength(500),

                Forms\Components\TextInput::make('target_path')
                    ->label('To')
                    ->required()
                    ->placeholder('/products/new-slug')
                    ->helperText('Where to send them. Can be a relative path (/foo/bar) or full URL (https://...)')
                    ->dehydrateStateUsing(function ($state) {
                        $state = (string) $state;
                        // If it's a full URL, leave it. Otherwise normalize the path.
                        if (preg_match('~^https?://~i', $state)) {
                            return $state;
                        }
                        return RedirectModel::normalize($state);
                    })
                    ->maxLength(500),

                Forms\Components\Select::make('status_code')
                    ->options(RedirectModel::statusOptions())
                    ->default(301)
                    ->required()
                    ->helperText('301 is the default — tells search engines the move is permanent.'),

                Forms\Components\Toggle::make('is_active')
                    ->default(true)
                    ->inline(false),

                Forms\Components\Textarea::make('notes')
                    ->rows(2)
                    ->columnSpanFull()
                    ->placeholder('Why this redirect exists — e.g. "Renamed product after rebrand"'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn ($record) => Pages\EditRedirect::getUrl(['record' => $record]))
            ->defaultSort('hits', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('source_path')
                    ->label('From')
                    ->searchable()
                    ->copyable()
                    ->weight('semibold')
                    ->wrap()
                    ->limit(50),
                Tables\Columns\IconColumn::make('arrow')
                    ->label('')
                    ->getStateUsing(fn () => true)
                    ->icon('heroicon-o-arrow-long-right')
                    ->color('gray'),
                Tables\Columns\TextColumn::make('target_path')
                    ->label('To')
                    ->searchable()
                    ->wrap()
                    ->limit(50),
                Tables\Columns\TextColumn::make('status_code')
                    ->badge()
                    ->color(fn ($state) => match ((int) $state) {
                        301, 308 => 'success',
                        302, 307 => 'warning',
                        default  => 'gray',
                    }),
                Tables\Columns\TextColumn::make('hits')
                    ->label('Hits')
                    ->numeric()
                    ->sortable()
                    ->alignRight(),
                Tables\Columns\TextColumn::make('last_hit_at')
                    ->label('Last hit')
                    ->dateTime('M d, Y h:i A')
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_auto')
                    ->label('Auto')
                    ->boolean()
                    ->trueIcon('heroicon-o-bolt')
                    ->falseIcon('heroicon-o-user')
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->filters([
                SelectFilter::make('status_code')->options(RedirectModel::statusOptions()),
                Filter::make('auto')->label('Auto-created')->query(fn ($q) => $q->where('is_auto', true))->toggle(),
                Filter::make('used')->label('Has been used')->query(fn ($q) => $q->where('hits', '>', 0))->toggle(),
                Filter::make('unused')->label('Never used')->query(fn ($q) => $q->where('hits', 0))->toggle(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('toggleActive')
                    ->label(fn ($record) => $record->is_active ? 'Deactivate' : 'Activate')
                    ->icon(fn ($record) => $record->is_active ? 'heroicon-o-pause' : 'heroicon-o-play')
                    ->color(fn ($record) => $record->is_active ? 'warning' : 'success')
                    ->action(fn ($record) => $record->update(['is_active' => !$record->is_active])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    static::csvExportBulkAction(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRedirects::route('/'),
            'create' => Pages\CreateRedirect::route('/create'),
            'edit'   => Pages\EditRedirect::route('/{record}/edit'),
        ];
    }
}
