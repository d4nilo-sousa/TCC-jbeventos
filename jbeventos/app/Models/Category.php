<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_name',
    ];

    public function relatedEvents() {
        return $this->belongsToMany(Event::class, 'category_event')->withTimestamps();
    }
}
