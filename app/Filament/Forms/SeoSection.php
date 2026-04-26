<?php

namespace App\Filament\Forms;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\View as ViewComponent;
use Filament\Forms\Get;

/**
 * Reusable SEO meta section — drop into any Filament resource form.
 *
 *   SeoSection::make()
 *       ->titleFallback('name')          // form field whose value is shown as placeholder + preview if meta_title is empty
 *       ->descriptionFallback('short_description')
 *       ->slugField('slug')
 */
class SeoSection
{
    public static function make(): Section
    {
        return Section::make('SEO')
            ->description('Search engine visibility — how this record appears on Google, Facebook, etc.')
            ->icon('heroicon-o-magnifying-glass')
            ->collapsed()
            ->columnSpanFull()
            ->schema([
                TextInput::make('meta_title')
                    ->label('Meta Title')
                    ->maxLength(80)
                    ->live(debounce: 300)
                    ->helperText(function ($state) {
                        $len = mb_strlen((string) $state);
                        if ($len === 0) return '0 / 60 chars — leave blank to use the record name automatically';
                        if ($len < 30)  return "{$len} / 60 chars — too short. Aim for 50–60 chars.";
                        if ($len <= 60) return "✓ {$len} / 60 chars — good length.";
                        if ($len <= 70) return "⚠ {$len} / 60 chars — Google may truncate.";
                        return "✗ {$len} / 60 chars — will be cut off in search results.";
                    }),

                Textarea::make('meta_description')
                    ->label('Meta Description')
                    ->rows(3)
                    ->maxLength(200)
                    ->live(debounce: 500)
                    ->helperText(function ($state) {
                        $len = mb_strlen((string) $state);
                        if ($len === 0)  return '0 / 160 chars — leave blank to auto-generate from content';
                        if ($len < 80)   return "{$len} / 160 chars — too short. Add detail.";
                        if ($len <= 160) return "✓ {$len} / 160 chars — good length.";
                        if ($len <= 180) return "⚠ {$len} / 160 chars — Google may truncate.";
                        return "✗ {$len} / 160 chars — will be cut off.";
                    }),

                ViewComponent::make('filament.forms.seo-preview')
                    ->columnSpanFull(),
            ]);
    }
}
