@php
    $templates = $templates ?? collect();
    $templateCategories = $templateCategories ?? collect();
    $stats = $stats ?? [
        'templates' => $templates->count(),
        'published_cvs' => 0,
        'users' => 0,
    ];
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CVBuilder - Efficient Marketing Solutions</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@700;800;900&display=swap" rel="stylesheet">
    
    <!-- Icons (Phosphor Icons) -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <!-- Three.js (Untuk Animasi 3D Background) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>

    <style>
        /* --- RESET & VARIABLES --- */
        :root {
            --bg-color: #030305;
            --primary: #8b5cf6;
            --primary-hover: #7c3aed;
            --secondary: #d946ef;
            --accent: #06b6d4;
            --text-main: #ffffff;
            --text-muted: #9ca3af;
            --glass-bg: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.08);
            --glass-hover: rgba(255, 255, 255, 0.08);
            --font-heading: 'Outfit', sans-serif;
            --font-body: 'Inter', sans-serif;
            --radius-md: 16px;
            --radius-lg: 24px;
            --radius-full: 9999px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            font-family: var(--font-body);
            overflow-x: hidden;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        /* --- TYPOGRAPHY --- */
        h1, h2, h3, h4 {
            font-family: var(--font-heading);
            letter-spacing: -0.02em;
            line-height: 1.2;
        }

        .gradient-text {
            background: linear-gradient(135deg, #c084fc 0%, #e879f9 50%, #818cf8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            background-size: 200% auto;
            animation: gradientMove 6s ease infinite;
        }

        @keyframes gradientMove {
            0%, 100% { background-position: 0% center; }
            50% { background-position: 200% center; }
        }

        /* --- UTILITIES --- */
        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
            position: relative;
            z-index: 10;
        }

        .glass-panel {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), border-color 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }

        .glass-panel:hover {
            border-color: rgba(139, 92, 246, 0.3);
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 14px 32px;
            border-radius: var(--radius-full);
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
        }

        .btn-primary:hover {
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.5);
            transform: translateY(-2px);
        }

        .btn-outline {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.3);
        }

        /* --- NAVBAR --- */
        nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            padding: 20px 0;
            transition: all 0.4s ease;
        }

        nav.scrolled {
            padding: 15px 0;
            background: rgba(3, 3, 5, 0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .nav-content {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr);
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 900;
            color: white;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            letter-spacing: -0.03em;
            justify-self: start;
        }

        .logo span { color: var(--primary); }

        .nav-links {
            display: flex;
            gap: 40px;
            list-style: none;
            justify-self: center;
        }

        .nav-links a {
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
            font-size: 0.95rem;
            position: relative;
        }

        .nav-links a:hover { color: white; }
        
        .nav-links a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -4px;
            left: 0;
            background-color: var(--primary);
            transition: width 0.3s ease;
        }
        
        .nav-links a:hover::after { width: 100%; }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 16px;
            justify-self: end;
            justify-content: flex-end;
        }

        .mobile-toggle { 
            display: none; 
            font-size: 28px; 
            color: white; 
            cursor: pointer;
            background: none;
            border: none;
            padding: 5px;
        }

        /* --- HERO SECTION --- */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
            padding-top: 100px;
        }

        #canvas-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1; /* Ensure background stays behind */
            overflow: hidden;
            pointer-events: none;
            background: radial-gradient(circle at 50% 50%, #0f0c15 0%, #030305 100%);
        }

        .hero-content {
            z-index: 2;
            max-width: 850px;
            opacity: 0;
            transform: translateY(40px);
            animation: fadeUp 1s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
            animation-delay: 0.2s;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(139, 92, 246, 0.1);
            border: 1px solid rgba(139, 92, 246, 0.2);
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 0.85rem;
            color: #c4b5fd;
            margin-bottom: 24px;
            font-weight: 500;
        }

        .hero h1 {
            font-size: clamp(3rem, 7vw, 5.5rem);
            margin-bottom: 24px;
            font-weight: 800;
        }

        .hero p {
            font-size: 1.25rem;
            color: var(--text-muted);
            margin-bottom: 40px;
            max-width: 650px;
            margin-left: auto;
            margin-right: auto;
        }

        .hero-stats {
            display: flex;
            justify-content: center;
            gap: 60px;
            margin-top: 80px;
            opacity: 0;
            animation: fadeUp 1s ease forwards;
            animation-delay: 0.8s;
            padding-top: 40px;
            border-top: 1px solid rgba(255,255,255,0.05);
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .stat-item h4 { 
            font-size: 2.5rem; 
            color: white; 
            font-weight: 700;
            margin-bottom: 4px;
        }
        .stat-item span { 
            font-size: 0.85rem; 
            color: var(--text-muted); 
            text-transform: uppercase; 
            letter-spacing: 1px; 
            font-weight: 600;
        }

        /* --- SECTIONS GENERAL --- */
        section {
            padding: 120px 0;
            position: relative;
            z-index: 10;
        }

        .section-header {
            text-align: center;
            margin-bottom: 80px;
        }

        .section-header h2 { font-size: 3rem; margin-bottom: 16px; }
        .section-tag {
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.8rem;
            font-weight: 700;
            margin-bottom: 16px;
            display: inline-block;
        }

        /* --- SERVICES --- */
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .service-card {
            padding: 48px 40px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            background: linear-gradient(180deg, rgba(255,255,255,0.03) 0%, rgba(255,255,255,0.01) 100%);
        }

        .icon-box {
            width: 64px;
            height: 64px;
            border-radius: 20px;
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.2), rgba(217, 70, 239, 0.1));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 32px;
            margin-bottom: 24px;
            transition: all 0.4s ease;
            border: 1px solid rgba(255,255,255,0.05);
        }

        .service-card:hover .icon-box {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 10px 20px rgba(139, 92, 246, 0.3);
        }

        .service-card h3 { font-size: 1.5rem; margin-bottom: 12px; color: white; }
        .service-card p { color: var(--text-muted); font-size: 1rem; line-height: 1.7; }

        /* --- TEMPLATE GALLERY --- */
        .template-filters {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-bottom: 60px;
            flex-wrap: wrap;
        }
        
        .filter-btn {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.08);
            color: var(--text-muted);
            padding: 10px 24px;
            border-radius: 50px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-btn.active, .filter-btn:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
        }

        .templates-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 40px;
        }

        .template-card {
            display: flex;
            flex-direction: column;
            gap: 20px;
            /* Initial state for animation */
            opacity: 0;
            transform: translateY(30px);
        }
        
        /* Animation helper class added by JS */
        .template-card.visible {
            animation: fadeUp 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        }

        .template-card-image-wrapper {
            position: relative;
            border-radius: var(--radius-lg);
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.1);
            aspect-ratio: 3/4;
            background-color: #0a0a0a;
        }

        .template-card-image-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .template-card:hover .template-card-image-wrapper img {
            transform: scale(1.08);
        }

        .template-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 24px;
            background: linear-gradient(to top, rgba(0,0,0,0.95) 0%, rgba(0,0,0,0.4) 50%, transparent 100%);
            transform: translateY(20px);
            opacity: 0;
            transition: all 0.3s ease;
        }

        .template-card:hover .template-overlay {
            transform: translateY(0);
            opacity: 1;
        }

        .template-tags {
            display: flex;
            gap: 8px;
            margin-top: 8px;
        }
        
        .template-tag {
            font-size: 0.75rem;
            background: rgba(255,255,255,0.1);
            padding: 4px 10px;
            border-radius: 6px;
            color: #e5e7eb;
            font-weight: 500;
        }

        .template-no-image {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            background: linear-gradient(135deg, #111, #1a1a1a);
            font-weight: 600;
            gap: 10px;
        }

        .template-card h3 { 
            font-size: 1.25rem; 
            color: white; 
            margin-bottom: 4px; 
            transition: color 0.3s;
        }
        
        .template-card:hover h3 { color: var(--primary); }

        .template-card-description {
            color: var(--text-muted);
            font-size: 0.9rem;
            line-height: 1.6;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* --- CTA SECTION --- */
        .cta-section { text-align: center; }
        .cta-box {
            padding: 100px 20px;
            background: radial-gradient(circle at center, rgba(139, 92, 246, 0.15), rgba(5,5,5,0) 70%);
            border: 1px solid rgba(139, 92, 246, 0.15);
            position: relative;
            overflow: hidden;
        }

        .cta-box::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.03) 0%, transparent 60%);
            animation: rotateBg 20s linear infinite;
        }

        @keyframes rotateBg { from {transform: rotate(0deg);} to {transform: rotate(360deg);} }

        .cta-content { position: relative; z-index: 2; }

        /* --- FOOTER --- */
        footer {
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            padding: 60px 0 30px;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.9rem;
            position: relative;
            z-index: 10;
            background: #020202;
        }

        .footer-logo {
            font-weight: 800;
            color: white;
            font-size: 1.5rem;
            margin-bottom: 20px;
            display: inline-block;
        }

        .social-links { display: flex; justify-content: center; gap: 20px; margin-bottom: 30px; }
        .social-links a {
            width: 40px; height: 40px;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,0.1);
            display: flex; align-items: center; justify-content: center;
            color: white; text-decoration: none;
            transition: all 0.3s;
        }
        .social-links a:hover {
            background: var(--primary);
            border-color: var(--primary);
            transform: translateY(-3px);
        }

        /* --- ANIMATIONS --- */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .reveal { opacity: 0; transform: translateY(30px); transition: all 0.8s cubic-bezier(0.2, 0.8, 0.2, 1); }
        .reveal.active { opacity: 1; transform: translateY(0); }

        /* --- RESPONSIVE --- */
        @media (max-width: 992px) {
            .hero-stats { gap: 30px; }
        }

        @media (max-width: 768px) {
            .nav-links {
                position: fixed;
                top: 70px;
                left: 0;
                right: 0;
                background: rgba(3, 3, 5, 0.95);
                backdrop-filter: blur(20px);
                flex-direction: column;
                align-items: center;
                padding: 40px 0;
                gap: 25px;
                border-bottom: 1px solid rgba(255,255,255,0.05);
                transform: translateY(-150%);
                transition: transform 0.4s ease;
                z-index: 999;
            }
            
            .nav-links.active { transform: translateY(0); }
            
            .mobile-toggle { display: block; }
            
            .hero { padding-top: 120px; text-align: center; }
            .hero h1 { font-size: 2.8rem; }
            .hero-stats { 
                flex-direction: column; 
                gap: 30px; 
                margin-top: 50px;
                border-top: none;
            }
            
            .section-header h2 { font-size: 2rem; }
            .cta-box { padding: 60px 20px; }
        }
    </style>
