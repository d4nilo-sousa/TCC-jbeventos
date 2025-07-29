<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coordinator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CoordinatorController extends Controller
{
    // Lista todos os coordenadores com seus dados de usuário relacionados
    public function index()
    {
        $coordinators = Coordinator::with('userAccount')->get();
        return view('admin.coordinators.index', compact('coordinators'));
    }

    // Exibe o formulário para criar um novo coordenador
    public function create()
    {
        return view('admin.coordinators.create');
    }

    // Armazena um novo coordenador e o usuário associado após validar os dados
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'coordinator_type' => 'required|in:general,course',
        ]);

        // Cria o usuário coordenador com senha criptografada e verificação de email
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => 'coordinator',
        ]);

        // Cria o coordenador vinculando ao usuário criado
        Coordinator::create([
            'user_id' => $user->id,
            'coordinator_type' => $request->coordinator_type,
        ]);

        return redirect()->route('coordinators.index')->with('success', 'Coordenador criado com sucesso!');
    }

    // Exibe os detalhes de um coordenador específico
    public function show(string $id)
    {
        $coordinator = Coordinator::with('userAccount')->findOrFail($id);
        return view('admin.coordinators.show', compact('coordinator'));
    }

    // Exibe o formulário para editar um coordenador existente
    public function edit(string $id)
    {
        $coordinator = Coordinator::with('userAccount')->findOrFail($id);
        return view('admin.coordinators.edit', compact('coordinator'));
    }

    // Atualiza o tipo do coordenador após validação
    public function update(Request $request, string $id)
    {
        $coordinator = Coordinator::findOrFail($id);

        $request->validate([
            'coordinator_type' => 'required|in:general,course',
        ]);

        $coordinator->update([
            'coordinator_type' => $request->coordinator_type,
        ]);

        return redirect()->route('coordinators.index')->with('success', 'Coordenador atualizado com sucesso!');
    }

    // Remove o coordenador e o usuário relacionado do banco
    public function destroy(string $id)
    {
        $coordinator = Coordinator::findOrFail($id);
        $user = $coordinator->userAccount;

        $coordinator->delete();

        if ($user) {
            $user->delete();
        }

        return redirect()->route('coordinators.index')->with('success', 'Coordenador excluído com sucesso!');
    }
}
