<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Class CoordinatorPasswordController
 *
 * Controlador responsável por exibir o formulário de alteração de senha para o coordenador
 * e realizar a atualização da senha após validação e verificação de senha temporária.
 *
 * @package App\Http\Controllers
 */
class CoordinatorPasswordController extends Controller
{
    /**
     * Exibe o formulário para o coordenador alterar a senha.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        return view('coordinator.password.edit');
    }

    /**
     * Recebe o formulário de alteração de senha, valida os dados,
     * verifica se a nova senha não é igual à temporária e atualiza a senha do coordenador.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        $coordinator = \App\Models\Coordinator::where('user_id', $user->id)->first();

        if ($coordinator && $coordinator->temporary_password) {
            if (Hash::check($request->password, $user->password)) {
                return back()->withErrors(['password' => 'A nova senha não pode ser igual à senha temporária que você usou para entrar.']);
            }
        }

        $user->password = Hash::make($request->password);
        $user->save();

        if ($coordinator) {
            $coordinator->temporary_password = false;
            $coordinator->save();
        }

        return redirect()->route('coordinator.dashboard')->with('success', 'Senha atualizada com sucesso!');
    }
}