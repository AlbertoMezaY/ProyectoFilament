<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class usuarios extends Model
{
    /** @use HasFactory<\Database\Factories\UsuariosFactory> */
    use HasFactory;

    protected $table = 'usuarios'; //

    protected $fillable = [
        'nombre',
        'apellidos',
        'edad',
        'correo',
        'contrasenia'
    ];
}
