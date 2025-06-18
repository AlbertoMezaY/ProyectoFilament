<?php

namespace App\Filament\Resources\BlogResource\Pages;

use App\Filament\Resources\BlogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EditBlog extends EditRecord
{
   protected static string $resource = BlogResource::class;

    protected function beforeSave(): void
    {
        $record = $this->getRecord();
        if (auth()->check() && auth()->id() !== $record->user_id) {
            abort(403, 'No tienes permiso para editar este post.');
        }

        $data = $this->form->getState();
        $formData = $data['data'] ?? $data;

        // Actualizar el slug basado en el título
        $formData['slug'] = BlogResource::generateUniqueSlug(Str::slug($formData['titulo']), $record);
        $this->record->slug = $formData['slug'];

        Log::info('Sincronizando categorías (beforeSave):', ['categories' => $formData['categories'] ?? []]);
        Log::info('Sincronizando etiquetas (beforeSave):', ['tags' => $formData['tags'] ?? []]);

        try {
            $record->categories()->sync($formData['categories'] ?? []);
            $record->tags()->sync($formData['tags'] ?? []);
            Log::info('Sincronización completada (beforeSave)');
        } catch (\Exception $e) {
            Log::error('Error al sincronizar relaciones (beforeSave):', ['error' => $e->getMessage()]);
        }
    }

    protected function afterSave(): void
    {
        $this->redirect($this->getRedirectUrl(), navigate: true);
    }

   
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(auth()->check() && auth()->id() === $this->getRecord()->user_id),
        ];
    }


}
