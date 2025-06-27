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

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'user_icon',
        'user_banner',
        'user_type',
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

    // Cursos criados pelo usuário
    public function createdCourses() {
        return $this->hasMany(Course::class);
    }

    // Retorna o coordenador associado a este usuário
    public function coordinatorRole() {
        return $this->hasOne(Coordinator::class);
    }

    // Comentários feitos pelo usuário
    public function userComments() {
        return $this->hasMany(Comment::class);
    }

    // Cursos que o usuário está participando
    // Relação muitos-para-muitos com a tabela pivot 'course_user_follow'
    // withTimestamps() mantém os timestamps na tabela pivot atualizados automaticamente
    public function participatingCourses() {
        return $this->belongsToMany(Course::class, 'course_user_follow')->withTimestamps();
    }

    // Reações feitas pelo usuário em eventos
    // Essa relação usa uma tabela pivot com atributos próprios (EventUserReaction)
    public function eventReactions() {
        return $this->hasMany(EventUserReaction::class);
    }
}


