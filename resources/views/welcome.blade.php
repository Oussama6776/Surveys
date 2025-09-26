<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SondagesPro – Créez, partagez et analysez vos sondages</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="antialiased bg-gray-50">
    <!-- Navigation -->
    <header class="bg-white/90 backdrop-filter backdrop-blur shadow-sm sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="/" class="flex items-center space-x-2">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-600 text-white font-bold">S</span>
                    <span class="text-xl font-bold text-gray-900">SondagesPro</span>
                </a>

                <nav class="hidden md:flex items-center space-x-6 text-sm">
                    <a href="#features" class="text-gray-600 hover:text-gray-900">Fonctionnalités</a>
                    <a href="#how-it-works" class="text-gray-600 hover:text-gray-900">Comment ça marche</a>
                    <a href="#faq" class="text-gray-600 hover:text-gray-900">FAQ</a>
                </nav>

                <div class="flex items-center space-x-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="hidden sm:inline-flex px-4 py-2 rounded-md border border-gray-200 text-gray-700 hover:bg-gray-50">Tableau de bord</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">Se déconnecter</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 rounded-md text-gray-700 hover:text-indigo-700">Connexion</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">Inscription</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Hero -->
    <section class="relative overflow-hidden bg-gradient-to-br from-indigo-50 via-white to-purple-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">Plateforme de sondages tout-en-un</span>
                    <h1 class="mt-4 text-4xl sm:text-5xl font-extrabold text-gray-900 leading-tight">
                        Créez, partagez et analysez<br class="hidden sm:block"/> vos sondages en quelques minutes
                    </h1>
                    <p class="mt-4 text-gray-600 text-lg">
                        Construisez des questionnaires attrayants, gérez l’accès (public/privé), et obtenez des insights clairs avec des dashboards intégrés.
                    </p>
                    <div class="mt-8 flex flex-col sm:flex-row sm:items-center sm:space-x-3 space-y-3 sm:space-y-0">
                        @auth
                            <a href="{{ route('surveys.create') }}" class="inline-flex items-center justify-center px-6 py-3 rounded-md bg-indigo-600 text-white hover:bg-indigo-700 font-medium">Nouveau sondage</a>
                            <a href="{{ route('surveys.index') }}" class="inline-flex items-center justify-center px-6 py-3 rounded-md border border-gray-200 text-gray-700 hover:bg-gray-50 font-medium">Gérer mes sondages</a>
                        @else
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-6 py-3 rounded-md bg-indigo-600 text-white hover:bg-indigo-700 font-medium">Créer un compte</a>
                            <a href="{{ url('/surveys/public') }}" class="inline-flex items-center justify-center px-6 py-3 rounded-md border border-gray-200 text-gray-700 hover:bg-gray-50 font-medium">Découvrir les sondages publics</a>
                        @endauth
                    </div>

                    <div class="mt-8 grid grid-cols-3 gap-6">
                        <div>
                            <div class="text-2xl font-bold text-gray-900">10k+</div>
                            <div class="text-sm text-gray-500">Réponses collectées</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900">1k+</div>
                            <div class="text-sm text-gray-500">Sondages créés</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900">99.9%</div>
                            <div class="text-sm text-gray-500">Disponibilité</div>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <div class="absolute -inset-4 bg-gradient-to-tr from-indigo-200 to-purple-200 rounded-3xl transform rotate-2 opacity-60"></div>
                    <div class="relative bg-white rounded-3xl shadow-xl p-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 rounded-xl border border-gray-100 bg-gray-50">
                                <div class="text-sm font-semibold text-gray-900">Glisser-déposer</div>
                                <p class="mt-1 text-sm text-gray-600">Construisez vos questions facilement.</p>
                            </div>
                            <div class="p-4 rounded-xl border border-gray-100 bg-gray-50">
                                <div class="text-sm font-semibold text-gray-900">Logique conditionnelle</div>
                                <p class="mt-1 text-sm text-gray-600">Affichez des questions selon les réponses.</p>
                            </div>
                            <div class="p-4 rounded-xl border border-gray-100 bg-gray-50">
                                <div class="text-sm font-semibold text-gray-900">Thèmes personnalisés</div>
                                <p class="mt-1 text-sm text-gray-600">Adaptez l’apparence à votre marque.</p>
                            </div>
                            <div class="p-4 rounded-xl border border-gray-100 bg-gray-50">
                                <div class="text-sm font-semibold text-gray-900">Analyses en temps réel</div>
                                <p class="mt-1 text-sm text-gray-600">Graphiques et exports instantanés.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section id="features" class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-extrabold text-gray-900">Tout ce dont vous avez besoin</h2>
                <p class="mt-3 text-gray-600">De la création à l’analyse, maîtrisez tout votre cycle de sondage.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="p-6 bg-white rounded-xl shadow border border-gray-100">
                    <div class="h-10 w-10 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold">1</div>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">Éditeur intuitif</h3>
                    <p class="mt-1 text-gray-600">Questions multiples: choix, échelle, classement, fichiers, localisation, etc.</p>
                </div>
                <div class="p-6 bg-white rounded-xl shadow border border-gray-100">
                    <div class="h-10 w-10 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold">2</div>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">Contrôle d’accès</h3>
                    <p class="mt-1 text-gray-600">Sondages publics/privés, codes d’accès, rôles et permissions fines.</p>
                </div>
                <div class="p-6 bg-white rounded-xl shadow border border-gray-100">
                    <div class="h-10 w-10 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold">3</div>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">Thèmes et branding</h3>
                    <p class="mt-1 text-gray-600">Choisissez un thème ou personnalisez couleurs, logo et mise en page.</p>
                </div>
                <div class="p-6 bg-white rounded-xl shadow border border-gray-100">
                    <div class="h-10 w-10 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold">4</div>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">Analytics avancés</h3>
                    <p class="mt-1 text-gray-600">Tableaux de bord, export CSV/Excel, webhooks et intégrations.</p>
                </div>
                <div class="p-6 bg-white rounded-xl shadow border border-gray-100">
                    <div class="h-10 w-10 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold">5</div>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">Logique & conditions</h3>
                    <p class="mt-1 text-gray-600">Affichage conditionnel, sauts de pages, validations, quotas.</p>
                </div>
                <div class="p-6 bg-white rounded-xl shadow border border-gray-100">
                    <div class="h-10 w-10 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold">6</div>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">Collaboration</h3>
                    <p class="mt-1 text-gray-600">Invitations, rôles d’équipe (admin, éditeur, lecteur) et historique.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How it works -->
    <section id="how-it-works" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-extrabold text-gray-900">Comment ça marche</h2>
                <p class="mt-3 text-gray-600">Trois étapes simples pour des réponses utiles.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-6 rounded-xl border border-gray-100 bg-gray-50">
                    <div class="text-sm font-semibold text-gray-900">1. Créez</div>
                    <p class="mt-1 text-gray-600">Composez vos questions avec l’éditeur, ajoutez la logique et personnalisez le thème.</p>
                </div>
                <div class="p-6 rounded-xl border border-gray-100 bg-gray-50">
                    <div class="text-sm font-semibold text-gray-900">2. Partagez</div>
                    <p class="mt-1 text-gray-600">Lien public, accès privé, QR code, email ou intégration à votre site.</p>
                </div>
                <div class="p-6 rounded-xl border border-gray-100 bg-gray-50">
                    <div class="text-sm font-semibold text-gray-900">3. Analysez</div>
                    <p class="mt-1 text-gray-600">Visualisez les résultats en temps réel et exportez vos données.</p>
                </div>
            </div>
            <div class="mt-10 text-center">
                @auth
                <a href="{{ route('surveys.create') }}" class="inline-flex items-center justify-center px-6 py-3 rounded-md bg-indigo-600 text-white hover:bg-indigo-700 font-medium">Créer mon premier sondage</a>
                @else
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-6 py-3 rounded-md bg-indigo-600 text-white hover:bg-indigo-700 font-medium">Essayer gratuitement</a>
                @endauth
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-extrabold text-gray-900">Ils nous font confiance</h2>
                <p class="mt-3 text-gray-600">Des équipes de toutes tailles utilisent SondagesPro.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-6 bg-white rounded-xl shadow border border-gray-100">
                    <p class="text-gray-700">“Un gain de temps énorme pour nos enquêtes de satisfaction. Les dashboards sont top.”</p>
                    <div class="mt-4 flex items-center space-x-3">
                        <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center font-bold text-indigo-700">A</div>
                        <div>
                            <div class="text-sm font-semibold text-gray-900">Amel B.</div>
                            <div class="text-xs text-gray-500">Responsable Qualité</div>
                        </div>
                    </div>
                </div>
                <div class="p-6 bg-white rounded-xl shadow border border-gray-100">
                    <p class="text-gray-700">“Très simple à prendre en main et suffisamment puissant pour des études complexes.”</p>
                    <div class="mt-4 flex items-center space-x-3">
                        <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center font-bold text-indigo-700">M</div>
                        <div>
                            <div class="text-sm font-semibold text-gray-900">Marc D.</div>
                            <div class="text-xs text-gray-500">Chef de Projet</div>
                        </div>
                    </div>
                </div>
                <div class="p-6 bg-white rounded-xl shadow border border-gray-100">
                    <p class="text-gray-700">“Nous avons doublé le taux de réponse grâce aux thèmes personnalisés et aux invitations.”</p>
                    <div class="mt-4 flex items-center space-x-3">
                        <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center font-bold text-indigo-700">S</div>
                        <div>
                            <div class="text-sm font-semibold text-gray-900">Sarah L.</div>
                            <div class="text-xs text-gray-500">Marketing</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    

    <!-- FAQ -->
    <section id="faq" class="py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <h2 class="text-3xl font-extrabold text-gray-900">Questions fréquentes</h2>
                <p class="mt-3 text-gray-600">Tout ce que vous devez savoir pour démarrer.</p>
            </div>
            <div class="space-y-4">
                <details class="p-4 bg-white rounded-xl border border-gray-200">
                    <summary class="cursor-pointer font-semibold text-gray-900">Puis-je créer des sondages privés ?</summary>
                    <p class="mt-2 text-gray-600">Oui, vous pouvez restreindre l’accès via des codes, rôles ou invitations par email.</p>
                </details>
                <details class="p-4 bg-white rounded-xl border border-gray-200">
                    <summary class="cursor-pointer font-semibold text-gray-900">Proposez-vous des exports des réponses ?</summary>
                    <p class="mt-2 text-gray-600">Oui, vous pouvez exporter en CSV/Excel et connecter des webhooks.</p>
                </details>
                <details class="p-4 bg-white rounded-xl border border-gray-200">
                    <summary class="cursor-pointer font-semibold text-gray-900">Est-ce compatible mobile ?</summary>
                    <p class="mt-2 text-gray-600">Bien sûr, l’éditeur et les sondages sont entièrement responsive.</p>
                </details>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-gray-200 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div class="flex items-center space-x-2">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-600 text-white font-bold">S</span>
                    <span class="text-gray-900 font-semibold">SondagesPro</span>
                </div>
                <div class="flex items-center space-x-6 text-sm">
                    <a href="{{ url('/surveys/public') }}" class="text-gray-600 hover:text-gray-900">Sondages publics</a>
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900">Connexion</a>
                    <a href="{{ route('register') }}" class="text-gray-600 hover:text-gray-900">Inscription</a>
                </div>
                <p class="text-sm text-gray-500">© {{ date('Y') }} SondagesPro. Tous droits réservés.</p>
            </div>
        </div>
    </footer>
</body>
</html>
