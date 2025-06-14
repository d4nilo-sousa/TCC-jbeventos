<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Coordinator extends Model
{
    use HasFactory;

    protected $fillable = [
        'coordinator_type',
    ];

    protected function casts(): array
    {
        return [
            'temporary_password' => 'boolean',
        ];
    }

    public function events() {
        return $this->hasMany(Event::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function course() {
        return $this->belongsTo(Course::class);
    }
}
