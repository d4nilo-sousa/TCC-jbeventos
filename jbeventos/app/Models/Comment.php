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
    ];

    protected function casts(): array
    {
        return [
            'visible_comment' => 'boolean',
        ];
    }

    public function commentUser() {
        return $this->belongsTo(User::class);
    }

    public function commentedEvent() {
        return $this->belongsTo(Event::class);
    }
}
