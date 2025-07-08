<div class="space-y-6" x-data="{ step: @entangle('step') }">
    <h2 class="text-2xl font-bold text-gray-800">📅 Prendre un rendez-vous</h2>

    {{-- Stepper visuel --}}
    <div class="flex items-center justify-between text-sm font-medium text-gray-600 mb-4">
        <div :class="step === 1 ? 'font-bold text-blue-600' : ''">1. Employé</div>
        <div :class="step === 2 ? 'font-bold text-blue-600' : ''">2. Créneau</div>
        <div :class="step === 3 ? 'font-bold text-blue-600' : ''">3. Confirmation</div>
    </div>

    {{-- Étape 1 : Choix de l'employé --}}
    <div x-show="step === 1" x-transition>
        <label class="block text-sm font-semibold mb-1">👤 Choisir un employé</label>
        <select wire:model="employe_id" class="w-full border-gray-300 rounded shadow-sm">
            <option value="">-- Sélectionnez --</option>
            @foreach($employes as $emp)
                <option value="{{ $emp->id }}">{{ $emp->name }}</option>
            @endforeach
        </select>
        @error('employe_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

        <div class="mt-4 text-right">
            <button wire:click="nextStep" class="bg-blue-600 text-white px-4 py-2 rounded">Suivant →</button>
        </div>
    </div>

    {{-- Étape 2 : Créneaux --}}
    <div x-show="step === 2" x-transition>
        <h3 class="text-lg font-bold mb-4">📆 Sélectionner une date et heure</h3>

        @if($employe_id)
            @livewire('client.calendrier-prise-rdv', [
                'employe_id' => $employe_id,
                'selectedDate' => $rdvDate,
                'selectedHeure' => $rdvHeure
            ], key($employe_id))
        @endif

        @error('rdvDate') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror

        <div class="mt-4 flex justify-between">
            <button wire:click="prevStep" class="text-sm text-gray-500">← Retour</button>
            <button wire:click="nextStep" class="bg-blue-600 text-white px-4 py-2 rounded">Suivant →</button>
        </div>
    </div>

    {{-- Étape 3 : Confirmation --}}
    <div x-show="step === 3" x-transition>
        <h3 class="text-lg font-bold mb-4">✅ Confirmer votre rendez-vous</h3>
        <p class="mb-2">👤 Employé : <strong>{{ $employeNom }}</strong></p>
        <p>Date : <strong>{{ $rdvDate }}</strong> à <strong>{{ $rdvHeure }}</strong></p>

        <div class="mt-4 flex justify-between">
            <button wire:click="prevStep" class="text-sm text-gray-500">← Modifier</button>
            <button wire:click="validerRdv" class="bg-green-600 text-white px-4 py-2 rounded">Confirmer</button>
        </div>
    </div>

    {{-- ✅ Message de succès animé --}}
    @if($rdvEnvoye)
        <div
            x-data="{ show: true }"
            x-show="show"
            x-transition.duration.500ms
            x-init="setTimeout(() => show = false, 5000); $el.scrollIntoView({behavior: 'smooth'})"
            class="mt-6 bg-green-100 border border-green-300 text-green-800 text-center p-4 rounded shadow"
        >
            🎉 Votre rendez-vous a été confirmé avec succès !
        </div>
    @endif
</div>
