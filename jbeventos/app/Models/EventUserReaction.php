<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventUserReaction extends Model
{
    use HasFactory;

    /**
     * Nome da tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'event_user_reaction';

    /**
     * Os atributos que podem ser preenchidos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reaction_type',  // Tipo da reação (ex: like, dislike, etc)
        'user_id',        // Id do Usuário que está reagindo
        'event_id',       // Id do Evento que usuário está reagindo
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONAMENTOS
    |--------------------------------------------------------------------------
    */

    /**
     * Retorna o evento ao qual esta reação pertence.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reactedEvent()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Retorna o usuário que fez a reação.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reactingUser()
    {
        return $this->belongsTo(User::class);
    }
}