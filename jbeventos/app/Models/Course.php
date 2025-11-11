<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

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

    public function events()
    {
        return $this->belongsToMany(Event::class, 'course_event');
    }

    public function posts(){
        return $this->hasMany(Post::class);
    }
   
    // Relação com o modelo Event para os eventos associados a um curso
    public function courseEvents() {
         return $this->hasMany(Event::class);
    }

    public function getCourseBannerUrlAttribute() {
        if ($this->course_banner){
            //se for uma cor hexadecimal
            if (preg_match('/^#[a-f0-9]{6}$/i', $this->course_banner)) {
                return $this->course_banner; // Retorna o código da cor
            }

            // Se for um caminho de arquivo, verifica se o arquivo existe e retorna a URL de storage
            if (Storage::disk('public')->exists($this->course_banner)) {
                return asset('storage/' . $this->course_banner);
            }

            //retorna a cor padrão
            return '#B0B0B0';
        }
    }
}
