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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;800&family=Outfit:wght@700;900&display=swap" rel="stylesheet">
    
    <!-- Icons (Phosphor Icons) -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <!-- Three.js (Untuk Animasi 3D Background) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>

    <style>
        /* --- RESET & VARIABLES --- */
        :root {
            --bg-color: #050505;
            --primary: #8b5cf6;
            --primary-hover: #7c3aed;
            --secondary: #d946ef;
            --accent: #06b6d4;
            --text-main: #ffffff;
            --text-muted: #9ca3af;
            --glass-bg: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.08);
            --font-heading: 'Outfit', sans-serif;
            --font-body: 'Inter', sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            font-family: var(--font-body);
            overflow-x: hidden;
            line-height: 1.6;
        }

        /* --- TYPOGRAPHY --- */
        h1, h2, h3, h4 {
            font-family: var(--font-heading);
            letter-spacing: -0.03em;
        }

        .gradient-text {
            background: linear-gradient(135deg, #a855f7 0%, #ec4899 50%, #6366f1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            background-size: 200% auto;
            animation: gradientMove 5s linear infinite;
        }

        @keyframes gradientMove {
            0% { background-position: 0% center; }
            100% { background-position: 200% center; }
        }

        /* --- UTILITIES --- */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
            position: relative;
            z-index: 10;
        }

        .glass-panel {
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            transition: transform 0.3s ease, border-color 0.3s ease;
        }

        .glass-panel:hover {
            border-color: rgba(139, 92, 246, 0.4);
            transform: translateY(-5px);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 32px;
            border-radius: 50px;
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
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            color: white;
            box-shadow: 0 0 20px rgba(168, 85, 247, 0.4);
        }

        .btn-primary:hover {
            box-shadow: 0 0 30px rgba(168, 85, 247, 0.6);
            transform: scale(1.02);
        }

        .btn-outline {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: white;
        }

        /* --- NAVBAR --- */
        nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            padding: 20px 0;
            transition: all 0.4s ease;
        }

        nav.scrolled {
            padding: 12px 0;
            background: rgba(5, 5, 5, 0.8);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .nav-content {
            display: flex;
            justify-content: space-between;
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
        }

        .logo span {
            color: var(--primary);
        }

        .nav-links {
            display: flex;
            gap: 32px;
        }

        .nav-links a {
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
            font-size: 0.95rem;
        }

        .nav-links a:hover {
            color: white;
        }

        /* --- HERO SECTION --- */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
            padding-top: 80px;
        }

        #canvas-container {
            position: fixed; /* Changed to fixed for consistent background */
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .hero-content {
            z-index: 2;
            max-width: 800px;
            opacity: 0;
            transform: translateY(30px);
            animation: fadeUp 1s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
            animation-delay: 0.5s;
        }

        .hero h1 {
            font-size: clamp(3rem, 6vw, 5rem);
            line-height: 1.1;
            margin-bottom: 24px;
        }

        .hero p {
            font-size: 1.25rem;
            color: var(--text-muted);
            margin-bottom: 40px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .hero-stats {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-top: 60px;
            opacity: 0;
            animation: fadeUp 1s ease forwards;
            animation-delay: 1s;
        }

        .stat-item h4 { font-size: 2rem; color: white; }
        .stat-item span { font-size: 0.875rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; }

        /* --- SECTIONS GENERAL --- */
        section {
            padding: 100px 0;
            position: relative;
            z-index: 10;
            background: linear-gradient(180deg, transparent 0%, rgba(5,5,5,0.8) 10%, rgba(5,5,5,1) 100%);
        }

        .section-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-header h2 { font-size: 2.5rem; margin-bottom: 16px; }
        .section-tag {
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.8rem;
            font-weight: 700;
            margin-bottom: 12px;
            display: block;
        }

        /* --- SERVICES --- */
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
        }

        .service-card {
            padding: 40px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .icon-box {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            background: rgba(139, 92, 246, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 32px;
            margin-bottom: 24px;
            transition: all 0.3s;
        }

        .service-card:hover .icon-box {
            background: var(--primary);
            color: white;
            box-shadow: 0 0 20px rgba(139, 92, 246, 0.5);
        }

        .service-card h3 { font-size: 1.5rem; margin-bottom: 12px; color: white; }
        .service-card p { color: var(--text-muted); font-size: 0.95rem; }

        /* --- TEMPLATE GALLERY (NEW SECTION) --- */
        .template-filters {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }
        
        .filter-btn {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            color: var(--text-muted);
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .filter-btn.active, .filter-btn:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .templates-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
        }

        .template-card-image-wrapper {
            position: relative;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.1);
            aspect-ratio: 3/4;
        }

        .template-card-image-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .template-card:hover .template-card-image-wrapper img {
            transform: scale(1.05);
        }

        .template-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 20px;
            background: linear-gradient(to top, rgba(0,0,0,0.9), transparent);
            transform: translateY(100%);
            transition: transform 0.3s ease;
        }

        .template-card:hover .template-overlay {
            transform: translateY(0);
        }

        .template-tags {
            display: flex;
            gap: 8px;
            margin-top: 5px;
        }
        
        .template-tag {
            font-size: 0.75rem;
            background: rgba(255,255,255,0.1);
            padding: 2px 8px;
            border-radius: 4px;
            color: var(--text-muted);
        }

        .template-no-image {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            background: linear-gradient(135deg, rgba(255,255,255,0.04), rgba(139, 92, 246, 0.18));
            font-weight: 600;
            letter-spacing: 0.02em;
        }

        .template-card-description {
            color: var(--text-muted);
            margin-top: 10px;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        /* --- CASES / PORTFOLIO --- */
        .cases-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 24px;
        }

        .case-card {
            overflow: hidden;
            border-radius: 24px;
            position: relative;
            cursor: pointer;
            height: 400px;
        }

        .case-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        .case-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 30px;
            background: linear-gradient(to top, rgba(0,0,0,0.9), transparent);
            transform: translateY(20px);
            opacity: 0;
            transition: all 0.4s ease;
        }

        .case-card:hover .case-image { transform: scale(1.1); }
        .case-card:hover .case-overlay { transform: translateY(0); opacity: 1; }

        .case-cat { color: var(--secondary); font-size: 0.8rem; font-weight: 700; text-transform: uppercase; }
        .case-title { color: white; font-size: 1.5rem; margin-top: 8px; }

        /* --- CTA SECTION --- */
        .cta-section { text-align: center; }
        .cta-box {
            padding: 80px 20px;
            background: radial-gradient(circle at center, rgba(139, 92, 246, 0.15), transparent 70%);
            border: 1px solid rgba(139, 92, 246, 0.2);
        }

        /* --- FOOTER --- */
        footer {
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            padding: 40px 0;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.9rem;
            position: relative;
            z-index: 10;
            background: #020202;
        }

        /* --- ANIMATIONS --- */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .reveal { opacity: 0; transform: translateY(30px); transition: all 0.8s ease; }
        .reveal.active { opacity: 1; transform: translateY(0); }

        /* --- MOBILE NAV --- */
        .mobile-toggle { display: none; font-size: 24px; color: white; cursor: pointer; }

        @media (max-width: 768px) {
            .nav-links { display: none; }
            .mobile-toggle { display: block; }
            .hero h1 { font-size: 2.5rem; }
            .hero-stats { flex-direction: column; gap: 20px; }
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
            <div class="nav-links">
                <a href="#services">Fitur</a>
                <a href="#templates">Template</a>
                <a href="#cases">Portfolio</a>
                <a href="#contact">Kontak</a>
            </div>
            <div style="display: flex; gap: 10px; align-items: center;">
                <a href="{{ route('login') }}" class="btn btn-primary" style="padding: 10px 24px; font-size: 0.9rem;">Buat CV</a>
                <div class="mobile-toggle">
                    <i class="ph ph-list"></i>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero">
        <div class="container hero-content">
            <div style="margin-bottom: 20px; display: inline-block;">
                <span style="background: rgba(255,255,255,0.05); padding: 8px 16px; border-radius: 20px; font-size: 0.8rem; border: 1px solid rgba(255,255,255,0.1); text-transform: uppercase; letter-spacing: 1px;">
                    Digital Resume Platform
                </span>
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
                <a href="#cases" class="btn btn-outline">
                    Lihat Contoh <i class="ph ph-eye"></i>
                </a>
            </div>

            <div class="hero-stats">
                <div class="stat-item">
                    <h4>{{ number_format((int) data_get($stats, 'templates', 0)) }}+</h4>
                    <span>Template Pro</span>
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

    <!-- TEMPLATE GALLERY SECTION (NEW) -->
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

                    <div class="template-card reveal" data-category="{{ $category }}" style="transition-delay: {{ min($index * 0.08, 0.4) }}s;">
                        <div class="template-card-image-wrapper">
                            @if($thumbnailUrl)
                                <img src="{{ $thumbnailUrl }}" alt="{{ $name }}">
                            @else
                                <div class="template-no-image">No Preview</div>
                            @endif

                            @if($template->is_default)
                                <div style="position: absolute; top: 10px; right: 10px; z-index: 5;">
                                    <span style="background: var(--primary); color: white; font-size: 0.7rem; padding: 4px 8px; border-radius: 4px; font-weight: 700; box-shadow: 0 4px 10px rgba(0,0,0,0.3);">DEFAULT</span>
                                </div>
                            @endif

                            <div class="template-overlay">
                                @auth
                                    <a href="{{ route('cv-builder.templates') }}" class="btn btn-primary" style="width: 100%; justify-content: center; font-size: 0.9rem;">Gunakan Template</a>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-primary" style="width: 100%; justify-content: center; font-size: 0.9rem;">Gunakan Template</a>
                                @endauth
                            </div>
                        </div>
                        <div style="margin-top: 16px;">
                            <h3 style="font-size: 1.1rem; color: white; margin-bottom: 5px;">{{ $name }}</h3>
                            <div class="template-tags">
                                <span class="template-tag">{{ \Illuminate\Support\Str::title(str_replace('-', ' ', $category)) }}</span>
                                @if($template->is_default)
                                    <span class="template-tag">Recommended</span>
                                @endif
                            </div>
                            <p class="template-card-description">{{ $description }}</p>
                        </div>
                    </div>
                @empty
                    <div class="glass-panel" style="padding: 28px; color: var(--text-muted);">
                        Belum ada template aktif yang tersedia.
                    </div>
                @endforelse

            </div>

            <!-- CTA for Templates -->
            <div style="text-align: center; margin-top: 50px;">
                @auth
                    <a href="{{ route('cv-builder.templates') }}" class="btn btn-outline">Lihat Semua {{ number_format((int) data_get($stats, 'templates', 0)) }} Template <i class="ph ph-arrow-right"></i></a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline">Lihat Semua {{ number_format((int) data_get($stats, 'templates', 0)) }} Template <i class="ph ph-arrow-right"></i></a>
                @endauth
            </div>
        </div>
    </section>

    <!-- Cases / Portfolio Section -->
    <section id="cases">
        <div class="container">
            <div class="section-header reveal">
                <span class="section-tag">Kisah Sukses</span>
                <h2>Dibuat oleh Pengguna Kami</h2>
            </div>

            <div class="cases-grid">
                <div class="case-card reveal">
                    <img src="https://picsum.photos/seed/tech1/600/800.jpg" alt="Project 1" class="case-image">
                    <div class="case-overlay">
                        <div class="case-cat">Fintech App</div>
                        <div class="case-title">Neo Banking UI</div>
                    </div>
                </div>
                <div class="case-card reveal" style="transition-delay: 0.1s;">
                    <img src="https://picsum.photos/seed/arch/600/800.jpg" alt="Project 2" class="case-image">
                    <div class="case-overlay">
                        <div class="case-cat">Architecture</div>
                        <div class="case-title">Modern Living 3D</div>
                    </div>
                </div>
                <div class="case-card reveal" style="transition-delay: 0.2s;">
                    <img src="https://picsum.photos/seed/fashion/600/800.jpg" alt="Project 3" class="case-image">
                    <div class="case-overlay">
                        <div class="case-cat">E-Commerce</div>
                        <div class="case-title">Streetwear Brand</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA / Contact Section -->
    <section id="contact" class="cta-section">
        <div class="container">
            <div class="glass-panel cta-box reveal">
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
                    <button class="btn btn-outline">
                        Pelajari Lebih Lanjut
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div style="margin-bottom: 20px;">
                <span style="font-weight: 800; color: white; font-size: 1.2rem;">CV<span style="color: var(--primary);">BUILDER</span></span>
            </div>
            <div style="display: flex; justify-content: center; gap: 20px; margin-bottom: 20px;">
                <a href="#" style="color: white;"><i class="ph ph-instagram-logo" style="font-size: 24px;"></i></a>
                <a href="#" style="color: white;"><i class="ph ph-linkedin-logo" style="font-size: 24px;"></i></a>
                <a href="#" style="color: white;"><i class="ph ph-twitter-logo" style="font-size: 24px;"></i></a>
            </div>
            <p>&copy; 2023 CVBuilder Platform. All rights reserved.</p>
        </div>
    </footer>

    <!-- Toast Notification -->
    <div id="toast" style="position: fixed; bottom: 30px; right: 30px; background: rgba(20, 20, 20, 0.9); border: 1px solid var(--primary); color: white; padding: 15px 25px; border-radius: 12px; transform: translateY(100px); opacity: 0; transition: all 0.3s; z-index: 1000; backdrop-filter: blur(10px); box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
        <div style="display: flex; align-items: center; gap: 10px;">
            <i class="ph ph-check-circle" style="color: #34d399; font-size: 20px;"></i>
            <span id="toast-msg">Notification</span>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script>
        // --- 1. Three.js Background Animation ---
        const initThreeJS = () => {
            const container = document.getElementById('canvas-container');
            const scene = new THREE.Scene();
            scene.fog = new THREE.FogExp2(0x050505, 0.002);

            const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
            camera.position.z = 30;

            const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
            renderer.setSize(window.innerWidth, window.innerHeight);
            renderer.setPixelRatio(window.devicePixelRatio);
            container.appendChild(renderer.domElement);

            // Shape 1: Icosahedron (Main CV Shape metaphor)
            const geometry1 = new THREE.IcosahedronGeometry(10, 1);
            const material1 = new THREE.MeshBasicMaterial({ 
                color: 0x8b5cf6, 
                wireframe: true,
                transparent: true,
                opacity: 0.15
            });
            const shape1 = new THREE.Mesh(geometry1, material1);
            scene.add(shape1);

            // Shape 2: Torus (Ring)
            const geometry2 = new THREE.TorusGeometry(15, 0.5, 16, 100);
            const material2 = new THREE.MeshBasicMaterial({ 
                color: 0xd946ef, 
                wireframe: true,
                transparent: true,
                opacity: 0.1
            });
            const shape2 = new THREE.Mesh(geometry2, material2);
            shape2.rotation.x = Math.PI / 2;
            scene.add(shape2);

            // Particles
            const particlesGeometry = new THREE.BufferGeometry();
            const particlesCount = 700;
            const posArray = new Float32Array(particlesCount * 3);

            for(let i = 0; i < particlesCount * 3; i++) {
                posArray[i] = (Math.random() - 0.5) * 80;
            }

            particlesGeometry.setAttribute('position', new THREE.BufferAttribute(posArray, 3));
            const particlesMaterial = new THREE.PointsMaterial({
                size: 0.05,
                color: 0xffffff,
                transparent: true,
                opacity: 0.4
            });
            const particlesMesh = new THREE.Points(particlesGeometry, particlesMaterial);
            scene.add(particlesMesh);

            // Mouse Interaction
            let mouseX = 0;
            let mouseY = 0;
            const windowHalfX = window.innerWidth / 2;
            const windowHalfY = window.innerHeight / 2;

            document.addEventListener('mousemove', (event) => {
                mouseX = (event.clientX - windowHalfX);
                mouseY = (event.clientY - windowHalfY);
            });

            const animate = () => {
                requestAnimationFrame(animate);
                const targetX = mouseX * 0.001;
                const targetY = mouseY * 0.001;

                shape1.rotation.y += 0.002;
                shape1.rotation.x += 0.001;
                shape1.rotation.y += 0.05 * (targetX - shape1.rotation.y);
                shape1.rotation.x += 0.05 * (targetY - shape1.rotation.x);

                shape2.rotation.z -= 0.001;
                shape2.rotation.y += 0.002;

                particlesMesh.rotation.y = -mouseX * 0.0002;
                particlesMesh.rotation.x = -mouseY * 0.0002;

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
            // Update active button style
            const buttons = document.querySelectorAll('.filter-btn');
            buttons.forEach(btn => btn.classList.remove('active'));
            btnElement.classList.add('active');

            // Filter cards
            const cards = document.querySelectorAll('.template-card');
            cards.forEach(card => {
                const cardCategory = card.getAttribute('data-category');
                if (category === 'all' || cardCategory === category) {
                    card.style.display = 'block';
                    // Small animation reset
                    card.style.opacity = '0';
                    setTimeout(() => card.style.opacity = '1', 50);
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // --- 3. UI Interactions ---
        document.addEventListener('DOMContentLoaded', () => {
            initThreeJS();

            const navbar = document.getElementById('navbar');
            window.addEventListener('scroll', () => {
                if (window.scrollY > 50) navbar.classList.add('scrolled');
                else navbar.classList.remove('scrolled');
            });

            const reveals = document.querySelectorAll('.reveal');
            const revealObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) entry.target.classList.add('active');
                });
            }, { threshold: 0.15 });
            reveals.forEach(el => revealObserver.observe(el));
        });

        function showToast(message) {
            const toast = document.getElementById('toast');
            document.getElementById('toast-msg').innerText = message;
            toast.style.opacity = '1';
            toast.style.transform = 'translateY(0)';
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(100px)';
            }, 3000);
        }
    </script>
</body>
</html>