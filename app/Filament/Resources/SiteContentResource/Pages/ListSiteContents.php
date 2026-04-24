<?php

namespace App\Filament\Resources\SiteContentResource\Pages;

use App\Filament\Resources\SiteContentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;

class ListSiteContents extends ListRecords
{
    protected static string $resource = SiteContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->icon('heroicon-m-squares-2x2'),

            'navbar' => Tab::make('Navbar')
                ->icon('heroicon-m-bars-3')
                ->query(fn ($query) => $query->where('page', 'navbar')),

            'footer' => Tab::make('Footer')
                ->icon('heroicon-m-building-storefront')
                ->query(fn ($query) => $query->where('page', 'footer')),

            'home' => Tab::make('Home')
                ->icon('heroicon-m-home')
                ->query(fn ($query) => $query->where('page', 'home')),

            'products' => Tab::make('Products')
                ->icon('heroicon-m-shopping-bag')
                ->query(fn ($query) => $query->where('page', 'products')),

            'cart' => Tab::make('Cart')
                ->icon('heroicon-m-shopping-cart')
                ->query(fn ($query) => $query->where('page', 'cart')),

            'checkout' => Tab::make('Checkout')
                ->icon('heroicon-m-credit-card')
                ->query(fn ($query) => $query->where('page', 'checkout')),

            'contact' => Tab::make('Contact')
                ->icon('heroicon-m-envelope')
                ->query(fn ($query) => $query->where('page', 'contact')),

            'common' => Tab::make('Common')
                ->icon('heroicon-m-globe-alt')
                ->query(fn ($query) => $query->where('page', 'common')),

            'track' => Tab::make('Track Order')
                ->icon('heroicon-m-truck')
                ->query(fn ($query) => $query->where('page', 'track')),
        ];
    }
}
