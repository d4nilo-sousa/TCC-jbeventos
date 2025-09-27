<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Event;
use App\Models\EventUserReaction;

class ProfileController extends Controller
{
    /**
     * Exibe o perfil do usuário logado, incluindo eventos salvos e criados.
     */
    public function show()
    {
        $user = auth()->user();

        // Carrega os eventos salvos do usuário.
        $savedEvents = $user->savedEvents()->orderBy('event_scheduled_at', 'desc')->get();
        
        // Inicializa a variável para os eventos criados como uma coleção vazia
        $createdEvents = collect();
        
        // Se o usuário for um coordenador e a relação 'coordinator' não for nula,
        // carregamos os eventos criados por ele.
        if ($user->user_type === 'coordinator' && $user->coordinator) {
            $createdEvents = $user->coordinator->managedEvents()->orderBy('event_scheduled_at', 'desc')->get();
        }

        // Passa todas as variáveis para a view.
        return view('profile.show', compact('user', 'savedEvents', 'createdEvents'));
    }

    /**
     * Exibe o perfil público de outro usuário.
     */
    public function viewPublicProfile(User $user)
    {
        $eventsCreated = collect(); // Inicia uma coleção vazia
        
        // Verifica se o usuário é um coordenador antes de buscar os eventos
        if ($user->user_type === 'coordinator' && $user->coordinator) {
            $eventsCreated = $user->coordinator->managedEvents()->orderBy('event_scheduled_at', 'desc')->get();
        }

        // Passa as variáveis para a view.
        return view('profile.public', compact('user', 'eventsCreated'));
    }
    
    /**
     * Salva um evento para o usuário logado.
     */
    public function saveEvent(Request $request, Event $event)
    {
        $user = auth()->user();

        // Verifique se o evento já foi salvo
        $exists = EventUserReaction::where('user_id', $user->id)
                                    ->where('event_id', $event->id)
                                    ->where('reaction_type', 'save')
                                    ->exists();

        if (!$exists) {  
            // Crie uma nova reação do tipo 'save'
            EventUserReaction::create([
                'user_id' => $user->id,
                'event_id' => $event->id,
                'reaction_type' => 'save',
            ]);

            return back()->with('success', 'Evento salvo com sucesso!');
        }
        return back()->with('info', 'O evento já está salvo.');
    }
    
    /**
     * Remove um evento salvo pelo usuário.
     */
    public function unsaveEvent(Request $request, Event $event)
    {
        $user = auth()->user();

        // Encontra e deleta a reação do tipo 'save'
        EventUserReaction::where('user_id', $user->id)
                        ->where('event_id', $event->id)
                        ->where('reaction_type', 'save')
                        ->delete();

        return back()->with('success', 'Evento removido dos salvos.');
    }

    public function updateBannerColor(Request $request)
    {
        $user = auth()->user();

        // Valida a cor
        $request->validate([
            'user_banner' => ['required', 'regex:/^#[a-f0-9]{6}$/i'],
        ]);

        // Apaga o banner antigo se existir, pois agora usaremos uma cor
        if ($user->user_banner && !preg_match('/^#[a-f0-9]{6}$/i', $user->user_banner)) {
            if (Storage::disk('public')->exists($user->user_banner)) {
                Storage::disk('public')->delete($user->user_banner);
            }
        }

        // Salva a nova cor
        $user->user_banner = $request->user_banner;
        $user->save();

        return back()->with('success', 'Banner atualizado com a cor escolhida!');
    }


     //Atualiza a foto de perfil com um dos ícones padrão.
    public function updateDefaultPhoto(Request $request)
    {
        $user = auth()->user();

        // Valida se o nome do ícone enviado é um dos válidos
        $validated = $request->validate([
            'user_icon_default' => 'required|string|in:avatar_default_1.svg,avatar_default_2.svg,avatar_default_3.png,avatar_default_4.png',
        ]);
        
        // Remove a foto de upload se existir, para evitar conflitos
        if ($user->user_icon && Storage::disk('public')->exists($user->user_icon)) {
            Storage::disk('public')->delete($user->user_icon);
        }

        // Salva o nome do arquivo padrão no banco de dados e limpa o campo de upload
        $user->user_icon = null;
        $user->user_icon_default = $validated['user_icon_default'];
        $user->save();

        return back()->with('success', 'Ícone padrão de perfil atualizado!');
    }


    public function updatePhoto(Request $request)
    {
        $user = auth()->user();

        // Valida a imagem
        $request->validate([
            'user_icon' => 'required|image|max:2048',
        ]);

        //apagar imagem antiga se existir
        if($user->user_icon && Storage::disk('public')->exists($user->user_icon)){
            Storage::disk('public')->delete($user->user_icon);
        }

        // Salva a nova imagem
        $path = $request->file('user_icon')->store('profile_photos', 'public');
        $user->user_icon = $path; // Salva "profile_photos/abc.jpg"
        $user->save();

        return back()->with('success', 'Foto de perfil atualizada!'); // Redireciona de volta para a tela de perfil
    }

    public function updateBanner(Request $request)
    {
        $user = auth()->user();

        // Valida a imagem
        $request->validate([
            'user_banner' => 'required|image|max:4096',
        ]);

        // Apaga o banner antigo se existir
        if ($user->user_banner && Storage::disk('public')->exists($user->user_banner)) {
            Storage::disk('public')->delete($user->user_banner);
        }

        // Salva o novo banner
        $path = $request->file('user_banner')->store('banners', 'public');
        $user->user_banner = $path;
        $user->save();

        return back()->with('success', 'Banner atualizado!');
    }

    public function updateBio(Request $request)
    {
        // Valida a biografia
        $request->validate([
            'bio' => 'nullable|string|max:500',
        ]);

        $user = auth()->user(); // Obtem o usuário autenticado
        $user->bio = $request->bio; // Atualiza a biografia
        $user->save(); // Salva as alterações

        return back()->with('success', 'Biografia atualizada!');
    }
}