<?php

namespace App\Filament\Resources\TagResource\Pages;

use App\Filament\Resources\TagResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTag extends CreateRecord
{
    public function getTitle(): string
    {
        return __('blog.crear_etiqueta'); // Cambia "Crear Blog" por "Nueva Entrada"
    }

    protected static string $resource = TagResource::class;
}
