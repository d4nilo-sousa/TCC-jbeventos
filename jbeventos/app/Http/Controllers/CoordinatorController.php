<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coordinator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CoordinatorController extends Controller
{
    public function index()
    {
        // Carrega todos os coordenadores com os dados do usuário relacionados
        $coordinators = Coordinator::with('userAccount')->get();
        return view('admin.coordinators.index', compact('coordinators'));
    }

    public function create()
    {
        return view('admin.coordinators.create');
    }

    public function store(Request $request)
    {
        // Validação básica dos dados do formulário
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'coordinator_type' => 'required|in:general,course',
        ]);

        // Cria o usuário e associa como coordenador
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Criptografa a senha
            'user_type' => 'coordinator',
        ]);

        Coordinator::create([
            'user_id' => $user->id,
            'coordinator_type' => $request->coordinator_type,
        ]);

        return redirect()->route('coordinators.index')->with('success', 'Coordenador criado com sucesso!');
    }

    public function show(string $id)
    {
        // Busca coordenador com dados do usuário ou lança erro 404
        $coordinator = Coordinator::with('userAccount')->findOrFail($id);
        return view('admin.coordinators.show', compact('coordinator'));
    }

    public function edit(string $id)
    {
        $coordinator = Coordinator::with('userAccount')->findOrFail($id);
        return view('admin.coordinators.edit', compact('coordinator'));
    }

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

    public function destroy(string $id)
    {
        $coordinator = Coordinator::findOrFail($id);
        $user = $coordinator->userAccount;

        // Remove coordenador e o usuário associado
        $coordinator->delete();

        if ($user) {
            $user->delete();
        }

        return redirect()->route('coordinators.index')->with('success', 'Coordenador excluído com sucesso!');
    }
}
