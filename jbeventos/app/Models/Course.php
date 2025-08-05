<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory;

    /**
     * Os atributos que podem ser preenchidos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'course_name',
        'course_description',
        'course_icon',
        'course_banner',
        'coordinator_id',
        'user_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONAMENTOS
    |--------------------------------------------------------------------------
    */

    /**
     * Relação de pertencimento com o coordenador do curso.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function courseCoordinator()
    {
        return $this->belongsTo(Coordinator::class, 'coordinator_id');
    }

    /**
     * Relação de pertencimento com o usuário criador do curso.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function courseCreator()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relação muitos-para-muitos com usuários que seguem o curso.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'course_user_follow')->withTimestamps();
    }

    /**
     * Retorna a contagem de seguidores do curso.
     *
     * @return int
     */
    public function followersCount()
    {
        return $this->followers()->count();
    }

    /**
     * Relação de um-para-muitos com eventos associados ao curso.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function events()
    {
        return $this->hasMany(Event::class);
    }
}