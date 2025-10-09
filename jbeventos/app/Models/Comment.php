<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\CommentReaction;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'comment',
        'visible_comment',
        'edited_at',
        'parent_id',
        'media_path',
        'user_id',
        'event_id',
    ];

    protected $casts = [
        'visible_comment' => 'boolean',
        'edited_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONAMENTOS
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    /*
    public function mentions()
    {
        return $this->hasMany(CommentMention::class);
    } */

    public function reactions()
    {
        return $this->hasMany(CommentReaction::class);
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTODOS ÚTEIS
    |--------------------------------------------------------------------------
    */

    public function isParent(): bool
    {
        return $this->parent_id === null;
    }

    public function isEdited(): bool
    {
        return $this->edited_at !== null;
    }

    public function likesCount(): int
    {
        return $this->reactions()->where('type', 'like')->count();
    }

    public function dislikesCount(): int
    {
        return $this->reactions()->where('type', 'dislike')->count();
    }

    public function repliesCount(): int
    {
        return $this->replies()->count();
    }

    /*
    |--------------------------------------------------------------------------
    | CORREÇÃO DE CHAVE ESTRANGEIRA (ON DELETE CASCADE VIA MODEL EVENT)
    |--------------------------------------------------------------------------
    */
     /**
     * Deleta todos os filhos relacionados (reações e respostas) antes de deletar o comentário pai.
     * Isso resolve o erro 'Integrity constraint violation'.
     */
    protected static function booted()
    {
        static::deleting(function ($comment) {
            // 1. Excluir Reações do Comentário
            // O relacionamento "reactions" é o que está causando a falha de Foreign Key (1451)
            $comment->reactions()->delete();

            // 2. Excluir Menções relacionadas (opcional, dependendo de CommentMention)
            //$comment->mentions()->delete();

            // 3. Excluir Respostas (para comentários que são pais)
            //$comment->replies()->delete(); // Isso também acionará o evento de exclusão para cada resposta, garantindo que suas reações sejam excluídas
        });
    }
}