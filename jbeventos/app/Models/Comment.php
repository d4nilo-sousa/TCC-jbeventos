<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory;

    /**
     * Os atributos que podem ser preenchidos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'comment',
        'visible_comment',
        'edited_at',
        'parent_id',
        'media_path',
        'user_id',
        'event_id',
    ];

    /**
     * Conversões de atributos para tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'visible_comment' => 'boolean',
        'edited_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONAMENTOS
    |--------------------------------------------------------------------------
    */

    /**
     * Comentário pertence a um usuário.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Comentário pertence a um evento.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Comentário pode ter um comentário pai (para respostas).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Comentário pode ter várias respostas (comentários filhos).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    /**
     * Comentário pode ter várias menções associadas.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mentions()
    {
        return $this->hasMany(CommentMention::class);
    }

    /**
     * Comentário pode ter várias reações associadas.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reactions()
    {
        return $this->hasMany(CommentReaction::class);
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTODOS ÚTEIS
    |--------------------------------------------------------------------------
    */

    /**
     * Verifica se o comentário é um comentário principal (sem pai).
     *
     * @return bool
     */
    public function isParent(): bool
    {
        return $this->parent_id === null;
    }

    /**
     * Verifica se o comentário foi editado.
     *
     * @return bool
     */
    public function isEdited(): bool
    {
        return $this->edited_at !== null;
    }

    /**
     * Conta quantas reações do tipo "like" o comentário possui.
     *
     * @return int
     */
    public function likesCount(): int
    {
        return $this->reactions()->where('type', 'like')->count();
    }

    /**
     * Conta quantas reações do tipo "dislike" o comentário possui.
     *
     * @return int
     */
    public function dislikesCount(): int
    {
        return $this->reactions()->where('type', 'dislike')->count();
    }

    /**
     * Conta quantas respostas (comentários filhos) o comentário possui.
     *
     * @return int
     */
    public function repliesCount(): int
    {
        return $this->replies()->count();
    }
}
