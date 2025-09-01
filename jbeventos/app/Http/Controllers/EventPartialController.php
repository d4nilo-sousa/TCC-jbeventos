<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

class EventPartialController extends Controller
{
    /**
     * Exibe o "card" parcial de um evento específico.
     * Esse método serve para retornar apenas o HTML parcial,
     */
    public function getPartial(string $id)
    {
        // Busca o evento pelo ID ou lança 404 se não encontrar
        $event = Event::findOrFail($id);

        // Renderiza a view "partials.event-card" passando a variável $event
        // ->render() retorna o HTML puro (string), em vez de uma resposta HTTP completa
        return view('partials.event-card', compact('event'))->render();
    }
}
