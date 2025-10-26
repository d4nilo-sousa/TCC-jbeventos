<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Event;
use App\Models\EventUserReaction;
use App\Events\UserIconUpdated;

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

        // Busca eventos criados, se for coordenador
        if ($user->user_type === 'coordinator' && $user->coordinator) {
            $eventsCreated = $user->coordinator->managedEvents()->orderBy('event_scheduled_at', 'desc')->get();
        }

        // Busca eventos que o usuário participou (curtiu)
        $participatedEvents = $this->getParticipatedEvents($user);

        // Passa as variáveis para a view.
        return view('profile.public', compact('user', 'eventsCreated', 'participatedEvents'));
    }

    /**
     * NOVO: Retorna eventos em que o usuário deu "like" ou "confirmou presença" (assumindo que 'like' é uma forma de participação).
     */
    private function getParticipatedEvents(User $user)
    {
        // Usando a reação 'like' como indicador de participação
        return Event::whereHas('reactions', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->whereIn('reaction_type', ['like']);
        })
            ->orderBy('event_scheduled_at', 'desc')
            ->limit(10) // Limita a 10 para não sobrecarregar
            ->get();
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

        // Se for AJAX, retorna JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Evento removido dos salvos.'
            ]);
        }

        // Caso contrário, comportamento normal
        return back()->with('success', 'Evento removido dos salvos.');
    }


    public function updateBannerColor(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'user_banner' => ['required', 'regex:/^#[a-f0-9]{6}$/i'],
        ]);

        // Apaga banner antigo caso seja imagem
        if ($user->user_banner && !preg_match('/^#[a-f0-9]{6}$/i', $user->user_banner)) {
            if (Storage::disk('public')->exists($user->user_banner)) {
                Storage::disk('public')->delete($user->user_banner);
            }
        }

        $user->user_banner = $request->user_banner;
        $user->save();

        // Retorna JSON se for AJAX
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'color' => $user->user_banner
            ]);
        }

        return back()->with('success', 'Banner atualizado com a cor escolhida!');
    }


    public function updateDefaultPhoto(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'user_icon_default' => 'required|string|in:avatar_default_1.svg,avatar_default_2.svg,avatar_default_3.png,avatar_default_4.png',
        ]);

        // Remove a foto antiga do storage, se existir
        if ($user->user_icon && Storage::disk('public')->exists($user->user_icon)) {
            Storage::disk('public')->delete($user->user_icon);
        }

        $user->user_icon = null; // limpa a foto customizada
        $user->user_icon_default = $validated['user_icon_default'];
        $user->save();

        // Determina a URL correta do avatar atualizado
        $avatarUrl = $user->user_icon
            ? asset('storage/' . $user->user_icon)
            : asset('imgs/' . $user->user_icon_default);

        // 🔔 Dispara o evento para atualizar em tempo real
        broadcast(new UserIconUpdated($user->id, $avatarUrl))->toOthers();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'avatar_url' => $avatarUrl
            ]);
        }

        return back()->with('success', 'Ícone padrão de perfil atualizado!');
    }



    public function updatePhoto(Request $request)
    {
        $user = auth()->user();

        // Valida a imagem
        $request->validate([
            'user_icon' => 'required|mimetypes:image/jpeg,image/png,image/gif,image/webp|max:2048',
        ]);

        // Apaga imagem antiga se existir
        if ($user->user_icon && Storage::disk('public')->exists($user->user_icon)) {
            Storage::disk('public')->delete($user->user_icon);
        }

        // Salva a nova imagem
        $path = $request->file('user_icon')->store('profile_photos', 'public');
        $user->user_icon = $path; // Salva "profile_photos/abc.jpg"
        $user->user_icon_default = null; // Garante que o ícone padrão não seja usado
        $user->save();

        // 🔔 Dispara o evento para atualizar em tempo real
        broadcast(new UserIconUpdated($user->id, asset('storage/' . $user->user_icon)))->toOthers();

        // Retorna JSON se for requisição AJAX
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'avatar_url' => asset('storage/' . $user->user_icon), // caminho completo da nova imagem
            ]);
        }

        // Caso contrário, comportamento normal
        return back()->with('success', 'Foto de perfil atualizada!');
    }


    public function updateBanner(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'user_banner' => 'required|mimetypes:image/jpeg,image/png,image/gif,image/webp|max:4096',
        ]);

        if ($user->user_banner && Storage::disk('public')->exists($user->user_banner)) {
            Storage::disk('public')->delete($user->user_banner);
        }

        $path = $request->file('user_banner')->store('banners', 'public');
        $user->user_banner = $path;
        $user->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'banner_url' => Storage::url($user->user_banner) // aqui é importante
            ]);
        }

        return back()->with('success', 'Banner atualizado!');
    }


    public function updateBio(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'bio' => 'nullable|string|max:500',
        ]);

        $user->bio = $validated['bio'];
        $user->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'bio' => $user->bio
            ]);
        }

        return back()->with('success', 'Biografia atualizada!');
    }
}
