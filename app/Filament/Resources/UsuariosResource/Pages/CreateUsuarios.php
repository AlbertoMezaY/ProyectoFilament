<?php

namespace App\Filament\Resources\UsuariosResource\Pages;

use App\Filament\Resources\UsuariosResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Usuarios;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegistroExitoso;


class CreateUsuarios extends CreateRecord
{
    protected static string $resource = UsuariosResource::class;

    protected function afterCreate(): void
    {
        $usuario = $this->record;

        // Envía el correo
        Mail::to($usuario->correo)->send(new RegistroExitoso($usuario->nombre));
    }
}
