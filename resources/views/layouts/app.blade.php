<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Survey Tool'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Styles -->
    <style>
        :root {
            /* Brand indigo (aligned with landing Tailwind indigo/violet) */
            --brand-primary: #6366f1; /* indigo-500 */
            --brand-primary-rgb: 99, 102, 241;
            --brand-secondary: #8b5cf6; /* violet-500 */

            /* Bootstrap theme overrides */
            --bs-primary: var(--brand-primary);
            --bs-primary-rgb: var(--brand-primary-rgb);
            --bs-link-color: var(--brand-primary);
            --bs-link-hover-color: #4f46e5; /* indigo-600 */
            --bs-border-color: #e5e7eb; /* light gray border */
        }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            border-radius: 0.5rem;
            margin: 0.25rem 0;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }
        .sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        .main-content {
            background-color: #f8f9fc;
            min-height: 100vh;
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        /* Guest main spacing/background can remain neutral; colors harmonized via vars */
        .guest-main a { color: var(--bs-link-color); }
        .guest-main a:hover { color: var(--bs-link-hover-color); }

        /* Harmonize Bootstrap button colors with brand */
        .btn-primary {
            --bs-btn-bg: var(--brand-primary);
            --bs-btn-border-color: var(--brand-primary);
            --bs-btn-hover-bg: #4f46e5; /* indigo-600 */
            --bs-btn-hover-border-color: #4f46e5;
            --bs-btn-active-bg: #4338ca; /* indigo-700 */
            --bs-btn-active-border-color: #4338ca;
            --bs-btn-focus-shadow-rgb: var(--brand-primary-rgb);
        }
        .btn-outline-primary {
            --bs-btn-color: var(--brand-primary);
            --bs-btn-border-color: var(--brand-primary);
            --bs-btn-hover-bg: var(--brand-primary);
            --bs-btn-hover-border-color: var(--brand-primary);
            --bs-btn-active-bg: #4f46e5;
            --bs-btn-active-border-color: #4f46e5;
            --bs-btn-disabled-color: var(--brand-primary);
            --bs-btn-disabled-border-color: var(--brand-primary);
        }
    </style>
    
    @stack('styles')
</head>
<body>
    @if(View::hasSection('guest') || (isset($guestLayout) && $guestLayout))
    <!-- Guest Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="/">
                <span class="d-inline-flex align-items-center justify-content-center rounded-2 bg-primary text-white fw-bold me-2" style="width:32px;height:32px;">S</span>
                SondagesPro
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#guestNavbar" aria-controls="guestNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="guestNavbar">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    @auth
                        <li class="nav-item me-2">
                            <a class="btn btn-outline-secondary" href="{{ route('dashboard') }}">Tableau de bord</a>
                        </li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-primary">Déconnexion</button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Connexion</a></li>
                        <li class="nav-item ms-lg-2"><a class="btn btn-primary" href="{{ route('register') }}">Inscription</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Guest Main Content -->
    <main class="guest-main">
        <div class="container py-4 py-md-5">
            <!-- Flash Messages (Guest) -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle"></i> {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>
    @else
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <!-- Logo -->
                    <div class="text-center mb-4">
                        <a href="/" class="navbar-brand text-white">
                            <i class="fas fa-poll"></i> SondagesPro
                        </a>
                    </div>

                    <!-- Navigation -->
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" 
                               href="{{ route('dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i>
                                Tableau de bord
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('surveys.*') ? 'active' : '' }}" 
                               href="{{ route('surveys.index') }}">
                                <i class="fas fa-poll"></i>
                                Sondages
                            </a>
                        </li>

                        @auth
                        @if(auth()->user()->hasAnyRole(['super_admin', 'admin']) || auth()->user()->hasPermission('users.read'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" 
                               href="{{ route('users.index') }}">
                                <i class="fas fa-users"></i>
                                Utilisateurs
                            </a>
                        </li>
                        @endif
                        @endauth

                        @can('analytics.read')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('analytics.*') ? 'active' : '' }}" 
                               href="{{ route('analytics.index') }}">
                                <i class="fas fa-chart-bar"></i>
                                Analytics
                            </a>
                        </li>
                        @endcan

                        @can('themes.read')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('themes.*') ? 'active' : '' }}" 
                               href="{{ route('themes.index') }}">
                                <i class="fas fa-palette"></i>
                                Thèmes
                            </a>
                        </li>
                        @endcan

                        @can('roles.read')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}" 
                               href="{{ route('roles.index') }}">
                                <i class="fas fa-user-shield"></i>
                                Rôles
                            </a>
                        </li>
                        @endcan

                        @can('webhooks.read')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('webhooks.*') ? 'active' : '' }}" 
                               href="{{ route('webhooks.index') }}">
                                <i class="fas fa-plug"></i>
                                Webhooks
                            </a>
                        </li>
                        @endcan

                        @can('files.read')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('files.*') ? 'active' : '' }}" 
                               href="{{ route('files.index') }}">
                                <i class="fas fa-file-upload"></i>
                                Fichiers
                            </a>
                        </li>
                        @endcan
                    </ul>

                    <!-- User Info -->
                    @auth
                    <div class="mt-auto pt-3">
                        <div class="text-center">
                            <div class="user-avatar mx-auto mb-2">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <small class="text-white-50">{{ auth()->user()->name }}</small>
                            <br>
                            <small class="text-white-50">
                                @foreach(auth()->user()->roles as $role)
                                    <span class="badge bg-light text-dark">{{ $role->display_name }}</span>
                                @endforeach
                            </small>
                        </div>
                    </div>
                    @endauth
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <!-- Top Navigation -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <div>
                        <h1 class="h2">@yield('page-title', 'Tableau de bord')</h1>
                        <p class="text-muted">@yield('page-description', 'Bienvenue dans votre tableau de bord')</p>
                    </div>
                    
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            @auth
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" 
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-user"></i> {{ auth()->user()->name }}
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('profile') }}">
                                                <i class="fas fa-user-edit"></i> Mon Profil
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-sign-in-alt"></i> Connexion
                                </a>
                                <a href="{{ route('register') }}" class="btn btn-primary">
                                    <i class="fas fa-user-plus"></i> Inscription
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>

                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="fas fa-info-circle"></i> {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Page Content -->
                @yield('content')
            </main>
        </div>
    </div>
    @endif

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @stack('scripts')
</body>
</html> 
