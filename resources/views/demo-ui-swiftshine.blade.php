<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Démo UI — SwiftShine</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- ✅ Styles personnalisés SwiftShine --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="p-8 space-y-8 bg-[#f9fafb]">

    <h1 class="text-3xl font-bold text-swift-blue">🌟 Démo Interface SwiftShine</h1>

    {{-- ✅ Boutons --}}
    <div class="card space-y-4">
        <h2 class="text-xl font-semibold">🔘 Boutons</h2>
        <button class="btn btn-red">🗑 Supprimer</button>
        <button class="btn btn-green">✅ Valider</button>
        <button class="btn btn-blue">🔍 Voir détails</button>
    </div>

    {{-- 🟢 Badges --}}
    <div class="card space-y-4">
        <h2 class="text-xl font-semibold">🟢 Statuts (badges)</h2>
        <div class="space-x-3">
            <span class="badge badge-validé">Validé</span>
            <span class="badge badge-refusé">Refusé</span>
            <span class="badge badge-attente">En attente</span>
        </div>
    </div>

    {{-- 📝 Formulaire --}}
    <div class="card space-y-4">
        <h2 class="text-xl font-semibold">📝 Formulaire</h2>
        <form class="space-y-4">
            <input type="text" placeholder="Nom complet" />
            <input type="email" placeholder="Adresse email" />
            <textarea placeholder="Votre message..."></textarea>
            <select>
                <option>Choisir un rôle</option>
                <option>Client</option>
                <option>Employé</option>
                <option>Admin</option>
            </select>
            <button class="btn btn-blue">📤 Envoyer</button>
        </form>
    </div>

    {{-- 💬 Toast --}}
    <div class="card space-y-2">
        <h2 class="text-xl font-semibold">💬 Toast de notification</h2>
        <div class="toast success">✅ Rendez-vous validé</div>
        <div class="toast error">❌ Ce créneau est déjà pris</div>
    </div>

    {{-- ⭐ Étoiles --}}
    <div class="card space-y-2">
        <h2 class="text-xl font-semibold">⭐ Évaluation (stars)</h2>
        <div class="star-rating space-x-1">
            @for($i = 1; $i <= 5; $i++)
                <span class="star">★</span>
            @endfor
        </div>
    </div>

</body>
</html>
