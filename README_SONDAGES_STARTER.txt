
Sondages Starter (Laravel feature skeleton)
==========================================

Ce package contient les fichiers (modèles, migrations, contrôleurs, vues Blade, services, seeders, routes) à copier
dans un projet Laravel vierge afin d'obtenir un MVP fonctionnel : 
- Auth (via Breeze recommandé)
- CRUD sondages et questions
- Participation publique via un token de sondage
- Unicité de participation par token (IP simplifié côté serveur)
- Liste paginée des réponses et détail
- Statistiques basiques + export CSV

Étapes pour démarrer (Laravel 11 recommandé) :
----------------------------------------------
1) Créer un nouveau projet :
   composer create-project laravel/laravel sondages
   cd sondages

2) (Optionnel mais recommandé) Installer Breeze (Blade) :
   composer require laravel/breeze --dev
   php artisan breeze:install
   npm install && npm run build

3) Copier le contenu de ce zip **par-dessus** votre projet Laravel.
   - Acceptez d'écraser routes/web.php si demandé, ou fusionnez manuellement.

4) Lancer les migrations et seeders :
   php artisan migrate --seed

5) Démarrer le serveur :
   php artisan serve

6) Se connecter (si Breeze) ou créer un compte puis :
   - Créer un sondage
   - Ajouter des questions
   - Ouvrir le lien public /p/{public_token} pour répondre
   - Voir les stats et exporter en CSV

