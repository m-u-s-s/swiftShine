<x-guest-layout>
    <!-- 🌟 HERO -->
    <div class="bg-blue-50 py-20 px-6 text-center">
        <div class="max-w-3xl mx-auto">
            <h1 class="text-4xl md:text-5xl font-extrabold text-blue-900 mb-4 animate-fade-down">
                Réservez votre rendez-vous en ligne
            </h1>
            <p class="text-lg text-blue-700 mb-8 animate-fade-up">
                Choisissez un créneau parmi nos experts disponibles.
            </p>
            <a href="#rdv" class="inline-block bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700 transition">
                📅 Commencer maintenant
            </a>
        </div>
    </div>

    <!-- 📅 FORMULAIRE DE PRISE DE RDV -->
    <div id="rdv" class="py-12 bg-white">
        <div class="max-w-5xl mx-auto px-6">
            @livewire('client.prendre-rendez-vous')
        </div>
    </div>
</x-guest-layout>
