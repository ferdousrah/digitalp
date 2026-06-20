<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Concerns\AuthorizesWithPermission;

class ReviewResource extends Resource
{
    use AuthorizesWithPermission;
    protected static ?string $permissionKey = 'reviews';

    protected static ?string $model = Review::class;
    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationGroup = 'Catalog';
    protected static ?int $navigationSort = 5;
    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationBadge(): ?string
    {
        $pending = Review::where('status', 'pending')->count();
        return $pending ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Review')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('product_id')->relationship('product', 'name')->disabled()->dehydrated(false)->label('Product'),
                    Forms\Components\TextInput::make('name')->disabled()->dehydrated(false)->label('Reviewer'),
                    Forms\Components\TextInput::make('rating')->disabled()->dehydrated(false)->suffix('/ 5'),
                    Forms\Components\TextInput::make('title')->disabled()->dehydrated(false),
                    Forms\Components\Textarea::make('comment')->disabled()->dehydrated(false)->rows(4)->columnSpanFull(),
                ]),
            Forms\Components\Section::make('Moderation')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('status')
                        ->options(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'])
                        ->required(),
                    Forms\Components\Toggle::make('is_verified')->label('Verified purchase'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')->label('Product')->limit(30)->searchable()->sortable(),
                Tables\Columns\TextColumn::make('name')->label('Reviewer')->searchable(),
                Tables\Columns\TextColumn::make('rating')->badge()->color('warning')->formatStateUsing(fn ($state) => $state . ' ★')->sortable(),
                Tables\Columns\TextColumn::make('title')->limit(30)->toggleable(),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn ($state) => match ($state) { 'approved' => 'success', 'rejected' => 'danger', default => 'warning' })
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_verified')->boolean()->label('Verified'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected']),
                Tables\Filters\SelectFilter::make('rating')->options([5 => '5 ★', 4 => '4 ★', 3 => '3 ★', 2 => '2 ★', 1 => '1 ★']),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')->icon('heroicon-o-check')->color('success')
                    ->visible(fn ($record) => $record->status !== 'approved')
                    ->action(fn ($record) => $record->update(['status' => 'approved'])),
                Tables\Actions\Action::make('reject')->icon('heroicon-o-x-mark')->color('danger')->requiresConfirmation()
                    ->visible(fn ($record) => $record->status !== 'rejected')
                    ->action(fn ($record) => $record->update(['status' => 'rejected'])),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve')->label('Approve')->icon('heroicon-o-check')->color('success')
                        ->action(fn ($records) => $records->each->update(['status' => 'approved']))->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('reject')->label('Reject')->icon('heroicon-o-x-mark')->color('danger')->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['status' => 'rejected']))->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReviews::route('/'),
            'edit'  => Pages\EditReview::route('/{record}/edit'),
        ];
    }
}
