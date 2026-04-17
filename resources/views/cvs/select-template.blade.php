@extends('layouts.app')

@section('content')
<style>
    :root {
        /* Palette: Clean, Professional, Paper-like */
        --bg-page: #f3f4f6; /* Light gray page bg */
        --tpl-card: #ffffff;
        --tpl-border: rgba(0, 0, 0, 0.08);
        --tpl-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.1);
        --tpl-shadow-hover: 0 20px 40px -5px rgba(0, 0, 0, 0.15);
        --tpl-text: #1f2937;
        --tpl-muted: #6b7280;
        --tpl-accent: #2563eb; /* Professional Blue */
        --tpl-accent-hover: #1d4ed8;
        
        --radius-md: 12px;
        --radius-lg: 16px;
        --radius-pill: 999px;
        --transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    body {
        /* Memastikan background luar bersih */
        background-color: #f8fafc;
        font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        -webkit-font-smoothing: antialiased;
    }

    /* Sembunyikan Navbar default jika ada */
    nav.navbar { display: none !important; }

    .cv-container {
        max-width: 1280px;
        margin: 0 auto;
        padding: 2rem 1.5rem 8rem;
    }

    /* --- Header --- */
    .cv-header {
        text-align: center;
        margin-bottom: 3rem;
        animation: fadeIn 0.6s ease-out;
    }

    .cv-header h1 {
        font-size: 2.2rem;
        font-weight: 800;
        color: #111827;
        margin-bottom: 0.5rem;
        letter-spacing: -0.025em;
    }

    .cv-header p {
        color: var(--tpl-muted);
        font-size: 1.05rem;
    }

    /* --- Toolbar --- */
    .cv-controls {
        display: flex;
        justify-content: center;
        margin-bottom: 3rem;
        position: sticky;
        top: 1rem;
        z-index: 50;
    }

    .toolbar {
        display: flex;
        align-items: center;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: var(--radius-pill);
        padding: 6px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        width: 100%;
        max-width: 700px;
        gap: 6px;
        transition: var(--transition);
    }

    .toolbar:focus-within {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        border-color: var(--tpl-accent);
    }

    .search-group {
        flex: 1;
        display: flex;
        align-items: center;
        position: relative;
    }

    .search-icon {
        position: absolute;
        left: 16px;
        color: #9ca3af;
        width: 18px;
        height: 18px;
    }

    .cv-control-input {
        width: 100%;
        border: none;
        background: transparent;
        padding: 10px 10px 10px 44px;
        font-size: 0.95rem;
        color: #374151;
    }

    .cv-control-input:focus { outline: none; }

    .voice-btn {
        background: transparent;
        border: none;
        color: #9ca3af;
        cursor: pointer;
        padding: 6px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: var(--transition);
    }
    .voice-btn:hover { background: #f3f4f6; color: var(--tpl-accent); }
    .voice-btn.listening { color: #ef4444; animation: pulse 1.5s infinite; }

    .divider { width: 1px; height: 20px; background: #e5e7eb; }

    .select-wrapper {
        position: relative;
        padding-right: 0;
        min-width: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .cv-control-select {
        border: none;
        background: transparent;
        color: #4b5563;
        font-size: 0.9rem;
        font-weight: 500;
        cursor: pointer;
        appearance: none;
        width: auto;
        min-width: 150px;
        max-width: 180px;
        text-align: center;
        text-align-last: center;
        direction: ltr;
        padding: 0 24px 0 10px;
    }

    .cv-control-select option {
        text-align: center;
    }
    .select-wrapper::after {
        content: '▼'; font-size: 0.6rem; color: #6b7280;
        position: absolute; right: 8px; top: 50%; transform: translateY(-50%);
    }

        /* --- GRID & CARD (Paper Style) --- */
    .cv-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 2rem;
    }

    .cv-card {
        background: transparent; /* Card wrapper is transparent */
        border: none;
        cursor: pointer;
        position: relative;
        display: flex;
        flex-direction: column;
        transition: var(--transition);
        group: card; /* Grouping for hover effects */
    }

        /* The Document Preview Area */
    .cv-card-preview-wrapper {
        background: #e2e8f0; /* Warna abu-abu luar (frame space) */
        border-radius: var(--radius-md);
        overflow: hidden;
        position: relative;
        box-shadow: var(--tpl-shadow);
        transition: var(--transition);
        border: 1px solid var(--tpl-border);
        aspect-ratio: 210 / 297; /* A4 Ratio */
        
        /* Flex agar kertas berada di tengah */
        display: flex;
        align-items: center;
        justify-content: center;
        
        /* Jarak (Space) di sekeliling kertas agar terlihat ada frame */
        padding: 4%; 
    }

    /* Hover effect on "Paper" */
    .cv-card:hover .cv-card-preview-wrapper {
        transform: translateY(-6px);
        box-shadow: var(--tpl-shadow-hover);
        border-color: var(--tpl-accent);
    }

    /* Selected State - Blue Border Ring */
    .cv-card.is-selected .cv-card-preview-wrapper {
        border: 2px solid var(--tpl-accent);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    }
    
    .cv-card.is-selected .cv-btn-overlay {
        background: var(--tpl-accent);
        color: #fff;
    }

    /* The Actual Image inside the paper */
    .cv-card-preview {
        width: 100%;
        height: 100%;
        
        /* DIUBAH: dari 'contain' menjadi 'cover' */
        /* Agar gambar memenuhi area penuh tanpa ruang kosong */
        object-fit: cover; 
        
        display: block;
        
        /* Bayangan & Background Putih untuk efek kertas */
        background: #ffffff;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border-radius: 2px;
    }

    /* Overlay Button "Use this template" */
    .cv-btn-overlay {
        position: absolute;
        bottom: 12px;
        right: 12px;
        background: #fff;
        color: #374151;
        font-size: 0.8rem;
        font-weight: 600;
        padding: 8px 16px;
        border-radius: var(--radius-pill);
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transition: var(--transition);
        z-index: 2;
        pointer-events: none; /* Click passes to label */
        border: 1px solid rgba(0,0,0,0.05);
    }
    
    /* Card Meta Info (Title & Desc) */
    .cv-card-meta {
        padding-top: 1rem;
        text-align: left;
    }

    .cv-card-title {
        font-weight: 700;
        font-size: 1.1rem;
        color: #111827;
        margin-bottom: 0.25rem;
        line-height: 1.3;
    }

    .cv-card-desc {
        font-size: 0.85rem;
        color: var(--tpl-muted);
        line-height: 1.5;
        display: -webkit-box;
        -webkit-line-clamp: 2; /* Limit to 2 lines */
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* --- Footer & Toast --- */
    .cv-sticky-footer {
        position: fixed;
        bottom: 24px;
        left: 50%;
        transform: translateX(-50%) translateY(150%);
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: var(--radius-lg);
        padding: 12px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        z-index: 100;
        width: 90%;
        max-width: 600px;
        transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .cv-sticky-footer.visible { transform: translateX(-50%) translateY(0); }

    .cv-footer-info { display: flex; align-items: center; gap: 12px; }
    .cv-footer-thumb {
        width: 36px; height: 50px;
        border-radius: 4px;
        object-fit: cover;
        border: 1px solid #e5e7eb;
    }
    
    .cv-footer-text div:first-child { font-weight: 700; color: #111827; font-size: 0.95rem; }
    .cv-footer-text div:last-child { font-size: 0.8rem; color: var(--tpl-muted); }

    .cv-submit-btn {
        background: #111827; /* Black button like modern sites */
        color: #fff;
        border: none;
        padding: 10px 24px;
        border-radius: var(--radius-pill);
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }
    .cv-submit-btn:hover { background: #000; }

    /* Toast */
    .toast {
        position: fixed; top: 20px; right: 20px;
        background: #fff; border-left: 4px solid #10b981;
        padding: 12px 20px; border-radius: 8px;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
        z-index: 2000; font-size: 0.9rem; color: #374151;
        animation: slideIn 0.3s ease;
    }
    
    @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    @keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); } 70% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); } 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); } }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    .no-results {
        grid-column: 1 / -1;
        text-align: center;
        padding: 4rem;
        color: var(--tpl-muted);
        background: #fff;
        border-radius: var(--radius-lg);
        border: 2px dashed #e5e7eb;
    }

    .hidden {
        display: none !important;
    }

    @media (max-width: 640px) {
        .cv-grid { grid-template-columns: 1fr; } /* Stack on mobile for better readability */
        .cv-sticky-footer { flex-direction: column; padding: 1rem; bottom: 10px; }
        .cv-submit-btn { width: 100%; }
    }
