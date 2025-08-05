<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPhoneController extends Controller
{
    /**
     * Exibe o formulário para o usuário atualizar seu telefone.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function edit(Request $request)
    {
        $user = auth()->user();
        return view('user.phone.edit', compact('user'));
    }

    /**
     * Atualiza o número de telefone do usuário autenticado.
     *
     * Valida o formato do telefone (ex: (99) 99999-9999) e verifica
     * se o número é único entre os usuários, exceto o próprio usuário.
     * Retorna uma resposta JSON, compatível com requisições AJAX.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $request->validate([
            'phone_number' => 'nullable|string|regex:/^\(\d{2}\) \d{5}-\d{4}$/|unique:users,phone_number,' . auth()->id(),
        ]);

        $user = auth()->user();
        $user->phone_number = $request->phone_number;
        $user->save();

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => true]);
    }
}
