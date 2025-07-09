<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;

class GestionUtilisateurs extends Component
{
    use WithPagination;

    public $roleFilter = '';
    public $search = '';
    public $perPage = 10;

    public function updatingRoleFilter() { $this->resetPage(); }
    public function updatingSearch() { $this->resetPage(); }

    public function toggleActivation($userId)
    {
        $user = User::findOrFail($userId);
        $user->update(['active' => !$user->active]);
    }

    public function updateRole($userId, $newRole)
    {
        $user = User::findOrFail($userId);
        $user->update(['role' => $newRole]);
    }

    public function render()
    {
        $users = User::when($this->roleFilter, fn($q) =>
                    $q->where('role', $this->roleFilter))
                ->when($this->search, fn($q) =>
                    $q->where('name', 'like', '%' . $this->search . '%'))
                ->orderBy('name')
                ->paginate($this->perPage);

        return view('livewire.admin.gestion-utilisateurs', compact('users'));
    }
}
