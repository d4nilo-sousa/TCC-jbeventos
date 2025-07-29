<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPhoneController extends Controller
{
    /**
     * Exibe o formulário de atualização de telefone.
     */
    public function edit(Request $request)
    {
        $user = auth()->user();
        return view('user.phone.edit', compact('user'));
    }

    /**
     * Atualiza o telefone do usuário.
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
