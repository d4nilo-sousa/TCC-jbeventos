<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;

class UserPhoneController extends Controller
{
    /**
     * Exibe o formulário de atualização de telefone.
     */
    public function edit(Request $request)
    {
        // Obtém o usuário autenticado
        $user = auth()->user();

        // Retorna a view para editar telefone, passando o usuário
        return view('user.phone.edit', compact('user'));
    }

    /**
     * Atualiza o telefone do usuário e redireciona após atualizar.
     */
    public function update(Request $request)
    {
        // Valida o telefone, pode ser nulo, deve ser string, seguir formato (99) 99999-9999,
        // e ser único na tabela users exceto para o usuário atual
        $request->validate([
            'phone_number' => 'nullable|string|regex:/^\(\d{2}\) \d{5}-\d{4}$/|unique:users,phone_number,' . auth()->id(),
        ]);

        // Obtém o usuário autenticado
        $user = auth()->user();

        // Atualiza o telefone e marca como verificado
        $user->phone_number = $request->phone_number;
        $user->save();

        // Redireciona para rota dashboard com mensagem de sucesso
        return redirect()->route('dashboard') // ou outra rota que prefira
            ->with('success', 'Telefone atualizado com sucesso!');
    }
}
