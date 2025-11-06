<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class FeedController extends Controller
{
    /**
     * Exibe a tela de Feed.
     */
     public function index(Request $request)
    {
        $user = Auth::user();

        // 1. Coleta de Eventos
        $events = Event::with(['eventCoordinator.userAccount', 'images', 'reactions', 'eventCategories'])
            ->latest('event_scheduled_at')
            ->get()
            ->map(function ($event) {
                $event->type = 'event';
                $event->sort_date = $event->event_scheduled_at; 
                return $event;
            });
        $feedItems = $events->values();
        
        // Define o estado inicial do modal. Se o usuário já tiver o campo 'welcome_modal_shown' como true, ele não verá nada.
        $showWelcomeModal = false;
        $showWelcomeButton = false;

        // Se o usuário NUNCA VIU o modal (baseado no campo do DB)
        if (!$user->welcome_modal_shown) {
            
            // Verifica o status na sessão para controlar a transição Modal -> Botão
            // Variável de controle na sessão
            $welcomeStatus = $request->session()->get('welcome_modal_status', 0);
            
            if ($welcomeStatus === 0) {
                // PRIMEIRA VISITA: Mostra o modal.
                $showWelcomeModal = true;
                
                // ATUALIZA A SESSÃO para 1, indicando que o modal já foi mostrado.
                $request->session()->put('welcome_modal_status', 1);
                
                // Marca o usuário no DB como tendo visto pelo menos uma vez, 
                // mas sem desativar a transição Session (Modal -> Botão)
            
            } elseif ($welcomeStatus === 1) {
                // SEGUNDA VISITA
                $showWelcomeButton = true;
                
                // Opcional: Atualiza o status da sessão para 2, para que o botão não apareça automaticamente
                // se ele simplesmente atualizar a página, mantendo a opção de clique.
                $request->session()->put('welcome_modal_status', 2);
            }
            // Se $welcomeStatus >= 2, nada acontece.
        } 

        // A persistência no DB é feita no método disableWelcomeModal.
        // O $request->session()->save() é mantido para garantir que o status 1 seja gravado imediatamente.
        $request->session()->save(); 

        return view('feed.index', [
            'feedItems' => $feedItems,
            'showWelcomeModal' => $showWelcomeModal,
            'showWelcomeButton' => $showWelcomeButton,
            'user' => $user, 
        ]);
    }

    /**
     * Define o status do modal de boas-vindas para desativado permanentemente (status 2).
     */
    public function disableWelcomeModal(Request $request)
    {
        $user = Auth::user();

        // 1. Persistência no Banco de Dados
        if ($user) {
            $user->welcome_modal_shown = true; // Marcar como permanentemente visto
            $user->save();
        }

        // 2. Persistência na Sessão (Garante que não apareça na mesma sessão)
        $request->session()->put('welcome_modal_status', 2);
        $request->session()->save();

        return response()->json(['status' => 'success', 'message' => 'Modal permanentemente desativado.']);
    }
}