<?php

namespace App\Filament\Concerns;

use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\Column;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Adds a "Export CSV" bulk action to any Filament table. It reads the
 * currently rendered table columns (so the export matches what the user sees)
 * and streams a CSV of the selected rows.
 *
 * Usage:
 *   use App\Filament\Concerns\ExportsToCsv;
 *   In ->bulkActions([...]) array, call static::csvExportBulkAction().
 */
trait ExportsToCsv
{
    public static function csvExportBulkAction(): BulkAction
    {
        return BulkAction::make('exportCsv')
            ->label('Export CSV')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('gray')
            ->action(function (Collection $records, $livewire) {
                $columns = collect($livewire->getTable()->getColumns())
                    ->filter(fn (Column $c) => !$c->isHidden())
                    ->values();

                $filename = strtolower(class_basename(static::class)) . '-' . now()->format('Ymd-His') . '.csv';

                return new StreamedResponse(function () use ($records, $columns) {
                    $out = fopen('php://output', 'w');
                    // UTF-8 BOM for Excel compatibility
                    fprintf($out, "\xEF\xBB\xBF");

                    fputcsv($out, $columns->map(fn (Column $c) => $c->getLabel())->all());

                    foreach ($records as $record) {
                        $row = [];
                        foreach ($columns as $col) {
                            $col->record($record);
                            $val = $col->getState();
                            if (is_array($val) || is_object($val)) $val = json_encode($val);
                            $row[] = strip_tags((string) $val);
                        }
                        fputcsv($out, $row);
                    }
                    fclose($out);
                }, 200, [
                    'Content-Type'        => 'text/csv; charset=UTF-8',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                ]);
            })
            ->deselectRecordsAfterCompletion();
    }
}
