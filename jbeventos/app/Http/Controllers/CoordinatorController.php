<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coordinator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CoordinatorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $coordinators = Coordinator::with('userAccount')->get();
        return view('admin.coordinators.index', compact('coordinators'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.coordinators.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
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
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $coordinator = Coordinator::with('userAccount')->findOrFail($id);
        return view('admin.coordinators.show', compact('coordinator'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $coordinator = Coordinator::with('userAccount')->findOrFail($id);
        return view('admin.coordinators.edit', compact('coordinator'));
    }

    /**
     * Update the specified resource in storage.
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

        return redirect()->route('admin.coordinators.index')->with('success', 'Coordenador atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $coordinator = Coordinator::findOrFail($id);
        $user = $coordinator->userAccount;

        $coordinator->delete();

        if ($user) {
            $user->delete();
        }

        return redirect()->route('admin.coordinators.index')->with('success', 'Coordenador excluído com sucesso!');
    }
}
