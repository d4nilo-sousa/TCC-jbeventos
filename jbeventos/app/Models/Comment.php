<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Coordinator extends Model
{
    use HasFactory; 

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'coordinator_type',
        'user_id',
    ];

    // Define casts para atributos específicos
    // 'temporary_password' será tratado como booleano
    protected function casts(): array
    {
        return [
            'temporary_password' => 'boolean',
        ];
    }

    // Retorna os eventos gerenciados por este coordenador
    public function managedEvents() {
        return $this->hasMany(Event::class);
    }

    // Retorna o usuário associado a este coordenador
    public function userAccount() {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Retorna o curso coordenado por este coordenador
    public function coordinatedCourse() {
        return $this->hasOne(Course::class);
    }
}