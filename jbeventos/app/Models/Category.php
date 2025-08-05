<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'category_name',
    ];

    /**
     * Relação muitos-para-muitos entre categorias e eventos.
     *
     * Utiliza a tabela pivot 'category_event' e mantém timestamps na pivot.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function relatedEvents()
    {
        return $this->belongsToMany(Event::class, 'category_event')->withTimestamps();
    }
}