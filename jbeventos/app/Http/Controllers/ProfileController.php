<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\User;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        return view('profile.show', compact('user'));
    }

    public function updatePhoto(Request $request)
    {
        // Valida a imagem
        $request->validate([
            'user_icon' => 'required|image|max:2048',
        ]);

        $user = auth()->user();

        // Deleta a imagem antiga
        if ($user->user_icon) {
            Storage::delete('public/profile_photos/' . $user->user_icon);
        }

        // Salva a nova imagem
        $path = $request->file('user_icon')->store('public/profile_photos');
        $user->user_icon = basename($path); // Armazena apenas o nome da imagem
        $user->save();

        return back()->with('success', 'Foto de perfil atualizada!'); // Redireciona de volta para a tela de perfil
    }

    public function updateBanner(Request $request)
    {
        // Valida a imagem
        $request->validate([
            'user_banner' => 'required|image|max:4096',
        ]);

        $user = auth()->user();

        // Deleta o banner antigo
        if ($user->user_banner) {
            Storage::delete('public/banners/' . $user->user_banner);
        }

        // Salva o novo banner
        $path = $request->file('user_banner')->store('public/banners');
        $user->user_banner = basename($path); // Armazena apenas o nome do banner
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



