# 🚀 Survey Tool - Nouvelles Fonctionnalités

Ce document décrit toutes les nouvelles fonctionnalités ajoutées à l'application Survey Tool.

## 📊 Analytics & Reporting

### Fonctionnalités
- **Tableaux de bord avancés** avec métriques en temps réel
- **Graphiques interactifs** (barres, lignes, secteurs, radar)
- **Analyse des tendances** avec filtres par période
- **Rapports PDF exportables** avec mise en page professionnelle
- **Export Excel/CSV** des données d'analytics
- **Nuage de mots** pour l'analyse des réponses textuelles
- **Statistiques de completion** par question
- **Analyse démographique** (appareils, navigateurs, localisation)

### Utilisation
```php
// Accès aux analytics via l'API
GET /api/v1/surveys/{survey}/analytics/overview
GET /api/v1/surveys/{survey}/analytics/questions
GET /api/v1/surveys/{survey}/analytics/export
```

## 🎨 Interface & UX

### Thèmes Personnalisables
- **Système de thèmes** avec couleurs, polices et styles personnalisés
- **Prévisualisation en temps réel** des modifications
- **Import/Export de thèmes** en format JSON
- **Thèmes prédéfinis** (Default, Dark, Corporate, Colorful)
- **CSS personnalisé** pour des modifications avancées

### Fonctionnalités UX
- **Drag & Drop** pour la réorganisation des questions
- **Interface responsive** optimisée pour tous les appareils
- **Prévisualisation** des enquêtes avant publication
- **Navigation multi-pages** avec barre de progression
- **Animations fluides** et transitions

## 🔧 Fonctionnalités Avancées

### Logique Conditionnelle
- **Questions conditionnelles** basées sur les réponses précédentes
- **Conditions multiples** avec opérateurs (equals, contains, greater_than, etc.)
- **Actions conditionnelles** (show, hide, require, optional)
- **Interface visuelle** pour la configuration des conditions

### Enquêtes Multi-pages
- **Pagination automatique** des questions
- **Navigation avant/arrière** entre les pages
- **Sauvegarde de progression** pour les utilisateurs
- **Barre de progression** visuelle

### Limitations et Contrôles
- **Limitation du nombre de réponses** par enquête
- **Limitation par utilisateur/IP** pour éviter les doublons
- **Codes d'accès** pour les enquêtes privées
- **Programmation automatique** (démarrage/arrêt)

## 🔒 Sécurité & Contrôle

### Gestion des Rôles
- **Système de rôles** (Super Admin, Admin, Créateur, Lecteur)
- **Permissions granulaires** par fonctionnalité
- **Attribution de rôles** aux utilisateurs
- **Audit trail** des actions utilisateur

### Enquêtes Privées
- **Codes d'accès** avec expiration et limite d'utilisation
- **Authentification requise** pour certaines enquêtes
- **Contrôle d'accès** basé sur les rôles
- **Logs de sécurité** pour le suivi des accès

### Validation Avancée
- **Validation côté client et serveur**
- **Règles de validation personnalisées**
- **Messages d'erreur contextuels**
- **Validation en temps réel**

## 🔗 Intégrations

### API REST
- **API complète** pour toutes les fonctionnalités
- **Authentification Sanctum** pour la sécurité
- **Documentation automatique** des endpoints
- **Rate limiting** pour la protection
- **Format JSON standardisé**

### Webhooks
- **Notifications en temps réel** des événements
- **Événements configurables** (réponse soumise, enquête créée, etc.)
- **Retry automatique** en cas d'échec
- **Logs d'exécution** pour le debugging
- **Test des webhooks** depuis l'interface

### Import/Export
- **Import de données** depuis Excel/CSV
- **Export multi-formats** (PDF, Excel, CSV, JSON)
- **Templates d'import** pour faciliter l'utilisation
- **Validation des données** importées

## 🎯 Types de Questions Avancés

