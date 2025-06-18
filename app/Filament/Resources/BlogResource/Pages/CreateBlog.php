<?php

namespace App\Filament\Resources\BlogResource\Pages;

use App\Filament\Resources\BlogResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CreateBlog extends CreateRecord
{
    protected static string $resource = BlogResource::class;

    
     protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id(); // Asigna el ID del usuario autenticado
        return $data;
    }

    public function getTitle(): string
    {
        return __('blog.titulo_cre'); // Cambia "Crear Blog" por "Nueva Entrada"
    }

   protected function beforeSave(): void
    {
        $data = $this->form->getState();
        $formData = $data['data'] ?? $data;

        // Actualizar el slug basado en el título
        $formData['slug'] = BlogResource::generateUniqueSlug(Str::slug($formData['titulo']), null);
        $this->record->slug = $formData['slug'];

        Log::info('Sincronizando categorías (beforeSave):', ['categories' => $formData['categories'] ?? []]);
        Log::info('Sincronizando etiquetas (beforeSave):', ['tags' => $formData['tags'] ?? []]);

        try {
            $this->record->categories()->sync($formData['categories'] ?? []);
            $this->record->tags()->sync($formData['tags'] ?? []);
            Log::info('Sincronización completada (beforeSave)');
        } catch (\Exception $e) {
            Log::error('Error al sincronizar relaciones (beforeSave):', ['error' => $e->getMessage()]);
        }
    }

    protected function afterCreate(): void
    {
        $this->redirect($this->getRedirectUrl(), navigate: true);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index'); // Redirige al listado después de crear
    }
}
