<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            :root {
                --app-bg: #f6f7fb;
                --app-text: #22304a;
                --app-muted: #6e7f9c;
                --app-card: rgba(255, 255, 255, 0.92);
                --app-card-border: rgba(156, 176, 206, 0.28);
                --app-accent: #1fb6c9;
                --app-accent-soft: #dff7fb;
                --app-accent-warm: #ff8a65;
            }

            body.app-light-body {
                margin: 0;
                min-height: 100vh;
                background:
                    radial-gradient(900px 460px at 5% -10%, rgba(31, 182, 201, 0.16), transparent 62%),
                    radial-gradient(780px 420px at 95% 0%, rgba(255, 138, 101, 0.12), transparent 60%),
                    var(--app-bg);
                color: var(--app-text);
                font-family: 'Manrope', 'Inter', system-ui, -apple-system, sans-serif;
                overflow-x: hidden;
            }

            body.app-light-body::before {
                content: '';
                position: fixed;
                inset: 0;
                pointer-events: none;
                opacity: 0.2;
                background-image:
                    linear-gradient(rgba(130, 148, 184, 0.12) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(130, 148, 184, 0.12) 1px, transparent 1px);
                background-size: 34px 34px;
                z-index: 0;
            }

            body.app-light-body > * {
                position: relative;
                z-index: 1;
            }

            body.app-light-body .card {
                background: var(--app-card);
                border: 1px solid var(--app-card-border);
                border-radius: 16px;
                box-shadow: 0 20px 30px -26px rgba(26, 40, 66, 0.45);
            }

            body.app-light-body .btn {
                transition: transform 0.22s ease, box-shadow 0.22s ease, filter 0.22s ease;
            }

            body.app-light-body .btn:hover {
                transform: translateY(-1px);
            }

            body.app-light-body .btn-primary {
                background: linear-gradient(95deg, var(--app-accent), #00a2be);
                border-color: #00a2be;
                box-shadow: 0 12px 24px -18px rgba(0, 162, 190, 0.7);
            }

            body.app-light-body .btn-primary:hover {
                filter: brightness(1.05);
            }

            body.app-light-body .form-control,
            body.app-light-body .form-select {
                border-color: rgba(156, 176, 206, 0.4);
                background-color: #fff;
                color: var(--app-text);
            }

            body.app-light-body .form-control:focus,
            body.app-light-body .form-select:focus {
                border-color: rgba(31, 182, 201, 0.7);
                box-shadow: 0 0 0 0.2rem rgba(31, 182, 201, 0.18);
            }

            @keyframes appFadeUp {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            body.app-light-body .page-animate {
                animation: appFadeUp 0.45s ease both;
            }

            @media (prefers-reduced-motion: reduce) {
                body.app-light-body .btn,
                body.app-light-body .page-animate {
                    transition: none !important;
                    animation: none !important;
                }
            }
        </style>
    @endif
</head>
<body class="{{ $isCvRender ? 'cv-render-body' : 'app-light-body' }}">
@yield('content')

@if(! $isCvRender)
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endif
</body>
</html>
