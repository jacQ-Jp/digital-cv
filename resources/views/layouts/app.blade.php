<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    @php($isCvRender = request()->routeIs('cvs.show') || request()->routeIs('cvs.render') || request()->routeIs('cvs.public') || ($previewMode ?? false))

    @if($isCvRender)
        <!-- CSS KHUSUS UNTUK RENDER CV (PUTIH BERSIH) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            body.cv-render-body {
                margin: 0;
                background: #f8fafc;
                color: #1e293b;
                font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, 'Segoe UI', sans-serif;
            }

            .cv-paper {
                width: min(794px, calc(100% - 2rem));
                min-height: 1123px;
                margin: 1.5rem auto;
                padding: 48px;
                background: #ffffff;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
                overflow-x: hidden;
            }

            .cv-paper, .cv-paper * { box-sizing: border-box; }
            .cv-paper-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 32px; margin-top: 40px; }
            .cv-item-head { flex-wrap: wrap; }
            .cv-item-date { white-space: normal !important; text-align: right; }

            .cv-paper .cv-name, .cv-paper .cv-role, .cv-paper .cv-email, .cv-paper .cv-summary, .cv-paper .cv-item-main, .cv-paper .cv-item-sub, .cv-paper .cv-item-desc, .cv-paper .cv-skill-tag, .cv-paper .cv-info-block, .cv-paper .cv-muted { overflow-wrap: anywhere; word-break: break-word; }

            @media (max-width: 900px) {
                .cv-paper { width: calc(100% - 1rem); padding: 32px 24px; }
                .cv-paper-grid { grid-template-columns: 1fr; gap: 24px; }
            }

            @media print {
                body.cv-render-body { background: #fff !important; margin: 0 !important; }
                .cv-paper { width: 100% !important; min-height: auto !important; margin: 0 !important; padding: 0 !important; box-shadow: none !important; }
            }
        </style>
    @endif

    {{-- CSS/JS UNTUK APLIKASI UTAMA (Landing & Dashboard) --}}
    @if(! $isCvRender && (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot'))))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    {{-- Bootstrap CDN + Custom Ultra Dark Theme --}}
    @if(! $isCvRender)
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <!-- Font: Inter (Body) & Outfit (Headings) untuk look modern tech -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        
        <style>
            :root {
                /* --- ULTRA DARK THEME VARIABLES --- */
                --app-bg-dark: #050505;
                --app-bg-card: rgba(255, 255, 255, 0.03);
                --app-card-border: rgba(255, 255, 255, 0.08);

                --app-text-main: #ffffff;
                --app-text-muted: #9ca3af;

                --app-primary: #8b5cf6;
                --app-primary-dark: #7c3aed;
                --app-secondary: #d946ef;
                --app-accent: #06b6d4;
            }

            html { scroll-behavior: smooth; }

            body.app-light-body {
                margin: 0;
                min-height: 100vh;
                background-color: var(--app-bg-dark);
                color: var(--app-text-main);
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                overflow-x: hidden;
                background-image:
                    radial-gradient(1200px circle at 15% -10%, rgba(139, 92, 246, 0.16), transparent 55%),
                    radial-gradient(900px circle at 90% -20%, rgba(217, 70, 239, 0.13), transparent 50%);
            }

            /* Subtle Noise Overlay */
            body.app-light-body::before {
                content: "";
                position: fixed;
                top: 0; left: 0; width: 100%; height: 100%;
                background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)' opacity='0.03'/%3E%3C/svg%3E");
                pointer-events: none;
                z-index: -1;
            }

            body.app-light-body::after {
                content: "";
                position: fixed;
                inset: 0;
                background-image: radial-gradient(rgba(255, 255, 255, 0.08) 0.6px, transparent 0.6px);
                background-size: 3px 3px;
                opacity: 0.08;
                pointer-events: none;
                z-index: -1;
            }

            body.app-light-body > * { position: relative; z-index: 1; }

            /* --- CUSTOM SCROLLBAR --- */
            ::-webkit-scrollbar { width: 10px; }
            ::-webkit-scrollbar-track { background: #050505; }
            ::-webkit-scrollbar-thumb { background: #2f2940; border-radius: 5px; border: 2px solid #050505; }
            ::-webkit-scrollbar-thumb:hover { background: var(--app-primary); }

            /* --- NAVBAR (Floating Glass) --- */
            body.app-light-body .navbar {
                background: rgba(5, 5, 5, 0.78);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                border-bottom: 1px solid rgba(255,255,255,0.05);
            }
            body.app-light-body .navbar-brand { 
                color: #fff !important; 
                font-family: 'Outfit', sans-serif; 
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 1px;
            }
            body.app-light-body .nav-link { 
                color: var(--app-text-muted) !important; 
                font-weight: 500;
                position: relative;
                transition: color 0.3s;
            }
            body.app-light-body .nav-link:hover, body.app-light-body .nav-link.active { 
                color: #fff !important; 
            }
            /* Underline animation for links */
            body.app-light-body .nav-link::after {
                content: ''; position: absolute; bottom: -2px; left: 50%; width: 0; height: 2px;
                background: var(--app-primary); transition: all 0.3s ease; transform: translateX(-50%);
            }
            body.app-light-body .nav-link:hover::after { width: 80%; }

            /* --- CARD COMPONENT (Glassmorphism 2.0) --- */
            body.app-light-body .card {
                background: var(--app-bg-card);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid var(--app-card-border);
                border-radius: 20px;
                box-shadow: 0 12px 40px -15px rgba(0, 0, 0, 0.55);
                color: var(--app-text-main);
                transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            }

            body.app-light-body .card:hover {
                transform: translateY(-5px);
                border-color: rgba(139, 92, 246, 0.4);
                box-shadow: 0 20px 50px -10px rgba(139, 92, 246, 0.28);
            }

            body.app-light-body .card-header {
                border-bottom: 1px solid rgba(255, 255, 255, 0.05);
                background: rgba(255, 255, 255, 0.02);
                border-radius: 20px 20px 0 0 !important;
            }

            /* --- BUTTONS (Neon Glow) --- */
            body.app-light-body .btn {
                transition: all 0.3s ease;
                font-weight: 600;
                border-radius: 10px;
                letter-spacing: 0.02em;
            }

            /* Primary Button */
            body.app-light-body .btn-primary {
                background: linear-gradient(90deg, var(--app-primary), var(--app-secondary));
                border: none;
                color: #fff;
                box-shadow: 0 0 20px rgba(168, 85, 247, 0.35);
                position: relative;
                overflow: hidden;
            }
            body.app-light-body .btn-primary:hover {
                box-shadow: 0 0 30px rgba(168, 85, 247, 0.55);
                transform: translateY(-2px);
            }
            
            /* Outline Button */
            body.app-light-body .btn-outline-light, 
            body.app-light-body .btn-outline-secondary {
                border-color: rgba(255, 255, 255, 0.15);
                color: var(--app-text-main);
                background: transparent;
            }
            body.app-light-body .btn-outline-light:hover {
                background: rgba(255, 255, 255, 0.05);
                color: #fff;
                border-color: #fff;
                box-shadow: 0 0 15px rgba(255,255,255,0.1);
            }

            /* --- FORMS (Futuristic Inputs) --- */
            body.app-light-body .form-control,
            body.app-light-body .form-select {
                background-color: rgba(255, 255, 255, 0.03);
                border: 1px solid var(--app-card-border);
                color: #fff;
                padding: 0.75rem 1rem;
                border-radius: 12px;
                transition: all 0.3s;
            }

            body.app-light-body .form-control:focus,
            body.app-light-body .form-select:focus {
                background-color: rgba(255, 255, 255, 0.05);
                border-color: var(--app-primary);
                color: #fff;
                box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.16);
            }

            body.app-light-body .form-control::placeholder {
                color: rgba(156, 163, 175, 0.8);
            }

            /* --- TYPOGRAPHY (Premium Look) --- */
            body.app-light-body h1, 
            body.app-light-body h2, 
            body.app-light-body h3,
            body.app-light-body .display-1,
            body.app-light-body .display-2 {
                color: #fff;
                font-family: 'Outfit', sans-serif;
                font-weight: 800;
                letter-spacing: -0.03em;
            }

            /* Gradient Text Utility */
            .text-gradient,
            .gradient-text {
                background: linear-gradient(to right, #fff, #c4b5fd);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }
            
            body.app-light-body .text-muted { color: var(--app-text-muted) !important; }
            body.app-light-body .text-primary { color: var(--app-primary) !important; }
            
            body.app-light-body a:not(.btn):not([class^="btn-"]):not([class*=" btn-"]) {
                color: var(--app-primary);
                text-decoration: none;
                transition: color 0.2s;
                font-weight: 500;
            }

            body.app-light-body a:not(.btn):not([class^="btn-"]):not([class*=" btn-"]):hover {
                color: #c4b5fd;
            }

            /* --- TABLES (Modern) --- */
            body.app-light-body .table {
                color: var(--app-text-main);
                border-color: rgba(255,255,255,0.05);
            }
            body.app-light-body .table > :not(caption) > * > * {
                background-color: transparent;
                border-bottom-color: rgba(255,255,255,0.05);
            }
            body.app-light-body .table-hover tbody tr:hover td {
                background-color: rgba(139, 92, 246, 0.05);
            }

            /* --- ANIMATIONS --- */
            @keyframes appFadeUp {
                from { opacity: 0; transform: translateY(30px); }
                to { opacity: 1; transform: translateY(0); }
            }
            body.app-light-body .animate-up {
                animation: appFadeUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
                opacity: 0;
            }
            
            .delay-100 { animation-delay: 0.1s; }
            .delay-200 { animation-delay: 0.2s; }
            .delay-300 { animation-delay: 0.3s; }
            .delay-400 { animation-delay: 0.4s; }

            /* Floating Animation for Icons */
            @keyframes float {
                0% { transform: translateY(0px); }
                50% { transform: translateY(-10px); }
                100% { transform: translateY(0px); }
            }
            .animate-float { animation: float 3s ease-in-out infinite; }
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