<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventUserReaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'reaction_type',
    ];

    public function reactedEvent() {
        return $this->belongsTo(Event::class);
    }

    public function reactingUser() {
        return $this->belongsTo(User::class);
    }
}