### Questions de Notation
- **Échelle de notation** (1-10, 1-5, etc.)
- **Étoiles** avec support des demi-étoiles
- **Émojis** pour les évaluations
- **Labels personnalisés** (Poor, Excellent, etc.)

### Questions de Classement
- **Classement par glisser-déposer**
- **Limites de classement** (min/max)
- **Autorisation des égalités**
- **Direction de classement** (ascendant/descendant)

### Upload de Fichiers
- **Support multi-formats** (images, documents, vidéos, audio)
- **Validation des types** et tailles
- **Prévisualisation** des fichiers
- **Stockage sécurisé** avec contrôle d'accès

### Questions Géographiques
- **Sélecteur de localisation** avec carte
- **Restriction par pays/région**
- **Coordonnées précises** ou approximatives
- **Intégration Google Maps**

### Autres Types
- **Date/Heure** avec sélecteur visuel
- **Email** avec validation
- **Téléphone** avec format international
- **URL** avec validation
- **Nombre** avec min/max
- **Slider** pour les valeurs numériques
- **Matrice** pour les questions groupées

## 🗄️ Base de Données

### Nouvelles Tables
- `roles` - Gestion des rôles utilisateur
- `user_roles` - Association utilisateurs-rôles
- `survey_themes` - Thèmes personnalisés
- `question_conditions` - Logique conditionnelle
- `survey_pages` - Pages des enquêtes multi-pages
- `audit_logs` - Logs d'audit
- `survey_access_codes` - Codes d'accès
- `webhooks` - Configuration des webhooks
- `survey_files` - Fichiers uploadés
- `question_ratings` - Configuration des notations
- `question_rankings` - Configuration des classements
- `question_locations` - Configuration géographique

### Modifications des Tables Existantes
- `surveys` - Ajout de champs avancés (thème, limitations, etc.)
- `questions` - Ajout de champs pour la logique conditionnelle
- `question_types` - Nouveaux types de questions

## 🎨 Assets Frontend

### CSS Personnalisé
- **Variables CSS** pour les thèmes
- **Classes utilitaires** pour les fonctionnalités
- **Animations** et transitions
- **Design responsive** mobile-first

### JavaScript Modulaire
- **Classes ES6** pour l'organisation du code
- **Gestion des événements** avancée
- **API asynchrone** pour les interactions
- **Validation côté client** en temps réel

### Bibliothèques Intégrées
- **Chart.js** pour les graphiques
- **jsPDF** pour l'export PDF
- **SheetJS** pour l'export Excel
- **SortableJS** pour le drag & drop
- **Flatpickr** pour les sélecteurs de date

## 🚀 Installation et Configuration

### Prérequis
```bash
# PHP 8.2+
# Composer
# Node.js 18+
# Base de données (MySQL/PostgreSQL/SQLite)
```

### Installation
```bash
# Cloner le projet
git clone <repository-url>
cd Survey-tool

# Installer les dépendances PHP
composer install

# Installer les dépendances Node.js
npm install

# Configuration
cp .env.example .env
php artisan key:generate

# Base de données
php artisan migrate
php artisan db:seed

# Assets
npm run build
```

### Configuration
```env
# Thèmes
THEME_DEFAULT=default
THEME_CACHE=true

# Analytics
ANALYTICS_CACHE_ENABLED=true
ANALYTICS_REAL_TIME_ENABLED=false

# API
API_KEY=your-secret-api-key
SANCTUM_EXPIRATION=525600

# Webhooks
WEBHOOK_DEFAULT_TIMEOUT=30
WEBHOOK_DEFAULT_RETRY_COUNT=3

# Fichiers
FILE_MAX_SIZE=10240
FILE_STORAGE_DISK=local
```

## 📚 Utilisation

