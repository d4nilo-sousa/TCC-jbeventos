<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Exibe a página do perfil do usuário autenticado.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        $user = auth()->user();
        return view('profile.show', compact('user'));
    }

    /**
     * Exibe o perfil público de um usuário específico.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function viewPublicProfile(User $user)
    {
        return view('profile.public', compact('user'));
    }

    /**
     * Atualiza a foto do perfil do usuário autenticado.
     *
     * Valida a imagem, remove a antiga se existir,
     * e salva o novo arquivo no disco 'public'.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePhoto(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'user_icon' => 'required|image|max:2048',
        ]);

        if ($user->user_icon && Storage::disk('public')->exists($user->user_icon)) {
            Storage::disk('public')->delete($user->user_icon);
        }

        $path = $request->file('user_icon')->store('profile_photos', 'public');
        $user->user_icon = $path;
        $user->save();

        return back()->with('success', 'Foto de perfil atualizada!');
    }

    /**
     * Atualiza o banner do perfil do usuário autenticado.
     *
     * Valida a imagem, remove o banner antigo se existir,
     * e salva o novo arquivo no disco 'public'.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateBanner(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'user_banner' => 'required|image|max:4096',
        ]);

        if ($user->user_banner && Storage::disk('public')->exists($user->user_banner)) {
            Storage::disk('public')->delete($user->user_banner);
        }

        $path = $request->file('user_banner')->store('banners', 'public');
        $user->user_banner = $path;
        $user->save();

        return back()->with('success', 'Banner atualizado!');
    }

    /**
     * Atualiza a biografia do usuário autenticado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateBio(Request $request)
    {
        $request->validate([
            'bio' => 'nullable|string|max:500',
        ]);

        $user = auth()->user();
        $user->bio = $request->bio;
        $user->save();

        return back()->with('success', 'Biografia atualizada!');
    }
}