<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_name',
        'event_description',
        'event_location',
        'event_start',
        'event_expired_at',
        'event_image',
        'visible_event',
    ];

    protected function casts(): array
    {
        return [
            'event_start' => 'datetime',
            'event_expired_at' => 'datetime',
            'visible_event' => 'boolean',
        ];
    }

    public function comments() {
        return $this->hasMany(Comment::class);
    }

    public function coordinator() {
        return $this->belongsTo(Coordinator::class);
    }
}
