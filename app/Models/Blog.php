<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Blog extends Model
{

    protected $fillable = ['titulo', 'slug', 'categoria', 'etiquetas', 'contenido', 'imagen', 'user_id'];

    protected $casts = [
        'etiquetas' => 'array',
    ];
    public function user()
{
    return $this->belongsTo(User::class);

    
}

protected function slug(): Attribute
    {
        return Attribute::make(
            set: fn ($value, $attributes) => $value ?? \Str::slug($attributes['titulo'])
        );
    }
}
