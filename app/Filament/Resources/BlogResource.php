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


class BlogResource extends Resource
{
    protected static ?string $model = Blog::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
       $record = request()->route('record');

        // Si $record es un string (ID), cargar el modelo Blog
        if (is_string($record)) {
            $record = Blog::find($record);
        }

        // $isEditable es true al crear (cuando $record es null) y solo para el autor al editar
        $isEditable = !$record || (auth()->check() && auth()->id() === $record?->user_id);

        return $form
            ->schema([
            Forms\Components\TextInput::make('titulo')
                ->required()
                ->maxLength(255)
                ->disabled(!$isEditable),
            Forms\Components\TextInput::make('slug')
                ->required()
                ->unique(Blog::class, 'slug', ignoreRecord: true)
                ->maxLength(255)
                ->disabled(!$isEditable),
            Forms\Components\Select::make('categoria')
                ->options([
                    'tecnologia' => 'Tecnología',
                    'ciencia' => 'Ciencia',
                    'arte' => 'Arte',
                    'deportes' => 'Deportes',
                    'otro' => 'Otro',
                ])
                ->required()
                ->disabled(!$isEditable),
            Forms\Components\TagsInput::make('etiquetas')
                ->placeholder('Escribe una etiqueta y presiona Enter')
                ->disabled(!$isEditable),
            Forms\Components\Textarea::make('contenido')
                ->required()
                ->columnSpanFull()
                ->disabled(!$isEditable),
            Forms\Components\TextInput::make('user.name')
                ->label('Autor')
                ->disabled(true)
                ->dehydrated(false)
                ->default(fn ($record) => $record?->user?->name ?? auth()->user()->name ?? 'Desconocido'),
            Forms\Components\FileUpload::make('imagen')
                ->image()
                ->nullable()
                ->disabled(!$isEditable),
            Forms\Components\Hidden::make('user_id')
                ->default(auth()->id()),
        ])
        ->statePath('data')
        ->model($record ?? Blog::class);
    }

    public static function table(Table $table): Table
    {
       return $table
            ->columns([
                Tables\Columns\TextColumn::make('titulo')->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('Autor'),
                Tables\Columns\ImageColumn::make('imagen')->label('Imagen'),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
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
                ->label('Guardar cambios')
                ->action(function (array $data, $record) {
                    $record->update($data);
                })
                ->button(),
            Action::make('cancel')
                ->label('Cancelar')
                ->url(fn () => static::getUrl('index'))
                ->button(),
        ] : [];
}

    public static function can(string $ability, ?Model $record = null): bool
{
    return $ability !== 'create' || auth()->check(); // Permitir ver y editar solo al autor
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
