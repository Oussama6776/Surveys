<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title ?? 'Sondages' }}</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900">
  <nav class="bg-white border-b shadow-sm">
    <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
      <a href="{{ route('surveys.index') }}" class="font-semibold">Sondages</a>
      <div>
        @auth
          <span class="mr-3">{{ auth()->user()->name }}</span>
          <form class="inline" method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="text-sm text-red-600">Déconnexion</button>
          </form>
        @endauth
      </div>
    </div>
  </nav>
  <main class="max-w-6xl mx-auto p-4">
    @if (session('success'))
      <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
      <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
        <ul class="list-disc list-inside">
          @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif
    {{ $slot ?? '' }}
    @yield('content')
  </main>
</body>
</html>

