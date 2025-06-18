<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogResource\Pages;
use App\Filament\Resources\BlogResource\RelationManagers;
use App\Models\Blog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Hidden;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BlogResource extends Resource
{
    protected static ?string $model = Blog::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'BLOG'; // Agrupa en "BLOG"
    protected static ?string $navigationLabel = 'Entradas';
    
    

    

    public static function form(Form $form): Form
    {
        $record = request()->route('record');

        if (is_string($record)) {
            $record = Blog::find($record);
        }

        $isEditable = !$record || (auth()->check() && auth()->id() === $record?->user_id);
        $isEditing = $record !== null;

        return $form
            ->schema([
                Forms\Components\TextInput::make('titulo')
                    ->label(__('blog.titulo'))
                    ->required()
                    ->maxLength(255)
                    ->disabled(!$isEditable)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) use ($record) {
                        $slug = Str::slug($state);
                        $set('slug', static::generateUniqueSlug($slug, $record));
                    }),
                Forms\Components\TextInput::make('slug')
                    ->label(__('blog.slug'))
                    ->required()
                    ->maxLength(255)
                    ->disabled($isEditing || !$isEditable) // Deshabilitado en modo edición
                    ->dehydrated()
                    ->default(fn ($record) => $record?->slug ?? static::generateUniqueSlug(Str::slug($record?->titulo ?? ''), $record))
                    ->rules([
                        Rule::unique(Blog::class, 'slug')->ignore($record?->id),
                    ]),
                Forms\Components\Select::make('categories')
                    ->label(__('blog.categoria'))
                    ->multiple()
                    ->relationship('categories', 'name')
                    ->required()
                    ->preload()
                    ->dehydrated()
                    ->disabled(!$isEditable),
                Forms\Components\Select::make('tags')
                    ->label(__('blog.etiqueta'))
                    ->multiple()
                    ->relationship('tags', 'name')
                    ->preload()
                    ->dehydrated()
                    ->disabled(!$isEditable),
                Forms\Components\Textarea::make('contenido')
                    ->label(__('blog.contenido'))
                    ->required()
                    ->columnSpanFull()
                    ->disabled(!$isEditable),
                Forms\Components\Select::make('user_id')
                    ->label(__('blog.autor'))
                    ->options(\App\Models\User::all()->pluck('name', 'id'))
                    ->required()
                    ->disabled(!$isEditable)
                    ->default(fn ($record) => $record?->user_id ?? auth()->id()),
                Forms\Components\FileUpload::make('imagen')
                    ->label(__('blog.imagen'))
                    ->image()
                    ->disk('public') // Asegúrate de que el disco sea 'public'
                    ->directory('blog_images') // Carpeta donde se guardan las imágenes
                    ->nullable()
                    ->disabled(!$isEditable),
            ])
            ->statePath('data')
            ->model($record ?? Blog::class);
    }

    // Método estático para generar un slug único, ahora fuera de form()
    public static function generateUniqueSlug($slug, $record = null)
    {
        $originalSlug = $slug;
        $count = 1;

        while (Blog::where('slug', $slug)->where('id', '!=', $record?->id ?? 0)->exists()) {
            $slug = "{$originalSlug}-" . $count++;
        }

        return $slug;
    }

    public static function table(Table $table): Table
    {
       return $table
        ->columns([
            Tables\Columns\TextColumn::make('titulo')->sortable()->label( __('blog.titulo') ),
            Tables\Columns\TextColumn::make('slug')->sortable()->label( __('blog.slug') ),
            Tables\Columns\TextColumn::make('categories.name')->label(__('blog.categoria')),
            Tables\Columns\TagsColumn::make('tags.name')->label(__('blog.etiqueta')),
            Tables\Columns\TextColumn::make('user.name')->label(__('blog.autor')),
            Tables\Columns\ImageColumn::make('imagen')->label(__('blog.imagen')),
            Tables\Columns\TextColumn::make('created_at')->dateTime() ->label(__('blog.creado_en')),
        ])
        ->filters([
            //
        ])
        ->actions([
            Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make()
                ->visible(fn ($record) => auth()->check() && auth()->id() === $record->user_id),
            Tables\Actions\DeleteAction::make()
                ->visible(fn ($record) => auth()->check() && auth()->id() === $record->user_id),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make()
                ->visible(function ($records) {
                    return $records && $records->every(fn ($record) => auth()->check() && auth()->id() === $record->user_id);
                }),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getActions(): array
{
    $record = request()->route('record');

        // Si $record es un string (ID), cargar el modelo Blog
        if (is_string($record)) {
            $record = Blog::find($record);
        }

        $isEditable = $record && auth()->check() && auth()->id() === $record->user_id;

        return $isEditable ? [
            Action::make('save')
                ->label(__('blog.guardar_cab'))
                ->action(function (array $data, $record) {
                    $record->update($data);
                })
                ->button(),
            Action::make('cancel')
                ->label(__('blog.cancelar'))
                ->url(fn () => static::getUrl('index'))
                ->button(),
        ] : [];
}

    public static function can(string $ability, ?Model $record = null): bool
    {
        if (!auth()->check()) {
            return false;
        }

        $user = auth()->user();

        return match ($ability) {
            'view_any' => $user->hasPermissionTo('view_any_blog'),
            'view' => $user->hasPermissionTo('view_blog'),
            'create' => $user->hasPermissionTo('create_blog'),
            'update' => $user->hasPermissionTo('update_blog'),
            'delete' => $user->hasPermissionTo('delete_blog'),
            default => true,
        };
    }

    public static function getEloquentQuery(): Builder
{
    return parent::getEloquentQuery();
}

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogs::route('/'),
            'create' => Pages\CreateBlog::route('/create'),
            'edit' => Pages\EditBlog::route('/{record}/edit'),
        ];
    }

    
}
