# 🔐 Guide des Rôles et Permissions - Survey Tool

Ce guide explique comment utiliser et gérer le système de rôles et permissions dans votre application Survey Tool.

## 📋 Vue d'ensemble des Rôles

### 1. **Super Administrateur** (`super_admin`)
- **Accès** : Complet au système
- **Permissions** : Toutes les permissions (`*`)
- **Utilisation** : Gestion complète du système, accès à toutes les fonctionnalités

### 2. **Administrateur** (`admin`)
- **Accès** : Gestion des utilisateurs et du système
- **Permissions** : 
  - Gestion des utilisateurs (créer, modifier, supprimer, attribuer des rôles)
  - Gestion de toutes les enquêtes
  - Analytics et rapports complets
  - Configuration système
  - Gestion des thèmes, fichiers, webhooks

### 3. **Créateur d'Enquêtes** (`survey_creator`)
- **Accès** : Création et gestion de ses propres enquêtes
- **Permissions** :
  - Créer, modifier, supprimer ses propres enquêtes
  - Gérer les questions de ses enquêtes
  - Voir les réponses et analytics de ses enquêtes
  - Utiliser les thèmes disponibles
  - Uploader des fichiers pour ses enquêtes
  - Configurer des webhooks pour ses enquêtes

### 4. **Lecteur d'Enquêtes** (`survey_viewer`)
- **Accès** : Consultation en lecture seule
- **Permissions** :
  - Voir toutes les enquêtes
  - Consulter les questions et réponses
  - Accéder aux analytics en lecture seule
  - Télécharger les fichiers

### 5. **Client** (`client`)
- **Accès** : Répondre aux enquêtes publiques
- **Permissions** :
  - Répondre aux enquêtes publiques
  - Télécharger les fichiers nécessaires

### 6. **Modérateur** (`moderator`)
- **Accès** : Modération du contenu
- **Permissions** :
  - Modérer le contenu des enquêtes
  - Voir les analytics
  - Télécharger les fichiers

## 🛠️ Comment Utiliser le Système de Rôles

### Attribution de Rôles

1. **Via l'interface web** :
   - Aller dans "Gestion des Utilisateurs"
   - Cliquer sur l'icône "Gérer les rôles" d'un utilisateur
   - Sélectionner les rôles appropriés
   - Sauvegarder

2. **Via le code** :
```php
// Attribuer un rôle
$user->assignRole('survey_creator');

// Vérifier un rôle
if ($user->hasRole('admin')) {
    // L'utilisateur est admin
}

// Vérifier une permission
if ($user->hasPermission('surveys.create')) {
    // L'utilisateur peut créer des enquêtes
}
```

### Vérification des Permissions

```php
// Dans un contrôleur
public function index()
{
    $this->middleware('permission:surveys.read');
    // ou
    if (!auth()->user()->hasPermission('surveys.read')) {
        abort(403);
    }
}

// Dans une vue Blade
@can('surveys.create')
    <a href="{{ route('surveys.create') }}">Créer une enquête</a>
@endcan

@if(auth()->user()->hasRole('admin'))
    <div class="admin-panel">Panel administrateur</div>
@endif
```

### Middleware de Protection

```php
// Protection par rôle
Route::middleware(['role:admin'])->group(function () {
    Route::get('/admin', 'AdminController@index');
});

// Protection par permission
Route::middleware(['permission:users.create'])->group(function () {
    Route::post('/users', 'UserController@store');
});

// Protection par propriété
Route::middleware(['ownership:survey'])->group(function () {
    Route::put('/surveys/{survey}', 'SurveyController@update');
});
```

## 🔒 Sécurité et Bonnes Pratiques

### 1. **Principe du Moindre Privilège**
- Donnez aux utilisateurs seulement les permissions nécessaires
- Évitez d'attribuer le rôle `super_admin` sauf si absolument nécessaire

### 2. **Séparation des Responsabilités**
- **Super Admin** : Configuration système, maintenance
- **Admin** : Gestion des utilisateurs, supervision
- **Créateur** : Création de contenu
- **Client** : Utilisation du service

### 3. **Audit et Traçabilité**
- Tous les changements de rôles sont loggés
- Les actions sensibles sont tracées
- Les tentatives d'accès non autorisées sont enregistrées

## 📊 Exemples d'Utilisation

### Scénario 1 : Entreprise avec Équipe Marketing
```
- 1 Super Admin (IT Manager)
- 2 Admins (Marketing Manager, Product Manager)
- 5 Créateurs d'Enquêtes (Marketing Team)
- 2 Lecteurs (Analytics Team)
- 1000+ Clients (Clients finaux)
```

### Scénario 2 : Agence de Recherche
```
- 1 Super Admin (Directeur)
- 1 Admin (Responsable Projets)
- 10 Créateurs (Chercheurs)
- 3 Modérateurs (Contrôle Qualité)
- 500+ Clients (Participants)
```

### Scénario 3 : Startup
```
- 1 Super Admin (CTO)
- 1 Admin (CEO)
- 3 Créateurs (Product Team)
- 100+ Clients (Utilisateurs)
```

## 🚀 Commandes Utiles

### Gestion des Rôles via Artisan
```bash
# Créer un utilisateur avec un rôle
php artisan tinker
>>> $user = User::create(['name' => 'John', 'email' => 'john@example.com', 'password' => bcrypt('password')]);
>>> $user->assignRole('survey_creator');

# Vérifier les rôles d'un utilisateur
>>> $user->roles;
>>> $user->hasRole('admin');

# Lister tous les rôles
>>> Role::all();
```

### Maintenance
```bash
# Nettoyer les permissions orphelines
php artisan surveytool:maintenance --cleanup-logs

# Vérifier l'intégrité des rôles
php artisan tinker
>>> User::whereDoesntHave('roles')->count();
```

## 🔧 Personnalisation

### Ajouter un Nouveau Rôle
1. Créer le rôle dans la base de données
2. Définir les permissions dans `RolePermissionSeeder`
3. Mettre à jour les middlewares si nécessaire
4. Tester les permissions

### Ajouter une Nouvelle Permission
1. Définir la permission dans le seeder
2. L'ajouter aux rôles appropriés
3. Utiliser la permission dans les contrôleurs/vues
4. Tester l'accès

## 📝 Logs et Monitoring

### Vérifier les Logs d'Accès
```bash
tail -f storage/logs/laravel.log | grep "403\|permission\|role"
```

### Monitoring des Rôles
```php
// Dans un contrôleur
public function dashboard()
{
    $roleStats = Role::withCount('users')->get();
    $permissionStats = collect(config('permissions'))->map(function($permissions, $group) {
        return [
            'group' => $group,
            'count' => count($permissions)
        ];
    });
    
    return view('admin.dashboard', compact('roleStats', 'permissionStats'));
}
```

## ⚠️ Points d'Attention

1. **Ne jamais supprimer le dernier Super Admin**
2. **Toujours tester les permissions après modification**
3. **Documenter les changements de rôles**
4. **Sauvegarder avant les modifications importantes**
5. **Utiliser HTTPS en production pour la sécurité**

---

## 📞 Support

Pour toute question sur le système de rôles :
- Consultez les logs : `storage/logs/laravel.log`
- Vérifiez les permissions : `php artisan tinker`
- Testez les accès : Interface web ou API

*Dernière mise à jour : $(date)*
