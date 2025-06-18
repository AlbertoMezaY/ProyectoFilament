<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = ['titulo', 'slug', 'contenido', 'imagen', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'blog_category', 'blog_id', 'category_id')
                    ->withTimestamps();
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'blog_tag', 'blog_id', 'tag_id')
                    ->withTimestamps();
    }

    protected function slug(): Attribute
    {
        return Attribute::make(
            set: fn ($value, $attributes) => $value ?? \Str::slug($attributes['titulo'])
        );
    }
}
