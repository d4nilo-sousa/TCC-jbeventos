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

    public function managedEvents() {
        return $this->hasMany(Event::class);
    }

    public function userAccount() {
        return $this->belongsTo(User::class);
    }

    public function coordinatedCourse() {
        return $this->belongsTo(Course::class);
    }
}
