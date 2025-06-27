<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventUserReaction extends Model
{
    use HasFactory;

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'reaction_type',  // Tipo da reação (ex: like, love, etc)
    ];

    // Retorna o evento ao qual esta reação pertence
    public function reactedEvent() {
        return $this->belongsTo(Event::class);
    }

    // Retorna o usuário que fez a reação
    public function reactingUser() {
        return $this->belongsTo(User::class);
    }
}
