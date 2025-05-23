<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
           ->visible(function () {
                    $authUser = Auth::user();
                    // SuperAdmin no puede eliminar a otro SuperAdmin
                    $isSuperAdminDeletingAnotherSuperAdmin = $authUser->hasRole('SuperAdmin') && $this->record->hasRole('SuperAdmin');
                    // Administrator no puede eliminar a otro Administrator ni a SuperAdmin
                    $isAdministratorDeletingRestrictedUser = $authUser->hasRole('Administrator') && ($this->record->hasRole('Administrator') || $this->record->hasRole('SuperAdmin'));
                    // Subscriber no puede eliminar a nadie
                    $isSubscriber = $authUser->hasRole('Subscriber');
                    // Editor no tiene permiso para eliminar
                    $isEditor = $authUser->hasRole('Editor');
                    return ($authUser->can('delete_user') || $authUser->can('delete_any_user'))
                        && !($isSuperAdminDeletingAnotherSuperAdmin || $isAdministratorDeletingRestrictedUser || $isSubscriber || $isEditor);
                })
                ->disabled(function () {
                    $authUser = Auth::user();
                    return $authUser && $authUser->is($this->record) && $authUser->hasAnyRole(['SuperAdmin', 'Administrator']);
                })
                ->modalHeading('Confirmar Eliminación')
                ->modalDescription(function () {
                    $authUser = Auth::user();
                    return $authUser && $authUser->is($this->record) && $authUser->hasAnyRole(['SuperAdmin', 'Administrator'])
                        ? 'No puedes eliminarte a ti mismo como Super Admin o Administrador.'
                        : '¿Estás seguro de que deseas eliminar este usuario?';
                }),
        ];
    }

    protected function authorizeAccess(): void
    {
      $authUser = Auth::user();
        // SuperAdmin no puede editar a otro SuperAdmin
        $isSuperAdminEditingAnotherSuperAdmin = $authUser->hasRole('SuperAdmin') && $this->record->hasRole('SuperAdmin') && !$authUser->is($this->record);
        // Administrator no puede editar a otro Administrator ni a SuperAdmin
        $isAdministratorEditingRestrictedUser = $authUser->hasRole('Administrator') && ($this->record->hasRole('Administrator') || $this->record->hasRole('SuperAdmin')) && !$authUser->is($this->record);
        // Subscriber solo puede editar su propio perfil
        $isSubscriberEditingOtherUser = $authUser->hasRole('Subscriber') && !$authUser->is($this->record);
        // Editor no puede editar a SuperAdmin, Administrator ni a otro Editor
        $isEditorEditingRestrictedUser = $authUser->hasRole('Editor') && ($this->record->hasRole('SuperAdmin') || $this->record->hasRole('Administrator') || ($this->record->hasRole('Editor') && !$authUser->is($this->record)));
        abort_unless(
            !($isSuperAdminEditingAnotherSuperAdmin || $isAdministratorEditingRestrictedUser || $isSubscriberEditingOtherUser || $isEditorEditingRestrictedUser),
            403,
            'No tienes permiso para editar este usuario.'
        );
    }
}
