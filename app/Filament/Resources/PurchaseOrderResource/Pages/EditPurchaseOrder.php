<?php

namespace App\Filament\Resources\PurchaseOrderResource\Pages;

use App\Filament\Resources\PurchaseOrderResource;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Services\InventoryService;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\EditRecord;

class EditPurchaseOrder extends EditRecord
{
    protected static string $resource = PurchaseOrderResource::class;

    protected function getHeaderActions(): array
    {
        $record = $this->getRecord();

        return [
            Actions\Action::make('receivePartial')
                ->label('Receive Items')
                ->icon('heroicon-o-arrow-down-on-square-stack')
                ->color('primary')
                ->visible(fn () => in_array($record->status, ['draft', 'ordered', 'partial']))
                ->modalHeading(fn () => 'Receive items for PO #' . $record->po_number)
                ->modalDescription('Enter the quantity you physically received for each line. Blank or 0 means "not received this time".')
                ->modalWidth('4xl')
                ->form(function () use ($record) {
                    // Build a Repeater-style list: one row per PO item, pre-filled with
                    // its outstanding qty so "accept all" is just a single click through.
                    $fields = [];
                    foreach ($record->items as $item) {
                        $outstanding = $item->outstanding();
                        $label = ($item->product?->sku ? "[{$item->product->sku}] " : '') . ($item->product?->name ?? 'Unknown');

                        $fields[] = Forms\Components\Grid::make(4)->schema([
                            Forms\Components\Placeholder::make("label_{$item->id}")
                                ->label('Item')
                                ->content($label)
                                ->columnSpan(2),
                            Forms\Components\Placeholder::make("outstanding_{$item->id}")
                                ->label('Outstanding')
                                ->content("{$outstanding} / {$item->quantity_ordered}")
                                ->columnSpan(1),
                            Forms\Components\TextInput::make("receive_{$item->id}")
                                ->label('Receive now')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue($outstanding)
                                ->default($outstanding)
                                ->disabled($outstanding <= 0)
                                ->columnSpan(1),
                        ]);
                    }
                    return $fields;
                })
                ->action(function (array $data) use ($record) {
                    foreach ($record->items as $item) {
                        $qty = (int) ($data["receive_{$item->id}"] ?? 0);
                        if ($qty > 0) {
                            InventoryService::receivePurchaseOrderItem($item, $qty);
                        }
                    }
                    $record->refresh()->recomputeStatus();
                    \Filament\Notifications\Notification::make()
                        ->title('Stock received')
                        ->body('The selected items have been added to inventory.')
                        ->success()->send();
                    $this->redirect(static::getResource()::getUrl('edit', ['record' => $record->refresh()]));
                }),

            Actions\Action::make('receiveAll')
                ->label('Receive All Outstanding')
                ->icon('heroicon-o-arrow-down-on-square')
                ->color('success')
                ->visible(fn () => in_array($record->status, ['draft', 'ordered', 'partial']))
                ->requiresConfirmation()
                ->modalHeading('Receive all outstanding items?')
                ->modalDescription('Stock will be added for every item with an outstanding quantity. Cost prices update using weighted average.')
                ->action(function () use ($record) {
                    foreach ($record->items as $item) {
                        InventoryService::receivePurchaseOrderItem($item);
                    }
                    $record->refresh()->recomputeStatus();
                    $this->redirect(static::getResource()::getUrl('edit', ['record' => $record->refresh()]));
                }),

            Actions\Action::make('cancel')
                ->label('Cancel PO')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn () => !in_array($record->status, ['received', 'cancelled']))
                ->requiresConfirmation()
                ->action(fn () => $record->update(['status' => 'cancelled'])),

            Actions\DeleteAction::make()->visible(fn () => $record->status !== 'received'),
        ];
    }

    protected function afterSave(): void
    {
        $this->record->recomputeTotals();
    }
}
