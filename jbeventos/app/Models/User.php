<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'user_icon',
        'user_banner',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_number_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function createdCourses() {
        return $this->hasMany(Course::class);
    }

    public function coordinatorRole() {
        return $this->hasOne(Coordinator::class);
    }

    public function userComments() {
        return $this->hasMany(Comment::class);
    }

    public function participatingCourses() {
        return $this->belongsToMany(Course::class, 'course_user_follow')->withTimestamps();
    }

    public function eventReactions() {
        return $this->hasMany(EventUserReaction::class);
    }
}