### Création d'une Enquête Avancée
1. **Créer l'enquête** avec les paramètres de base
2. **Sélectionner un thème** ou créer un thème personnalisé
3. **Ajouter des questions** avec les nouveaux types
4. **Configurer la logique conditionnelle** si nécessaire
5. **Paramétrer les limitations** et codes d'accès
6. **Publier l'enquête** et partager le lien

### Gestion des Analytics
1. **Accéder au tableau de bord** analytics
2. **Sélectionner l'enquête** à analyser
3. **Utiliser les filtres** pour affiner les données
4. **Exporter les rapports** au format souhaité
5. **Configurer les webhooks** pour les notifications

### Administration
1. **Gérer les rôles** et permissions
2. **Créer des thèmes** personnalisés
3. **Configurer les webhooks** pour les intégrations
4. **Surveiller les logs** d'audit
5. **Gérer les fichiers** uploadés

## 🔧 Développement

### Structure du Code
```
app/
├── Http/Controllers/
│   ├── AnalyticsController.php
│   ├── ThemeController.php
│   ├── RoleController.php
│   ├── WebhookController.php
│   ├── FileController.php
│   └── Api/
├── Models/
│   ├── Role.php
│   ├── SurveyTheme.php
│   ├── QuestionCondition.php
│   └── ...
└── Providers/
    └── SurveyServiceProvider.php

resources/
├── views/
│   ├── analytics/
│   ├── themes/
│   ├── roles/
│   └── ...
├── css/
│   └── survey-advanced.css
└── js/
    ├── survey-advanced.js
    ├── analytics.js
    └── themes.js
```

### API Endpoints
```
GET    /api/v1/surveys                    # Liste des enquêtes
POST   /api/v1/surveys                    # Créer une enquête
GET    /api/v1/surveys/{id}               # Détails d'une enquête
PUT    /api/v1/surveys/{id}               # Modifier une enquête
DELETE /api/v1/surveys/{id}               # Supprimer une enquête

GET    /api/v1/surveys/{id}/analytics     # Analytics d'une enquête
GET    /api/v1/surveys/{id}/responses     # Réponses d'une enquête
POST   /api/v1/surveys/{id}/responses     # Soumettre une réponse
```

## 🐛 Dépannage

### Problèmes Courants
1. **Erreurs de permissions** - Vérifier les rôles utilisateur
2. **Problèmes de thèmes** - Vérifier la configuration CSS
3. **Webhooks qui échouent** - Vérifier les logs et la configuration
4. **Problèmes d'upload** - Vérifier les permissions de stockage
5. **Erreurs d'API** - Vérifier l'authentification et les tokens

### Logs
```bash
# Logs Laravel
tail -f storage/logs/laravel.log

# Logs des webhooks
tail -f storage/logs/webhooks.log

# Logs d'audit
tail -f storage/logs/audit.log
```

## 📈 Performance

### Optimisations
- **Cache des analytics** pour améliorer les performances
- **Pagination** des grandes listes
- **Lazy loading** des images et fichiers
- **Compression** des assets CSS/JS
- **CDN** pour les fichiers statiques

### Monitoring
- **Métriques de performance** intégrées
- **Surveillance des erreurs** en temps réel
- **Alertes automatiques** pour les problèmes
- **Rapports de performance** périodiques

## 🔮 Roadmap

### Fonctionnalités Futures
- [ ] **Intelligence artificielle** pour l'analyse des réponses
- [ ] **Intégration CRM** (Salesforce, HubSpot)
- [ ] **Notifications push** pour les réponses
- [ ] **Gamification** des enquêtes
- [ ] **Templates d'enquêtes** prédéfinis
- [ ] **Collaboration en équipe** sur les enquêtes
- [ ] **Versioning** des enquêtes
- [ ] **A/B Testing** intégré

---

## 📞 Support

Pour toute question ou problème :
- **Documentation** : Consulter ce fichier et les commentaires du code
- **Issues** : Créer une issue sur le repository
- **Email** : support@surveytool.com

---

*Dernière mise à jour : $(date)*
