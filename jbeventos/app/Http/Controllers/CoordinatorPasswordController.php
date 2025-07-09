<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CoordinatorPasswordController extends Controller
{
    // Exibe o formulário para o coordenador alterar a senha
    public function edit()
    {
        return view('coordinator.password.edit');
    }

    // Recebe o formulário e atualiza a senha do coordenador
    public function update(Request $request)
    {
        // Valida: senha obrigatória, mínimo 8 caracteres, e confirmação da senha
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Pega o usuário autenticado
        $user = auth()->user();

        // Atualiza a senha com hash para segurança
        $user->password = Hash::make($request->password);
        $user->save();

        // Busca o coordenador associado ao usuário atual
        $coordinator = \App\Models\Coordinator::where('user_id', $user->id)->first();

        if ($coordinator) {
            // Marca a senha temporária como falsa, pois foi atualizada
            $coordinator->temporary_password = false;
            $coordinator->save();
        }

        // Redireciona para o dashboard com mensagem de sucesso
        return redirect()->route('coordinator.dashboard')->with('success', 'Senha atualizada com sucesso!');
    }
}
