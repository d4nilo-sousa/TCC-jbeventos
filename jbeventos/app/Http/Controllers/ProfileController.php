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

    public function viewPublicProfile(User $user)
    {
        return view('profile.public', compact('user'));
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



