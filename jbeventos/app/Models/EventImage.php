<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'image_path',
        'event_id',
    ];

    public function event() 
    {
        return $this->belongsTo(Event::class);
    }
}
