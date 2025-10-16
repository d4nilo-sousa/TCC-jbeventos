<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;
use App\Notifications\CustomResetPasswordNotification;

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
        'user_icon',
        'user_icon_default',
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
            'password' => 'hashed',
        ];
    }

    // Cursos criados pelo usuário
    public function createdCourses()
    {
        return $this->hasMany(Course::class);
    }

    // Retorna o coordenador associado a este usuário
    public function coordinator()
    {
        return $this->hasOne(Coordinator::class);
    }

    // Retorna o coordenador associado a este usuário
    public function coordinatorRole()
    {
        return $this->hasOne(Coordinator::class);
    }

    // Comentários feitos pelo usuário
    public function userComments()
    {
        return $this->hasMany(Comment::class);
    }

    public function savedEvents(){
        return $this->belongsToMany(Event::class, 'event_user_reaction')
                                    ->wherePivot('reaction_type', 'save')
                                    ->withTimestamps();
    }

    // Cursos que o usuário está participando
    // Relação muitos-para-muitos com a tabela pivot 'course_user_follow'
    // withTimestamps() mantém os timestamps na tabela pivot atualizados automaticamente
    public function followedCourses()
    {
        return $this->belongsToMany(Course::class, 'course_user_follow')->withTimestamps();
    }

    // Posts feitos pelo usuário
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    // Respostas feitas pelo usuário
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }


    // Reações feitas pelo usuário em eventos
    // Essa relação usa uma tabela pivot com atributos próprios (EventUserReaction)
    public function eventReactions()
    {
        return $this->hasMany(EventUserReaction::class);
    }

    //reações do usuário em comentários
    public function commentReactions(){
        return $this->hasMany(CommentReaction::class);
    }

    // Atributos personalizados
    public function getUserIconUrlAttribute()
    {
        if ($this->user_icon_default) {
            // Retorna o ícone padrão escolhido
            return asset('imgs/' . $this->user_icon_default);
        }

        if ($this->user_icon) {
            // Verifica se o arquivo de upload realmente existe
            if (Storage::disk('public')->exists($this->user_icon)) {
                return asset('storage/' . $this->user_icon);
            }
        }

        // Retorna o ícone padrão genérico se nada for encontrado
        return asset('imgs/avatar_default_1.svg');
        return $this->user_icon
            ? asset('storage/' . $this->user_icon)
            : asset('default-avatar.png');
    }

    public function getUserBannerUrlAttribute()
    {
        if ($this->user_banner) {
            // Se for uma cor hexadecimal
            if (preg_match('/^#[a-f0-9]{6}$/i', $this->user_banner)) {
                return $this->user_banner;
            }

            // Se for um caminho de arquivo
            if (Storage::disk('public')->exists($this->user_banner)) {
                return asset('storage/' . $this->user_banner);
            }

            // Se o valor está definido mas não é válido, retorna banner default
            return asset('default-banner.jpg');
        }

        // Se não houver nenhum valor, retorna uma cor padrão
        return '#B0B0B0';
    }

    public function getCoordinatedCourseNameAttribute()
    {
        // Verifica se o usuário é um coordenador e se possui um Coordinator associado
        if ($this->user_type === 'coordinator' && $this->coordinator) {
            // Tenta carregar o curso coordenado (usando a relação definida no Coordinator.php)
            $course = $this->coordinator->coordinatedCourse;
            
            // Retorna o nome do curso se ele existir
            return $course ? $course->course_name : null;
        }

        return null;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPasswordNotification($token));
    }
}
