<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Auth;
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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
     protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationLabel = 'Usuarios';
    protected static ?string $pluralLabel = 'Usuarios';

    public static function form(Form $form): Form
    {
      $isOwnProfile = auth()->user() && auth()->user()->is($form->getRecord());
    $issuper_adminEditingAnothersuper_admin = auth()->user() && auth()->user()->hasRole('super_admin') 
        && $form->getRecord() && $form->getRecord()->hasRole('super_admin') && !$isOwnProfile;
    $isAdministratorEditingRestrictedUser = auth()->user() && auth()->user()->hasRole('Administrator')
        && $form->getRecord() && ($form->getRecord()->hasRole('Administrator') || $form->getRecord()->hasRole('super_admin')) && !$isOwnProfile;
    $isSubscriberEditingOtherUser = auth()->user() && auth()->user()->hasRole('Subscriber') && !$isOwnProfile;
    $isEditorEditingRestrictedUser = auth()->user() && auth()->user()->hasRole('Editor')
        && $form->getRecord() && ($form->getRecord()->hasRole('super_admin') || $form->getRecord()->hasRole('Administrator') || ($form->getRecord()->hasRole('Editor') && !$isOwnProfile));

    $isRestricted = $issuper_adminEditingAnothersuper_admin || $isAdministratorEditingRestrictedUser || $isSubscriberEditingOtherUser || $isEditorEditingRestrictedUser;

    return $form->schema([
        Section::make('Información del usuario')
            ->schema([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255)
                    ->disabled(fn () => $isRestricted),

                TextInput::make('apellidos')
                    ->label('Apellidos')
                    ->required()
                    ->maxLength(255)
                    ->disabled(fn () => $isRestricted),

                TextInput::make('edad')
                    ->label('Edad')
                    ->required()
                    ->maxLength(255)
                    ->disabled(fn () => $isRestricted),

                TextInput::make('email')
                    ->label('Correo electrónico')
                    ->required()
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->disabled(fn () => $isRestricted),

                TextInput::make('password')
                    ->label('Contraseña')
                    ->password()
                    ->required(fn (string $context) => $context === 'create')
                    ->dehydrated(fn ($state) => filled($state))
                    ->maxLength(255)
                  //  ->visibleOn('create'),
            ]),

        Section::make('Rol')
            ->schema([
                Select::make('roles')
                    ->label('Asignar rol')
                    ->multiple()
                    ->relationship('roles', 'name')
                    ->preload()
                    ->visible(fn () => auth()->user()->hasAnyRole(['super_admin', 'Administrator'])),
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
            Tables\Actions\EditAction::make()
                ->visible(function (Model $record) {
                    $authUser = auth()->user();
                    // super_admin no puede editar a otro super_admin
                    $issuper_adminEditingAnothersuper_admin = $authUser->hasRole('super_admin') && $record->hasRole('super_admin');
                    // Administrator no puede editar a otro Administrator ni a super_admin
                    $isAdministratorEditingRestrictedUser = $authUser->hasRole('Administrator') && ($record->hasRole('Administrator') || $record->hasRole('super_admin'));
                    // Subscriber solo puede editar su propio perfil
                    $isSubscriberEditingOtherUser = $authUser->hasRole('Subscriber') && !$authUser->is($record);
                    // Editor no puede editar a super_admin, Administrator ni a otro Editor
                    $isEditorEditingRestrictedUser = $authUser->hasRole('Editor') && ($record->hasRole('super_admin') || $record->hasRole('Administrator') || ($record->hasRole('Editor') && !$authUser->is($record)));
                    return !($issuper_adminEditingAnothersuper_admin || $isAdministratorEditingRestrictedUser || $isSubscriberEditingOtherUser || $isEditorEditingRestrictedUser) || $authUser->is($record);
                }),
            Tables\Actions\DeleteAction::make()
                ->visible(function (Model $record) {
                    $authUser = auth()->user();
                    // super_admin no puede eliminar a otro super_admin
                    $issuper_adminDeletingAnothersuper_admin = $authUser->hasRole('super_admin') && $record->hasRole('super_admin');
                    // Administrator no puede eliminar a otro Administrator ni a super_admin
                    $isAdministratorDeletingRestrictedUser = $authUser->hasRole('Administrator') && ($record->hasRole('Administrator') || $record->hasRole('super_admin'));
                    // Subscriber no puede eliminar a nadie
                    $isSubscriber = $authUser->hasRole('Subscriber');
                    // Editor no tiene permiso para eliminar
                    $isEditor = $authUser->hasRole('Editor');
                    return ($authUser->can('delete_user') || $authUser->can('delete_any_user'))
                        && !($issuper_adminDeletingAnothersuper_admin || $isAdministratorDeletingRestrictedUser || $isSubscriber || $isEditor);
                })
                ->disabled(function (Model $record) {
                    $authUser = auth()->user();
                    return $authUser && $authUser->is($record) && $authUser->hasAnyRole(['super_admin', 'Administrator']);
                })
                ->modalHeading('Confirmar Eliminación')
                ->modalDescription(function (Model $record) {
                    $authUser = Auth::user();
                    return $authUser && $authUser->is($record) && $authUser->hasAnyRole(['super_admin', 'Administrator'])
                        ? 'No puedes eliminarte a ti mismo como Super Admin o Administrador.'
                        : '¿Estás seguro de que deseas eliminar este usuario?';
                }),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make()
                ->visible(fn () => auth()->user()->can('delete_any_user')),
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

   

   /* Public static function canViewAny(): bool
{
    return auth()->user()?->hasAnyRole(['super_admin', 'Administrator']);
}*/
}
