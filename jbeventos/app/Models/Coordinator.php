<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Coordinator extends Model
{
    use HasFactory;

    /**
     * Os atributos que podem ser preenchidos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'coordinator_type',
        'temporary_password',
        'user_id',
    ];

    /**
     * Define o casting dos atributos do modelo.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'temporary_password' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELACIONAMENTOS
    |--------------------------------------------------------------------------
    */

    /**
     * Retorna os eventos gerenciados por este coordenador.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function managedEvents()
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Retorna o usuário associado a este coordenador.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userAccount()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Retorna o curso coordenado por este coordenador.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function coordinatedCourse()
    {
        return $this->hasOne(Course::class);
    }
}
