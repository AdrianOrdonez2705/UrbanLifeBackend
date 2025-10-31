<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Maps to your 'usuario' table.
 */
class Usuario extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $table = 'usuario';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false;

    // These attributes should be hidden from JSON responses
    protected $hidden = [
        'contrasenia',
    ];

    /**
     * This maps your 'contrasenia' column to Laravel's password system.
     */
    public function getAuthPassword()
    {
        return $this->contrasenia;
    }

    //
    // --- METHODS REQUIRED BY JWTSubject INTERFACE ---
    //

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     * (This will be 'id_usuario')
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     * (We don't need any custom claims right now).
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}

