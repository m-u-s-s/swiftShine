<div class="space-y-6">
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4">
        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Utilisateurs</p>
            <p class="mt-2 text-2xl font-bold text-slate-900">{{ $stats['utilisateurs'] }}</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Clients</p>
            <p class="mt-2 text-2xl font-bold text-slate-900">{{ $stats['clients'] }}</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Employés</p>
            <p class="mt-2 text-2xl font-bold text-slate-900">{{ $stats['employes'] }}</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">RDV</p>
            <p class="mt-2 text-2xl font-bold text-slate-900">{{ $stats['rendez_vous'] }}</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Feedbacks</p>
            <p class="mt-2 text-2xl font-bold text-slate-900">{{ $stats['feedbacks'] }}</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Logs</p>
            <p class="mt-2 text-2xl font-bold text-slate-900">{{ $stats['logs'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <h4 class="text-sm font-bold uppercase tracking-wide text-slate-700">Commandes de seed utiles</h4>

            <div class="mt-4 space-y-3">
                @foreach($seedCommands as $command)
                    <code class="block rounded-xl bg-slate-900 text-slate-100 p-3 text-sm overflow-x-auto">{{ $command }}</code>
                @endforeach
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <h4 class="text-sm font-bold uppercase tracking-wide text-slate-700">Commandes de vérification</h4>

            <div class="mt-4 space-y-3">
                @foreach($usefulCommands as $command)
                    <code class="block rounded-xl bg-slate-900 text-slate-100 p-3 text-sm overflow-x-auto">{{ $command }}</code>
                @endforeach
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
        <h4 class="text-sm font-bold uppercase tracking-wide text-amber-800">Note</h4>
        <p class="mt-2 text-sm text-amber-700 leading-relaxed">
            Cette section est volontairement informative. Elle ne lance pas directement de commandes système depuis l’interface,
            ce qui évite les actions destructrices involontaires en production.
        </p>
    </div>
</div>
