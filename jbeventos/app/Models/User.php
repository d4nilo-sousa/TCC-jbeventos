<?php

namespace App\Models;

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
     * Os atributos que podem ser preenchidos em massa.
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
     * Os atributos que devem ser ocultados durante a serialização.
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
     * Atributos adicionais a serem adicionados ao array e JSON do modelo.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Definição dos casts dos atributos para tipos nativos.
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

    /*
    |--------------------------------------------------------------------------
    | RELACIONAMENTOS
    |--------------------------------------------------------------------------
    */

    /**
     * Cursos criados pelo usuário.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function createdCourses()
    {
        return $this->hasMany(Course::class);
    }

    /**
     * Coordenador associado a este usuário.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function coordinator()
    {
        return $this->hasOne(Coordinator::class);
    }

    /**
     * Alias para o coordenador associado a este usuário.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function coordinatorRole()
    {
        return $this->hasOne(Coordinator::class);
    }

    /**
     * Comentários feitos pelo usuário.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userComments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Cursos que o usuário está seguindo (many-to-many).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function followedCourses()
    {
        return $this->belongsToMany(Course::class, 'course_user_follow')->withTimestamps();
    }

    /**
     * Reações feitas pelo usuário em eventos.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function eventReactions()
    {
        return $this->hasMany(EventUserReaction::class);
    }

    /*
    |--------------------------------------------------------------------------
    | ATRIBUTOS PERSONALIZADOS
    |--------------------------------------------------------------------------
    */

    /**
     * Retorna a URL da foto do usuário (icone).
     *
     * @return string
     */
    public function getUserIconUrlAttribute()
    {
        return $this->user_icon
            ? asset('storage/' . $this->user_icon)
            : asset('default-avatar.png');
    }

    /**
     * Retorna a URL do banner do usuário.
     *
     * @return string
     */
    public function getUserBannerUrlAttribute()
    {
        return $this->user_banner
            ? asset('storage/' . $this->user_banner)
            : asset('default-banner.jpg');
    }
}
