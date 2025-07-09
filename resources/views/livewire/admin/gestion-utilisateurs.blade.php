<div class="bg-white p-4 rounded shadow space-y-4">

    <h3 class="text-xl font-semibold text-blue-800">👥 Gestion des utilisateurs</h3>

    {{-- 🔍 Filtres --}}
    <div class="flex flex-wrap gap-4 items-end">
        <div>
            <label class="text-sm text-gray-600">Rôle</label>
            <select wire:model="roleFilter" class="border rounded px-2 py-1 text-sm">
                <option value="">— Tous —</option>
                <option value="client">Client</option>
                <option value="employe">Employé</option>
                <option value="societe">Société</option>
            </select>
        </div>

        <div>
            <label class="text-sm text-gray-600">Rechercher</label>
            <input type="text" wire:model.debounce.300ms="search"
                   placeholder="Nom ou email"
                   class="border rounded px-2 py-1 text-sm" />
        </div>
    </div>

    {{-- 📋 Tableau --}}
    <table class="w-full text-sm table-auto border mt-3">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-2 py-1 text-left">Nom</th>
                <th class="px-2 py-1">Email</th>
                <th class="px-2 py-1">Rôle</th>
                <th class="px-2 py-1">Actif</th>
                <th class="px-2 py-1">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $u)
                <tr class="border-t">
                    <td class="px-2 py-1">{{ $u->name }}</td>
                    <td class="px-2 py-1">{{ $u->email }}</td>
                    <td class="px-2 py-1">
                        <select wire:change="updateRole({{ $u->id }}, $event.target.value)"
                                class="border px-1 text-sm">
                            <option value="client" @selected($u->role === 'client')>Client</option>
                            <option value="employe" @selected($u->role === 'employe')>Employé</option>
                            <option value="societe" @selected($u->role === 'societe')>Société</option>
                        </select>
                    </td>
                    <td class="px-2 py-1">
                        @if($u->active)
                            <span class="text-green-600 font-semibold">Oui</span>
                        @else
                            <span class="text-red-600 font-semibold">Non</span>
                        @endif
                    </td>
                    <td class="px-2 py-1">
                        <button wire:click="toggleActivation({{ $u->id }})"
                                class="text-xs bg-gray-200 px-2 py-1 rounded hover:bg-gray-300">
                            {{ $u->active ? 'Désactiver' : 'Activer' }}
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $users->links() }}
    </div>
</div>
