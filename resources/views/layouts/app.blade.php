<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>

    @php($isCvRender = request()->routeIs('cvs.show') || request()->routeIs('cvs.render') || request()->routeIs('cvs.public') || ($previewMode ?? false))

    @if($isCvRender)
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            body.cv-render-body {
                margin: 0;
                background: #f3f4f6;
                color: #111827;
                font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, 'Segoe UI', sans-serif;
            }

            .cv-paper {
                width: min(794px, calc(100% - 2rem));
                min-height: 1123px;
                margin: 1.5rem auto;
                padding: 48px;
                background: #ffffff;
                box-shadow: 0 16px 42px rgba(15, 23, 42, 0.12);
                overflow-x: hidden;
            }

            .cv-paper,
            .cv-paper * {
                box-sizing: border-box;
            }

            .cv-paper-grid {
                display: grid;
                grid-template-columns: 2fr 1fr;
                gap: 28px;
                margin-top: 30px;
            }

            .cv-paper-grid > * {
                min-width: 0;
            }

            .cv-item-head {
                flex-wrap: wrap;
            }

            .cv-item-date {
                white-space: normal !important;
                text-align: right;
            }

            .cv-paper .cv-name,
            .cv-paper .cv-role,
            .cv-paper .cv-email,
            .cv-paper .cv-summary,
            .cv-paper .cv-item-main,
            .cv-paper .cv-item-sub,
            .cv-paper .cv-item-desc,
            .cv-paper .cv-skill-tag,
            .cv-paper .cv-info-block,
            .cv-paper .cv-muted {
                overflow-wrap: anywhere;
                word-break: break-word;
            }

            @media (max-width: 900px) {
                .cv-paper {
                    width: calc(100% - 1rem);
                    padding: 28px 22px;
                }

                .cv-paper-grid {
                    grid-template-columns: 1fr;
                    gap: 22px;
                }
            }

            @media print {
                body.cv-render-body {
                    background: #fff !important;
                    margin: 0 !important;
                }

                .cv-paper {
                    width: 100% !important;
                    min-height: auto !important;
                    margin: 0 !important;
                    padding: 24px !important;
                    box-shadow: none !important;
                }
            }
        </style>
    @endif

    {{-- App CSS/JS for dashboard/admin UI only --}}
    @if(! $isCvRender && (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot'))))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    {{-- Minimal Bootstrap via CDN for basic CRUD UI --}}
    @if(! $isCvRender)
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif
</head>
<body class="{{ $isCvRender ? 'cv-render-body' : '' }}">
@if(! $isCvRender)
<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">{{ config('app.name', 'Laravel') }}</a>

        @auth
            <form method="POST" action="{{ route('cv-builder.templates.save') }}" class="ms-3">
                @csrf
                <input type="hidden" name="redirect_to" value="{{ request()->getRequestUri() }}" />
                <select name="template_slug" class="form-select form-select-sm" onchange="this.form.submit()" aria-label="Template">
                    @php($activeTemplates = \App\Models\Template::query()->where('is_active', true)->orderByDesc('is_default')->orderBy('name')->get())
                    @php($selected = session('cv_builder.template_slug') ?? $activeTemplates->firstWhere('is_default', true)?->slug ?? $activeTemplates->first()?->slug)
                    @foreach($activeTemplates as $tpl)
                        <option value="{{ $tpl->slug }}" @selected($selected === $tpl->slug)>
                            {{ $tpl->name }}@if($tpl->is_default) (default)@endif
                        </option>
                    @endforeach
                </select>
            </form>
        @endauth

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
@endif

@yield('content')

@if(! $isCvRender)
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endif
</body>
</html>
