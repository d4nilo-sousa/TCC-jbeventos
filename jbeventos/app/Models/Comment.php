<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public function mentions()
    {
        return $this->hasMany(CommentMention::class);
    }

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
}