<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesWithPermission;
use App\Filament\Concerns\ExportsToCsv;
use App\Filament\Resources\CouponResource\Pages;
use App\Models\Coupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

class CouponResource extends Resource
{
    use AuthorizesWithPermission;
    use ExportsToCsv;
    protected static ?string $permissionKey = 'coupons';

    protected static ?string $model = Coupon::class;
    protected static ?string $recordTitleAttribute = 'code';
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Shop';
    protected static ?string $navigationLabel = 'Coupons';
    protected static ?int $navigationSort = 3;

    public static function getGloballySearchableAttributes(): array
    {
        return ['code', 'description'];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Coupon Details')->columns(2)->schema([
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true)
                    ->helperText('Customer types this. Case-insensitive — stored as uppercase.')
                    ->dehydrateStateUsing(fn ($state) => strtoupper(trim((string) $state))),
                Forms\Components\Toggle::make('is_active')->default(true)->inline(false),
                Forms\Components\Textarea::make('description')
                    ->rows(2)
                    ->columnSpanFull()
                    ->placeholder('Internal notes about this coupon'),
            ]),

            Forms\Components\Section::make('Discount')->columns(3)->schema([
                Forms\Components\Select::make('type')
                    ->options(['percentage' => 'Percentage (%)', 'fixed' => 'Fixed (৳)'])
                    ->default('percentage')
                    ->required()
                    ->live(),
                Forms\Components\TextInput::make('value')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->suffix(fn (Forms\Get $get) => $get('type') === 'percentage' ? '%' : '৳')
                    ->helperText(fn (Forms\Get $get) => $get('type') === 'percentage'
                        ? 'Percent off the subtotal (e.g. 10 means 10%)'
                        : 'Fixed amount off the subtotal'),
                Forms\Components\TextInput::make('max_discount')
                    ->label('Max Discount Cap')
                    ->numeric()
                    ->prefix('৳')
                    ->nullable()
                    ->visible(fn (Forms\Get $get) => $get('type') === 'percentage')
                    ->helperText('For percent coupons: cap the discount amount. Leave blank for no cap.'),
            ]),

            Forms\Components\Section::make('Restrictions')->columns(2)->schema([
                Forms\Components\TextInput::make('min_order_amount')
                    ->label('Minimum Order Subtotal')
                    ->numeric()
                    ->prefix('৳')
                    ->nullable()
                    ->helperText('Order subtotal must reach this amount.'),
                Forms\Components\TextInput::make('usage_limit')
                    ->label('Global Usage Limit')
                    ->numeric()
                    ->integer()
                    ->minValue(1)
                    ->nullable()
                    ->helperText('Total times this code can be used across all customers.'),
                Forms\Components\TextInput::make('usage_limit_per_customer')
                    ->label('Per-Customer Limit')
                    ->numeric()
                    ->integer()
                    ->minValue(1)
                    ->nullable()
                    ->helperText('Max uses by the same phone number.'),
                Forms\Components\Placeholder::make('used_count_display')
                    ->label('Used so far')
                    ->content(fn ($record) => $record ? (int) $record->used_count : 0),
                Forms\Components\DateTimePicker::make('starts_at')
                    ->native(false)
                    ->nullable()
                    ->helperText('Leave blank for "starts immediately".'),
                Forms\Components\DateTimePicker::make('expires_at')
                    ->native(false)
                    ->nullable()
                    ->after('starts_at')
                    ->helperText('Leave blank for "never expires".'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn ($record) => Pages\EditCoupon::getUrl(['record' => $record]))
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->copyable()
                    ->weight('bold')
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state === 'percentage' ? 'Percent' : 'Fixed')
                    ->color(fn ($state) => $state === 'percentage' ? 'info' : 'success'),
                Tables\Columns\TextColumn::make('value')
                    ->formatStateUsing(function ($state, $record) {
                        return $record->type === 'percentage'
                            ? number_format((float) $state, 0) . '%'
                            : '৳' . number_format((float) $state, 0);
                    })
                    ->label('Off'),
                Tables\Columns\TextColumn::make('used_count')
                    ->label('Used')
                    ->formatStateUsing(fn ($state, $record) => $record->usage_limit
                        ? $state . ' / ' . $record->usage_limit
                        : $state),
                Tables\Columns\TextColumn::make('min_order_amount')
                    ->label('Min Order')
                    ->money('BDT')
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expires')
                    ->dateTime('M d, Y')
                    ->placeholder('Never')
                    ->color(fn ($state) => $state && now()->isAfter($state) ? 'danger' : 'gray'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->filters([
                Filter::make('active')->label('Active only')->query(fn ($q) => $q->where('is_active', true))->toggle(),
                Filter::make('expired')->label('Expired')->query(fn ($q) => $q->whereNotNull('expires_at')->where('expires_at', '<', now()))->toggle(),
                Filter::make('exhausted')->label('Fully used')->query(fn ($q) => $q->whereColumn('used_count', '>=', 'usage_limit'))->toggle(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('toggleActive')
                    ->label(fn ($record) => $record->is_active ? 'Deactivate' : 'Activate')
                    ->icon(fn ($record) => $record->is_active ? 'heroicon-o-pause' : 'heroicon-o-play')
                    ->color(fn ($record) => $record->is_active ? 'warning' : 'success')
                    ->action(fn ($record) => $record->update(['is_active' => !$record->is_active])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    static::csvExportBulkAction(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            CouponResource\RelationManagers\RedemptionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit'   => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