</style>

<div class="cv-container">
    <!-- Header -->
    <header class="cv-header">
        <h1>Design Your Future</h1>
        <p>Pilih template profesional untuk memulai karir Anda.</p>
    </header>

    @if($templates->isEmpty())
        <div class="no-results">
            <h3>No Templates Found</h3>
            <p>Belum ada template yang tersedia.</p>
        </div>
    @else
    <form method="POST" action="{{ route('cv-builder.templates.save') }}" id="cvForm">
        @csrf

        <!-- Toolbar -->
        <div class="cv-controls">
            <div class="toolbar">
                <div class="search-group">
                    <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <input type="text" id="tplSearch" class="cv-control-input" placeholder="Cari template..." autocomplete="off">
                    <button type="button" id="voiceBtn" class="voice-btn" title="Gunakan Suara">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path><path d="M19 10v2a7 7 0 0 1-14 0v-2"></path><line x1="12" y1="19" x2="12" y2="23"></line><line x1="8" y1="23" x2="16" y2="23"></line></svg>
                    </button>
                </div>
                <div class="divider"></div>
                <div class="select-wrapper">
                    <select id="tplSlugFilter" class="cv-control-select">
                        <option value="">Semua Kategori</option>
                        @foreach($templates->pluck('slug')->filter()->unique()->sort()->values() as $slug)
                            <option value="{{ strtolower($slug) }}">{{ ucfirst($slug) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Grid -->
        <div class="cv-grid" id="templateGrid">
            @foreach($templates as $tpl)
                @php
                    $thumbnailUrl = $tpl->thumbnailPreviewUrl();
                    $defaultSlug = $templates->firstWhere('is_default', true)?->slug ?? $templates->first()->slug;
                    $isChecked = old('template_slug', $defaultSlug) === $tpl->slug;
                @endphp

                <label class="cv-card {{ $isChecked ? 'is-selected' : '' }} js-template-card" 
                       data-name="{{ strtolower($tpl->name) }}" 
                       data-slug="{{ strtolower($tpl->slug) }}">
                    
                    <input type="radio" name="template_slug" value="{{ $tpl->slug }}" {{ $isChecked ? 'checked' : '' }}>

                    <!-- Preview Area (Paper Look) -->
                    <div class="cv-card-preview-wrapper">
                        @if($thumbnailUrl)
                            <img src="{{ $thumbnailUrl }}" alt="{{ $tpl->name }}" class="cv-card-preview" loading="lazy">
                        @else
                            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#ccc;">No Image</div>
                        @endif
                        
                        <!-- Floating Button Overlay -->
                        <div class="cv-btn-overlay">
                            {{ $isChecked ? 'Selected' : 'Use this template' }}
                        </div>
                    </div>

                    <!-- Meta Info (DB Data) -->
                    <div class="cv-card-meta">
                        <div class="cv-card-title">{{ $tpl->name }}</div>
                        <!-- Menggunakan description dari DB. Jika kolom beda, sesuaikan di sini -->
                        <div class="cv-card-desc">{{ $tpl->description ?? 'Template profesional yang cocok untuk berbagai kebutuhan industri.' }}</div>
                    </div>
                </label>
            @endforeach

            <!-- Empty State -->
            <div id="noResults" class="no-results hidden">
                <p>Template tidak ditemukan.</p>
            </div>
        </div>

        <!-- Sticky Footer -->
        <div class="cv-sticky-footer" id="stickyFooter">
            <div class="cv-footer-info">
                <img id="footerThumb" src="" class="cv-footer-thumb" alt="Thumb">
                <div class="cv-footer-text">
                    <div id="footerName">-</div>
                    <div id="footerDesc">Template Pilihan Anda</div>
                </div>
            </div>
            <button type="submit" class="cv-submit-btn">Lanjut →</button>
        </div>

    </form>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('tplSearch');
        const slugFilter = document.getElementById('tplSlugFilter');
        const cards = document.querySelectorAll('.js-template-card');
        const stickyFooter = document.getElementById('stickyFooter');
        const footerName = document.getElementById('footerName');
        const footerThumb = document.getElementById('footerThumb');
        const footerDesc = document.getElementById('footerDesc'); // Added
        const voiceBtn = document.getElementById('voiceBtn');
        const noResults = document.getElementById('noResults');

        // --- 1. Selection Logic ---
        function updateSelection() {
            let selected = false;
            cards.forEach(card => {
                const radio = card.querySelector('input[type="radio"]');
                const btn = card.querySelector('.cv-btn-overlay');
                const name = card.querySelector('.cv-card-title').textContent;
                const desc = card.querySelector('.cv-card-desc').textContent;
                const thumb = card.querySelector('img').src;

                if (radio.checked) {
                    card.classList.add('is-selected');
                    btn.textContent = 'Selected';
                    
                    footerName.textContent = name;
                    footerDesc.textContent = desc; // Update footer desc
                    footerThumb.src = thumb;
                    stickyFooter.classList.add('visible');
                    selected = true;
                } else {
                    card.classList.remove('is-selected');
                    btn.textContent = 'Use this template';
                }
            });
        }

        // Event delegation
        document.getElementById('templateGrid').addEventListener('change', (e) => {
            if(e.target.name === 'template_slug') updateSelection();
        });
        
        // Initialize
        updateSelection();

        // --- 2. Filter Logic ---
        function filterItems() {
            const query = searchInput.value.toLowerCase().trim();
            const selectedSlug = slugFilter.value.toLowerCase().trim();
            let visibleCount = 0;

            cards.forEach(card => {
                const name = card.getAttribute('data-name') || '';
                const slug = card.getAttribute('data-slug') || '';
                
                const matchesSearch = name.includes(query);
                const matchesSlug = !selectedSlug || slug === selectedSlug;
                const isVisible = matchesSearch && matchesSlug;

                if (isVisible) {
                    card.classList.remove('hidden');
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                    card.style.display = 'none';
                }
            });

            noResults.style.display = visibleCount === 0 ? 'block' : 'none';
        }

        searchInput.addEventListener('input', filterItems);
        slugFilter.addEventListener('change', filterItems);

        // Ensure empty-state visibility is correct on first load.
        filterItems();

        // --- 3. Voice Search Logic ---
        function setupVoiceSearch() {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            if (!SpeechRecognition) {
                voiceBtn.style.display = 'none';
                return;
            }

            const recognition = new SpeechRecognition();
            recognition.continuous = false;
            recognition.lang = 'id-ID';
            recognition.interimResults = false;

            voiceBtn.addEventListener('click', () => recognition.start());

            recognition.onstart = () => {
                voiceBtn.classList.add('listening');
                searchInput.placeholder = "Mendengarkan...";
            };

            recognition.onend = () => {
                voiceBtn.classList.remove('listening');
                searchInput.placeholder = "Cari template...";
            };

            recognition.onresult = (event) => {
                const transcript = event.results[0][0].transcript;
                searchInput.value = transcript;
                filterItems();
                showToast(`Mencari: "${transcript}"`);
            };

            recognition.onerror = (e) => {
                voiceBtn.classList.remove('listening');
                console.error(e);
                if(e.error === 'no-speech') showToast("Tidak ada suara", "error");
            };
        }

        setupVoiceSearch();

        // --- Helper: Toast ---
        function showToast(msg, type='success') {
            const toast = document.createElement('div');
            toast.className = 'toast';
            toast.style.borderLeftColor = type === 'error' ? '#ef4444' : '#10b981';
            toast.textContent = msg;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    });
</script>
@endsection