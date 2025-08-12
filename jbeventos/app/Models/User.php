<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'phone_number',
        'phone_number_verified_at',
        'user_icon',
        'user_banner',
        'bio',
        'user_type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
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
    public function coordinator() {
        return $this->hasOne(Coordinator::class);
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
    public function followedCourses() {
        return $this->belongsToMany(Course::class, 'course_user_follow')->withTimestamps();
    }

    // Reações feitas pelo usuário em eventos
    // Essa relação usa uma tabela pivot com atributos próprios (EventUserReaction)
    public function eventReactions() {
        return $this->hasMany(EventUserReaction::class);
    }

    // Atributos personalizados
    public function getUserIconUrlAttribute()
    {
    return $this->user_icon
        ? asset('storage/' . $this->user_icon)
        : asset('default-avatar.png');
    }

    public function getUserBannerUrlAttribute()
    {
    return $this->user_banner
        ? asset('storage/' . $this->user_banner)
        : asset('default-banner.jpg');
    }       

}

