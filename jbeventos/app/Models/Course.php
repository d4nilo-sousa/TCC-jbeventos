<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_name',
        'course_description',
        'course_icon',
        'course_banner',
        'coordinator_id',
    ];

    public function courseCoordinator() {
        return $this->belongsTo(Coordinator::class);
    }

    public function courseCreator() {
        return $this->belongsTo(User::class);
    }

    public function courseParticipants() {
        return $this->belongsToMany(User::class, 'course_user_follow')->withTimestamps();
    }
}
