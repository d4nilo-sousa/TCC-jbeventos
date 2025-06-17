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
        'event_scheduled_at',
        'event_expired_at',
        'event_image',
        'visible_event',
    ];

    protected function casts(): array
    {
        return [
            'event_scheduled_at' => 'datetime',
            'event_expired_at' => 'datetime',
            'visible_event' => 'boolean',
        ];
    }

    public function eventComments() {
        return $this->hasMany(Comment::class);
    }

    public function eventCoordinator() {
        return $this->belongsTo(Coordinator::class);
    }

    public function eventCategories() {
        return $this->belongsToMany(Category::class, 'category_event')->withTimestamps();
    }

    public function reactions() {
        return $this->hasMany(EventUserReaction::class);
    }
}
