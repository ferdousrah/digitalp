<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        $record = $this->getRecord();

        return [
            Actions\Action::make('printInvoice')
                ->label('Print Invoice')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url(fn () => route('admin.orders.invoice', $record))
                ->openUrlInNewTab(),

            Actions\Action::make('printLabel')
                ->label('Print Courier Label')
                ->icon('heroicon-o-ticket')
                ->color('gray')
                ->url(fn () => route('admin.orders.label', $record))
                ->openUrlInNewTab(),

            Actions\ActionGroup::make([
                Actions\Action::make('markProcessing')
                    ->label('Mark as Processing')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->visible(fn () => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(fn () => $record->update(['status' => 'processing'])),
                Actions\Action::make('markShipped')
                    ->label('Mark as Shipped')
                    ->icon('heroicon-o-truck')
                    ->visible(fn () => in_array($record->status, ['pending', 'processing']))
                    ->form([
                        Forms\Components\Select::make('courier_service')
                            ->label('Courier')
                            ->options(Order::couriers())
                            ->required()
                            ->default($record->courier_service),
                        Forms\Components\TextInput::make('tracking_number')
                            ->label('Tracking number')
                            ->required()
                            ->default($record->tracking_number)
                            ->maxLength(100),
                    ])
                    ->action(fn (array $data) => $record->update([
                        'status'          => 'shipped',
                        'courier_service' => $data['courier_service'],
                        'tracking_number' => $data['tracking_number'],
                    ])),
                Actions\Action::make('markDelivered')
                    ->label('Mark as Delivered')
                    ->icon('heroicon-o-check-badge')
                    ->visible(fn () => $record->status === 'shipped')
                    ->requiresConfirmation()
                    ->action(fn () => $record->update(['status' => 'delivered', 'payment_status' => 'paid'])),
                Actions\Action::make('markPaid')
                    ->label('Mark as Paid')
                    ->icon('heroicon-o-banknotes')
                    ->visible(fn () => $record->payment_status !== 'paid')
                    ->requiresConfirmation()
                    ->action(fn () => $record->update(['payment_status' => 'paid'])),
                Actions\Action::make('cancel')
                    ->label('Cancel Order')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn () => !in_array($record->status, ['delivered', 'cancelled', 'refunded']))
                    ->requiresConfirmation()
                    ->action(fn () => $record->update(['status' => 'cancelled'])),
                Actions\Action::make('refund')
                    ->label('Record Refund')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('danger')
                    ->visible(fn () => $record->payment_status === 'paid')
                    ->form([
                        Forms\Components\TextInput::make('refund_amount')
                            ->numeric()
                            ->prefix('৳')
                            ->required()
                            ->default($record->total)
                            ->minValue(0)
                            ->maxValue($record->total),
                    ])
                    ->action(fn (array $data) => $record->update([
                        'refund_amount'   => $data['refund_amount'],
                        'refunded_at'     => now(),
                        'payment_status'  => 'refunded',
                        'status'          => 'refunded',
                    ])),
                Actions\Action::make('addNote')
                    ->label('Add Internal Note')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->form([
                        Forms\Components\Textarea::make('note')->required()->rows(3),
                    ])
                    ->action(function (array $data) use ($record) {
                        $record->logActivity(
                            event: 'note_added',
                            title: 'Note added',
                            description: $data['note'],
                        );
                    }),
            ])
                ->label('Actions')
                ->icon('heroicon-m-bolt')
                ->color('primary')
                ->button(),

            Actions\EditAction::make(),
        ];
    }
}
