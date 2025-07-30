<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory;

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'course_name',
        'course_description',
        'course_icon',
        'course_banner',
        'coordinator_id',
        'user_id',
    ];

    // Relação com o modelo Coordinator
    public function courseCoordinator() {
        return $this->belongsTo(Coordinator::class, 'coordinator_id');
    }

    // Relação com o modelo User para o criador do curso
    public function courseCreator() {
        return $this->belongsTo(User::class);
    }

    // Relação muitos-para-muitos com User usando a tabela pivot 'course_user_follow'
    // withTimestamps() adiciona timestamps na tabela pivot automaticamente
    public function followers() {
        return $this->belongsToMany(User::class, 'course_user_follow')->withTimestamps();
    }

    public function followersCount() {
        return $this->followers()->count();
    }

    // Relação muitos-para-muitos com Event
    public function events() {
        return $this->hasMany(Event::class);
    }

   
}
