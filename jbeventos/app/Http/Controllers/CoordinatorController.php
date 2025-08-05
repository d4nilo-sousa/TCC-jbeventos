<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coordinator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Class CoordinatorController
 *
 * Controlador responsável pela gestão de coordenadores.
 * Inclui operações de CRUD (criação, leitura, atualização e exclusão)
 * e gerenciamento de usuários associados aos coordenadores.
 *
 * @package App\Http\Controllers
 */
class CoordinatorController extends Controller
{
    /**
     * Exibe a lista de todos os coordenadores com seus respectivos dados de usuário.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $coordinators = Coordinator::with('userAccount')->get();
        return view('admin.coordinators.index', compact('coordinators'));
    }

    /**
     * Exibe o formulário para criação de um novo coordenador.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.coordinators.create');
    }

    /**
     * Armazena um novo coordenador e o usuário associado, após validação dos dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'coordinator_type' => 'required|in:general,course',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => 'coordinator',
        ]);

        Coordinator::create([
            'user_id' => $user->id,
            'coordinator_type' => $request->coordinator_type,
        ]);

        return redirect()->route('coordinators.index')->with('success', 'Coordenador criado com sucesso!');
    }

    /**
     * Exibe os detalhes de um coordenador específico.
     *
     * @param  string  $id
     * @return \Illuminate\View\View
     */
    public function show(string $id)
    {
        $coordinator = Coordinator::with('userAccount')->findOrFail($id);
        return view('admin.coordinators.show', compact('coordinator'));
    }

    /**
     * Exibe o formulário de edição de um coordenador existente.
     *
     * @param  string  $id
     * @return \Illuminate\View\View
     */
    public function edit(string $id)
    {
        $coordinator = Coordinator::with('userAccount')->findOrFail($id);
        return view('admin.coordinators.edit', compact('coordinator'));
    }

    /**
     * Atualiza o tipo de um coordenador existente, após validação.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Remove um coordenador e o usuário associado do banco de dados.
     *
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
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
