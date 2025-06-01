<?php

namespace App\Filament\Resources\BlogResource\Pages;

use App\Filament\Resources\BlogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;


class ListBlogs extends ListRecords
{
    protected static string $resource = BlogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label(__('blog.crear_pub'))
        ];
    }

    public function getTitle(): string
    {
        return __('blog.post'); // Cambia "Crear Blog" por "Nueva Entrada"
    }


}
