<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

    /**
     * Os atributos que podem ser preenchidos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'event_name',
        'event_description',
        'event_location',
        'event_scheduled_at',
        'event_expired_at',
        'event_image',
        'visible_event',
        'coordinator_id', // Relacionamento com Coordenador
        'course_id',      // Relacionamento com Curso (opcional)
    ];

    /**
     * Define o cast dos atributos para tipos específicos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'event_scheduled_at' => 'datetime',
            'event_expired_at' => 'datetime',
            'visible_event' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELACIONAMENTOS
    |--------------------------------------------------------------------------
    */

    /**
     * Obtém os comentários relacionados a este evento.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function eventComments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Obtém o coordenador responsável por este evento.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function eventCoordinator()
    {
        return $this->belongsTo(Coordinator::class, 'coordinator_id');
    }

    /**
     * Relação muitos-para-muitos com categorias associadas ao evento.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function eventCategories()
    {
        return $this->belongsToMany(Category::class, 'category_event')->withTimestamps();
    }

    /**
     * Retorna as reações dos usuários para este evento.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reactions()
    {
        return $this->hasMany(EventUserReaction::class);
    }

    /**
     * Usuários que ativaram alertas para este evento.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function notifiableUsers()
    {
        return $this->belongsToMany(User::class, 'event_user_alerts', 'event_id', 'user_id');
    }

    /**
     * Curso ao qual este evento pertence.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}