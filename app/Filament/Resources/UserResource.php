<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
     protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationLabel = 'Usuarios';
    protected static ?string $pluralLabel = 'Usuarios';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Información del usuario')
                ->schema([
                    TextInput::make('name')
                        ->label('Nombre')
                        ->required()
                        ->maxLength(255),

                         TextInput::make('apellidos')
                        ->label('Apellidos')
                        ->required()
                        ->maxLength(255),

                         TextInput::make('edad')
                        ->label('Edad')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('email')
                        ->label('Correo electrónico')
                        ->required()
                        ->email()
                        ->unique(ignoreRecord: true),

                    TextInput::make('password')
                        ->label('Contraseña')
                        ->password()
                        ->required(fn (string $context) => $context === 'create')
                        ->dehydrated(fn ($state) => filled($state))
                        ->maxLength(255)
                        ->visibleOn('create'),
                ]),

            Section::make('Rol')
                ->schema([
                    Select::make('roles')
                        ->label('Asignar rol')
                        ->multiple()
                        ->relationship('roles', 'name')
                        ->preload()
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            TextColumn::make('id')->sortable(),
            TextColumn::make('name')->label('Nombre')->searchable(),
            TextColumn::make('apellidos')->label('Apellidos')->searchable(),
            TextColumn::make('edad')->label('Edad')->sortable(),
            TextColumn::make('email')->label('Correo')->searchable(),
            TextColumn::make('roles.name')->label('Roles')->sortable(),
            TextColumn::make('created_at')->label('Creado')->dateTime(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    Public static function canViewAny(): bool
{
    return auth()->user()?->hasAnyRole(['SuperAdmin', 'Administrator']);
}
}
