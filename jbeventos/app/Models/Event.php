<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'event_name',
        'event_description',
        'event_info',
        'event_location',
        'event_scheduled_at',
        'event_expired_at',
        'event_image',
        'visible_event',
        'event_type',
        'coordinator_id', // Relacionamento com Coordenador
        'course_id', // Relacionamento com Curso
    ];

    // Define o cast dos campos para tipos específicos
    protected function casts(): array
    {
        return [
            'event_scheduled_at' => 'datetime',
            'event_expired_at' => 'datetime',
            'visible_event' => 'boolean',
        ];
    }

    // Retorna os comentários associados a este evento
    public function eventComments()
    {
        return $this->hasMany(Comment::class);
    }

    // Retorna o coordenador responsável pelo evento
    public function eventCoordinator()
    {
        return $this->belongsTo(Coordinator::class, 'coordinator_id');
    }

    // Relação muitos-para-muitos com Category usando tabela pivot 'category_event'
    // withTimestamps() adiciona timestamps na tabela pivot automaticamente
    public function eventCategories()
    {
        return $this->belongsToMany(Category::class, 'category_event')->withTimestamps();
    }

    // Retorna as reações dos usuários ao evento
    // Essa relação envolve uma tabela pivô com atributos próprios
    public function reactions()
    {
        return $this->hasMany(EventUserReaction::class, 'event_id');
    }

    public function notifiableUsers() {
        return $this->belongsToMany(User::class, 'event_user_alerts', 'event_id', 'user_id');
    }

    // Relação com o modelo Course
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Retorna o curso que está assossiado ao evento
    public function eventCourse()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function images()
    {
        return $this->hasMany(EventImage::class);
    }    
    
    public function saivers(){
        return $this->belongsToMany(User::class, 'event_user_reaction')
                ->wherePivot('reaction_type', 'save')
                ->withTimestamps();
    }
}
