Présentation générale
Ce dépôt correspond à une application Laravel (PHP 8.1) intégrant Jetstream, Livewire et Tailwind. On retrouve les dépendances principales dans le composer.json : Laravel, Livewire, Jetstream…

Côté JavaScript, la compilation se fait via Vite et Tailwind, avec FullCalendar et ApexCharts pour les interfaces riches

Organisation des dossiers
app/ : logique applicative

Models/ : classes Eloquent (User, RendezVous, Disponibilite, etc.)

Livewire/ : composants interactifs (dashboards, calendriers…)

Http/ : contrôleurs et middleware (ex. CheckRole)

Console/Commands/ : commandes artisan personnalisées (vérifications Livewire, génération de disponibilités…)

resources/ : vues Blade et assets (CSS/JS). Les composants Livewire possèdent leurs templates dans resources/views/livewire.

routes/ : déclarations de routes (principalement dans web.php).
Les dashboards sont exposés via des routes Livewire protégées par un middleware de rôle

database/ : migrations et seeders définissant la structure (tables rendez_vous, disponibilites, etc.)

Fonctionnalités notables
Prise de rendez‑vous côté client
Le composant Livewire PrendreRendezVous gère un formulaire en plusieurs étapes : choix de l’employé, sélection du créneau puis confirmation. Les validations sont intégrées dans le composant et la vue correspondante affiche un « stepper » dynamique.

Tableau de bord administrateur
Le composant AdminDashboard calcule des statistiques sur les rendez‑vous (validés, en attente, refusés) et affiche un calendrier global ainsi que la gestion des limites journalières pour chaque employé.

Gestion des disponibilités des employés
Les employés peuvent ajouter ou supprimer leurs créneaux via DisponibilitesManager. Un calendrier personnel affiche ensuite disponibilités et rendez‑vous confirmés.

Notifications et toasts
Un composant Vue/Alpine affiche des toasts animés en bas de page, avec un son de succès ou d’erreur pour renforcer l’interactivité

Outils en ligne de commande
Plusieurs commandes artisan facilitent le développement : par exemple dispo:generer pour créer des disponibilités de test ou des vérifications de composants Livewire.

Seeders et paramètres
Le DatabaseSeeder insère des valeurs par défaut (feedback de test, limites journalières, paramètre global « duree_creneau »). Un helper parametre() simplifie la lecture de ces paramètres

Points de repère pour approfondir
Comprendre Laravel : lire la documentation officielle sur les migrations, Eloquent, les middlewares et les notifications.

Jetstream et Livewire : se familiariser avec la structure de Jetstream (authentification, équipes, API tokens) et avec le cycle de vie des composants Livewire pour manipuler facilement les vues dynamiques.

Gestion des assets : observer comment Vite compile resources/css/app.css et resources/js/app.js, et comment Tailwind est configuré dans tailwind.config.js.

Base de données : étudier les migrations pour voir comment sont reliées les entités (rendez‑vous, disponibilités, limites journalières, feedback).

Tests : la suite tests/ montre des exemples de tests Laravel ; s’en inspirer pour ajouter ses propres scénarios.

En suivant ces repères et en parcourant progressivement les dossiers mentionnés, on peut se familiariser avec le code et comprendre comment l’application gère la prise de rendez‑vous de façon interactive.