</head>
<body>

    <!-- Canvas Background for 3D Animation -->
    <div id="canvas-container"></div>

    <!-- Navigation -->
    <nav id="navbar">
        <div class="container nav-content">
            <a href="#" class="logo">
                <i class="ph ph-file-text" style="font-size: 32px; color: var(--primary);"></i>
                CV<span>BUILDER</span>
            </a>
            
            <ul class="nav-links" id="nav-links">
                <li><a href="#services" onclick="toggleMenu()">Fitur</a></li>
                <li><a href="#templates" onclick="toggleMenu()">Template</a></li>
                <li><a href="#contact" onclick="toggleMenu()">Kontak</a></li>
            </ul>

            <div class="nav-actions">
                <a href="{{ route('login') }}" class="btn btn-primary" style="padding: 10px 24px; font-size: 0.9rem;">Buat CV</a>
                <button class="mobile-toggle" id="mobile-toggle" aria-label="Toggle Menu">
                    <i class="ph ph-list"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero">
        <div class="container hero-content">
            <div class="hero-badge">
                <i class="ph ph-sparkle-fill" style="color: var(--secondary);"></i>
                Digital Resume Platform
            </div>
            <h1>
                Efficient <br>
                <span class="gradient-text">CV Solutions</span>
            </h1>
            <p>
                Bangun CV profesional yang menonjol dalam hitungan menit. Dilengkapi template ATS-friendly dan editor yang mudah digunakan.
            </p>
            <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
                <a href="#templates" class="btn btn-primary">
                    Pilih Template <i class="ph ph-arrow-right"></i>
                </a>
                <a href="#services" class="btn btn-outline">
                    Lihat Fitur <i class="ph ph-eye"></i>
                </a>
            </div>

            <div class="hero-stats">
                <div class="stat-item">
                    <h4>{{ number_format((int) data_get($stats, 'templates', 0)) }}+</h4>
                    <span>Template</span>
                </div>
                <div class="stat-item">
                    <h4>{{ number_format((int) data_get($stats, 'users', 0)) }}+</h4>
                    <span>Pengguna</span>
                </div>
                <div class="stat-item">
                    <h4>{{ number_format((int) data_get($stats, 'published_cvs', 0)) }}+</h4>
                    <span>CV Published</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Services Section -->
    <section id="services">
        <div class="container">
            <div class="section-header reveal">
                <span class="section-tag">Fitur Unggulan</span>
                <h2>Mengapa Kami?</h2>
                <p style="color: var(--text-muted); max-width: 500px; margin: 0 auto;">Platform all-in-one untuk kebutuhan karir digital Anda.</p>
            </div>

            <div class="services-grid">
                <div class="glass-panel service-card reveal">
                    <div class="icon-box"><i class="ph ph-paint-brush-broad"></i></div>
                    <h3>Desain Premium</h3>
                    <p>Template didesain oleh desainer profesional untuk memastikan tampilan yang modern dan menarik.</p>
                </div>
                <div class="glass-panel service-card reveal" style="transition-delay: 0.1s;">
                    <div class="icon-box"><i class="ph ph-robot"></i></div>
                    <h3>ATS Friendly</h3>
                    <p>Struktur format yang ramah sistem pelacakan pelamar (ATS) perusahaan besar.</p>
                </div>
                <div class="glass-panel service-card reveal" style="transition-delay: 0.2s;">
                    <div class="icon-box"><i class="ph ph-share-network"></i></div>
                    <h3>Bagikan Instan</h3>
                    <p>Dapatkan link unik untuk CV Anda dan bagikan langsung ke rekruter tanpa login.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- TEMPLATE GALLERY SECTION -->
    <section id="templates">
        <div class="container">
            <div class="section-header reveal">
                <span class="section-tag">Template Gallery</span>
                <h2>Pilih Desain Karirmu</h2>
                <p style="color: var(--text-muted); max-width: 600px; margin: 0 auto;">
                    Koleksi template profesional yang didesain oleh ahli. Cukup isi data, dan CV siap digunakan dalam hitungan menit.
                </p>
            </div>

            <!-- Filter Categories -->
            <div class="template-filters reveal">
                <button class="filter-btn active" onclick="filterTemplates('all', this)">Semua</button>
                @foreach($templateCategories as $category)
                    <button class="filter-btn" onclick="filterTemplates('{{ $category }}', this)">
                        {{ \Illuminate\Support\Str::title(str_replace('-', ' ', $category)) }}
                    </button>
                @endforeach
            </div>

            <!-- Template Grid -->
            <div class="templates-grid">
                @forelse($templates as $index => $template)
                    @php
                        $thumbnailUrl = $template->thumbnailPreviewUrl();
                        $category = strtolower(trim((string) $template->slug));
                        $name = trim((string) $template->name) !== ''
                            ? $template->name
                            : \Illuminate\Support\Str::title(str_replace('-', ' ', $category));
                        $description = trim((string) $template->description) !== ''
                            ? $template->description
                            : 'Template profesional yang siap pakai untuk berbagai kebutuhan karir.';
                    @endphp

                    <div class="template-card" data-category="{{ $category }}" style="animation-delay: {{ min($index * 0.1, 0.5) }}s;">
                        <div class="template-card-image-wrapper">
                            @if($thumbnailUrl)
                                <img src="{{ $thumbnailUrl }}" alt="{{ $name }}" loading="lazy">
                            @else
                                <div class="template-no-image">
                                    <i class="ph ph-image" style="font-size: 48px;"></i>
                                    No Preview
                                </div>
                            @endif

                            @if($template->is_default)
                                <div style="position: absolute; top: 16px; right: 16px; z-index: 5;">
                                    <span style="background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; font-size: 0.7rem; padding: 6px 12px; border-radius: 6px; font-weight: 700; box-shadow: 0 4px 15px rgba(0,0,0,0.3); letter-spacing: 0.5px;">POPULAR</span>
                                </div>
                            @endif

                            <div class="template-overlay">
                                @auth
                                    <a href="{{ route('cv-builder.templates') }}" class="btn btn-primary" style="width: 100%; justify-content: center; font-size: 0.9rem;">
                                        Gunakan Template <i class="ph ph-arrow-right"></i>
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-primary" style="width: 100%; justify-content: center; font-size: 0.9rem;">
                                        Gunakan Template <i class="ph ph-arrow-right"></i>
                                    </a>
                                @endauth
                            </div>
                        </div>
                        <div>
                            <h3>{{ $name }}</h3>
                            <div class="template-tags">
                                <span class="template-tag">{{ \Illuminate\Support\Str::title(str_replace('-', ' ', $category)) }}</span>
                                @if($template->is_default)
                                    <span class="template-tag" style="background: rgba(139, 92, 246, 0.2); color: #c4b5fd;">Recommended</span>
                                @endif
                            </div>
                            <p class="template-card-description">{{ $description }}</p>
                        </div>
                    </div>
                @empty
                    <div class="glass-panel" style="padding: 40px; color: var(--text-muted); grid-column: 1 / -1; text-align: center;">
                        <i class="ph ph-folder-open" style="font-size: 32px; margin-bottom: 16px; display: block;"></i>
                        Belum ada template aktif yang tersedia.
                    </div>
                @endforelse

            </div>

            <!-- CTA for Templates -->
            <div style="text-align: center; margin-top: 60px;">
                @auth
                    <a href="{{ route('cv-builder.templates') }}" class="btn btn-outline">
                        Lihat Semua {{ number_format((int) data_get($stats, 'templates', 0)) }} Template 
                        <i class="ph ph-arrow-right"></i>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline">
                        Lihat Semua {{ number_format((int) data_get($stats, 'templates', 0)) }} Template 
                        <i class="ph ph-arrow-right"></i>
                    </a>
                @endauth
            </div>
        </div>
    </section>

    <!-- CTA / Contact Section -->
    <section id="contact" class="cta-section">
        <div class="container">
            <div class="glass-panel cta-box reveal">
                <div class="cta-content">
                    <h2 style="font-size: clamp(2rem, 4vw, 3.5rem); margin-bottom: 20px;">
                        Siap untuk <span class="gradient-text">Karir Baru?</span>
                    </h2>
                    <p style="color: var(--text-muted); margin-bottom: 30px; max-width: 600px; margin-left: auto; margin-right: auto;">
                        Ribuan orang telah mendapatkan pekerjaan impian mereka menggunakan CV yang dibuat di platform kami.
                    </p>
                    <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                        <a href="{{ route('login') }}" class="btn btn-primary">
                            Buat CV Sekarang
                        </a>
                        <button class="btn btn-outline" onclick="showToast('Fitur pelajaran segera hadir!')">
                            Pelajari Lebih Lanjut
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-logo">CV<span style="color: var(--primary);">BUILDER</span></div>
            <div class="social-links">
                <a href="#" aria-label="Instagram"><i class="ph ph-instagram-logo"></i></a>
                <a href="#" aria-label="LinkedIn"><i class="ph ph-linkedin-logo"></i></a>
                <a href="#" aria-label="Twitter"><i class="ph ph-twitter-logo"></i></a>
            </div>
            <p>&copy; {{ date('Y') }} CVBuilder Platform. All rights reserved.</p>
        </div>
    </footer>

    <!-- Toast Notification -->
    <div id="toast" style="position: fixed; bottom: 30px; right: 30px; background: rgba(20, 20, 20, 0.95); border: 1px solid rgba(255,255,255,0.1); color: white; padding: 16px 24px; border-radius: 12px; transform: translateY(100px) scale(0.9); opacity: 0; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); z-index: 2000; backdrop-filter: blur(12px); box-shadow: 0 20px 40px rgba(0,0,0,0.4); display: flex; align-items: center; gap: 12px;">
        <i class="ph ph-check-circle" style="color: #34d399; font-size: 24px;"></i>
        <span id="toast-msg" style="font-weight: 500;">Notification</span>
    </div>

    <!-- SCRIPTS -->
    <script>
        // --- 1. Three.js Background Animation (Optimized) ---
        const initThreeJS = () => {
            const container = document.getElementById('canvas-container');
            if (!container) return;

            const scene = new THREE.Scene();
            // Fog untuk menyatukan warna background
            scene.fog = new THREE.FogExp2(0x030305, 0.002);

            const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
            camera.position.z = 30;

            const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
            renderer.setSize(window.innerWidth, window.innerHeight);
            renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2)); // Limit pixel ratio for performance
            container.appendChild(renderer.domElement);

            // Shape 1: Icosahedron
            const geometry1 = new THREE.IcosahedronGeometry(10, 1);
            const material1 = new THREE.MeshBasicMaterial({ 
                color: 0x8b5cf6, 
                wireframe: true,
                transparent: true,
                opacity: 0.1
            });
            const shape1 = new THREE.Mesh(geometry1, material1);
            scene.add(shape1);

            // Shape 2: Torus
            const geometry2 = new THREE.TorusGeometry(16, 0.3, 16, 100);
            const material2 = new THREE.MeshBasicMaterial({ 
                color: 0xd946ef, 
                wireframe: true,
                transparent: true,
                opacity: 0.08
            });
            const shape2 = new THREE.Mesh(geometry2, material2);
            shape2.rotation.x = Math.PI / 2;
            scene.add(shape2);

            // Particles
            const particlesGeometry = new THREE.BufferGeometry();
            const particlesCount = 500;
            const posArray = new Float32Array(particlesCount * 3);

            for(let i = 0; i < particlesCount * 3; i++) {
                posArray[i] = (Math.random() - 0.5) * 90;
            }

            particlesGeometry.setAttribute('position', new THREE.BufferAttribute(posArray, 3));
            const particlesMaterial = new THREE.PointsMaterial({
                size: 0.08,
                color: 0x818cf8,
                transparent: true,
                opacity: 0.5
            });
            const particlesMesh = new THREE.Points(particlesGeometry, particlesMaterial);
            scene.add(particlesMesh);

            // Mouse Interaction
            let mouseX = 0;
            let mouseY = 0;
            let targetX = 0;
            let targetY = 0;

            const windowHalfX = window.innerWidth / 2;
            const windowHalfY = window.innerHeight / 2;

            document.addEventListener('mousemove', (event) => {
                mouseX = (event.clientX - windowHalfX);
                mouseY = (event.clientY - windowHalfY);
            });

            const animate = () => {
                requestAnimationFrame(animate);
                
                // Smooth interpolation
                targetX = mouseX * 0.0005;
                targetY = mouseY * 0.0005;

                shape1.rotation.y += 0.002;
                shape1.rotation.x += 0.001;
                // Add mouse influence gently
                shape1.rotation.y += 0.05 * (targetX - shape1.rotation.y);
                shape1.rotation.x += 0.05 * (targetY - shape1.rotation.x);

                shape2.rotation.z -= 0.001;
                shape2.rotation.y += 0.002;

                particlesMesh.rotation.y = -mouseX * 0.0001;
                particlesMesh.rotation.x = -mouseY * 0.0001;

                renderer.render(scene, camera);
            };
            animate();

            window.addEventListener('resize', () => {
                camera.aspect = window.innerWidth / window.innerHeight;
                camera.updateProjectionMatrix();
                renderer.setSize(window.innerWidth, window.innerHeight);
            });
        };

        // --- 2. Template Filtering Logic ---
        function filterTemplates(category, btnElement) {
            // Update active button
            const buttons = document.querySelectorAll('.filter-btn');
            buttons.forEach(btn => btn.classList.remove('active'));
            btnElement.classList.add('active');

            // Filter cards with animation
            const cards = document.querySelectorAll('.template-card');
            let delayCounter = 0;

            cards.forEach(card => {
                const cardCategory = card.getAttribute('data-category');
                if (category === 'all' || cardCategory === category) {
                    card.style.display = 'flex';
                    card.classList.remove('visible');
                    // Force reflow
                    void card.offsetWidth;
                    // Add visible class to trigger animation
                    setTimeout(() => card.classList.add('visible'), 50);
                } else {
                    card.style.display = 'none';
                    card.classList.remove('visible');
                }
            });
        }

        // --- 3. Mobile Menu Logic ---
        function toggleMenu() {
            const navLinks = document.getElementById('nav-links');
            const icon = document.querySelector('.mobile-toggle i');
            
            navLinks.classList.toggle('active');
            
            // Change icon based on state
            if (navLinks.classList.contains('active')) {
                icon.classList.remove('ph-list');
                icon.classList.add('ph-x');
            } else {
                icon.classList.remove('ph-x');
                icon.classList.add('ph-list');
            }
        }

        document.getElementById('mobile-toggle').addEventListener('click', toggleMenu);

        // --- 4. UI Interactions & Initialization ---
        document.addEventListener('DOMContentLoaded', () => {
            // Init Background
            initThreeJS();

            // Navbar Scroll Effect
            const navbar = document.getElementById('navbar');
            window.addEventListener('scroll', () => {
                if (window.scrollY > 50) navbar.classList.add('scrolled');
                else navbar.classList.remove('scrolled');
            });

            // Reveal Animations on Scroll (IntersectionObserver)
            const observerOptions = { threshold: 0.1, rootMargin: "0px 0px -50px 0px" };
            const revealObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));
            
            // Trigger animation for template cards initially
            const cards = document.querySelectorAll('.template-card');
            cards.forEach((card, index) => {
                card.classList.add('visible');
            });
        });

        // Toast Function
        function showToast(message) {
            const toast = document.getElementById('toast');
            document.getElementById('toast-msg').innerText = message;
            
            toast.style.opacity = '1';
            toast.style.transform = 'translateY(0) scale(1)';
            
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(100px) scale(0.9)';
            }, 3000);
        }
    </script>
</body>
</html>