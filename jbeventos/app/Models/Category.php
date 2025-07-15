<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory; 

    // Define os campos que podem ser preenchidos em massa (mass assignment)
    protected $fillable = [
        'category_name',
    ];

    // Define relação muitos-para-muitos com o modelo Event
    // A tabela pivot usada é 'category_event'
    // withTimestamps() adiciona timestamps na tabela pivot automaticamente
    public function relatedEvents() {
        return $this->belongsToMany(Event::class, 'category_event')->withTimestamps();
    }
}