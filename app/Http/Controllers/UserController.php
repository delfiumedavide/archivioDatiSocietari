<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::with(['roles', 'companies'])
            ->orderBy('name')
            ->paginate(20);

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        $roles = Role::all();
        $permissions = Permission::orderBy('section')->orderBy('name')->get()->groupBy('section');
        $companies = Company::active()->orderBy('denominazione')->get();

        return view('users.create', compact('roles', 'permissions', 'companies'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = User::create([
            'name'      => $request->input('name'),
            'email'     => $request->input('email'),
            'password'  => $request->input('password'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        $user->roles()->attach($request->input('role_id'));

        if ($request->has('permissions')) {
            $user->permissions()->sync($request->input('permissions'));
        }

        $user->companies()->sync($request->input('company_ids', []));

        return redirect()->route('users.index')
            ->with('success', 'Utente creato con successo.');
    }

    public function edit(User $user): View
    {
        $user->load(['roles', 'permissions', 'companies']);
        $roles = Role::all();
        $permissions = Permission::orderBy('section')->orderBy('name')->get()->groupBy('section');
        $companies = Company::active()->orderBy('denominazione')->get();
        $assignedIds = $user->companies->pluck('id')->toArray();

        return view('users.edit', compact('user', 'roles', 'permissions', 'companies', 'assignedIds'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = [
            'name'      => $request->input('name'),
            'email'     => $request->input('email'),
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->input('password'));
        }

        $user->update($data);
        $user->roles()->sync([$request->input('role_id')]);
        $user->permissions()->sync($request->input('permissions', []));
        $user->companies()->sync($request->input('company_ids', []));

        return redirect()->route('users.index')
            ->with('success', 'Utente aggiornato con successo.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->isAdmin() && User::whereHas('roles', fn ($q) => $q->where('name', 'admin'))->count() <= 1) {
            return redirect()->route('users.index')
                ->with('error', 'Impossibile eliminare l\'ultimo amministratore.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Utente eliminato.');
    }
}
