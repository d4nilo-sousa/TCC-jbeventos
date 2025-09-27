<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommentReaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'comment_id',
        'type', // like ou dislike
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

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }
}
