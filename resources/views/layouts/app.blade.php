<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>

    {{-- Use Vite assets if available --}}
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    {{-- Minimal Bootstrap via CDN for basic CRUD UI --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">{{ config('app.name', 'Laravel') }}</a>
        <div class="ms-auto d-flex gap-2">
            @guest
                <a class="btn btn-sm btn-outline-primary" href="{{ route('login') }}">Login</a>
                <a class="btn btn-sm btn-primary" href="{{ route('register') }}">Register</a>
            @endguest

            @auth
                @if(auth()->user()?->role?->slug === 'admin')
                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.dashboard') }}">Admin</a>
                @endif
                <a class="btn btn-sm btn-outline-primary" href="{{ route('cvs.index') }}">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-sm btn-outline-danger" type="submit">Logout</button>
                </form>
            @endauth
        </div>
    </div>
</nav>

@yield('content')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
