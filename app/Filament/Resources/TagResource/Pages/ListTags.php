<?php

namespace App\Filament\Resources\TagResource\Pages;

use App\Filament\Resources\TagResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTags extends ListRecords
{
    protected static string $resource = TagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label(__('blog.crear_etiqueta'))
        ];
    }

    public function getTitle(): string
    {
        return __('blog.titulo_etiqueta'); // Cambia "Crear Blog" por "Nueva Entrada"
    }
}
