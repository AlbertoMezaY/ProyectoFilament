<?php

namespace App\Filament\Resources\BlogResource\Pages;

use App\Filament\Resources\BlogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBlog extends EditRecord
{
   protected static string $resource = BlogResource::class;

    protected function beforeSave(): void
    {
        $record = $this->getRecord();
        if (auth()->check() && auth()->id() !== $record->user_id) {
            abort(403, 'No tienes permiso para editar este post.');
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(auth()->check() && auth()->id() === $this->getRecord()->user_id),
        ];
    }


}
