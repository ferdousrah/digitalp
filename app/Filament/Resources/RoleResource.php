<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Roles & Permissions';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        // Build a map of "resource-group label" => [permission-id => human action name]
        $groups = static::groupedPermissions();

        return $form->schema([
            Forms\Components\Section::make('Role Details')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Role Name')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->helperText('Use lowercase with underscores (e.g., content_editor).')
                        ->maxLength(125),
                    Forms\Components\TextInput::make('guard_name')
                        ->default('web')
                        ->disabled()
                        ->dehydrated()
                        ->required(),
                ]),

            Forms\Components\Section::make('Permissions')
                ->description('Tick the actions this role is allowed to perform. Super Admin bypasses all checks automatically.')
                ->schema(
                    collect($groups)->map(function (array $perms, string $label) {
                        return Forms\Components\CheckboxList::make("permission_group_{$label}")
                            ->label($label)
                            ->options($perms)
                            ->columns(4)
                            ->bulkToggleable()
                            ->dehydrated(false); // handled manually in afterSave
                    })->values()->toArray()
                ),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'super_admin' => 'danger',
                        'admin'       => 'warning',
                        default       => 'primary',
                    }),
                Tables\Columns\TextColumn::make('permissions_count')
                    ->counts('permissions')
                    ->label('# Permissions'),
                Tables\Columns\TextColumn::make('users_count')
                    ->counts('users')
                    ->label('# Users'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn (Model $record) => in_array($record->name, ['super_admin', 'admin'])),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit'   => Pages\EditRole::route('/{record}/edit'),
        ];
    }

    /**
     * Build a map of ["Resource Label" => [permission_id => "Action"]]
     * from whatever permissions exist in the database. Keeps the form
     * dynamic — new permissions appear automatically.
     */
    public static function groupedPermissions(): array
    {
        $labels = [
            'products'         => 'Products',
            'categories'       => 'Categories',
            'brands'           => 'Brands',
            'attributes'       => 'Attributes',
            'orders'           => 'Orders',
            'blog_posts'       => 'Blog Posts',
            'blog_categories'  => 'Blog Categories',
            'pages'            => 'Pages',
            'faqs'             => 'FAQs',
            'faq_categories'   => 'FAQ Categories',
            'services'         => 'Services',
            'gallery_albums'   => 'Gallery Albums',
            'testimonials'     => 'Testimonials',
            'sliders'          => 'Sliders',
            'newsletter'       => 'Newsletter Subscribers',
            'contacts'         => 'Contact Submissions',
            'company_timeline' => 'Company Timeline',
            'menu_items'       => 'Menu Items',
            'home_sections'    => 'Homepage Sections',
            'site_contents'    => 'Site Contents',
            'settings'         => 'General Settings',
            'template'         => 'Template / Colors',
            'product_card'     => 'Product Card Settings',
            'payment'          => 'Payment Settings',
            'font'             => 'Font Settings',
            'hero_layout'      => 'Hero Layout',
            'users'            => 'Users',
            'roles'            => 'Roles & Permissions',
        ];

        $groups = [];
        foreach (Permission::orderBy('name')->get() as $perm) {
            [$resource, $action] = array_pad(explode('.', $perm->name, 2), 2, '');
            $label = $labels[$resource] ?? ucfirst(str_replace('_', ' ', $resource));
            $groups[$label][$perm->id] = ucfirst($action);
        }
        ksort($groups);
        return $groups;
    }
}
