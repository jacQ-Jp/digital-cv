@extends('layouts.app')

@section('content')
<style>
    :root {
        /* --- DARK THEME (Based on Screenshot 1) --- */
        --bg-body: #020617;       /* Very Dark Blue/Black */
        --bg-sidebar: rgba(15, 23, 42, 0.7); /* Glassy Dark */
        --bg-card: #0f172a;       /* Slightly lighter Dark */
        
        --text-main: #f8fafc;     /* White */
        --text-muted: #94a3b8;    /* Muted Gray */
        
        /* Primary Purple (Buat CV Button) */
        --primary: #8b5cf6;       
        --primary-hover: #7c3aed;
        
        /* Button Colors (Based on Screenshot 2) */
        --btn-edit: #3b82f6;      /* Blue */
        --btn-edit-hover: #2563eb;
        
        --btn-light: #e2e8f0;     /* White/Light Gray Background */
        --btn-light-text: #0f172a; /* Dark Text on Light Button */
        --btn-light-hover: #f1f5f9;
        
        --btn-delete: #ec4899;    /* Pink */
        --btn-delete-hover: #db2777;
        
        --border-color: rgba(255, 255, 255, 0.1);
        
        --shadow-card: 0 4px 6px -1px rgba(0, 0, 0, 0.5);
        --shadow-glow: 0 0 20px rgba(139, 92, 246, 0.15);
        
        --radius: 8px;
    }

    body {
        background-color: var(--bg-body);
        color: var(--text-main);
        font-family: 'Inter', sans-serif;
        padding-top: 1rem;
    }

    .cv-history-wrap {
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 1rem 2rem;
    }

    /* --- LAYOUT --- */
    .history-layout {
        display: grid;
        grid-template-columns: 260px 1fr;
        gap: 1.5rem;
        align-items: start;
    }

    /* --- SIDEBAR --- */
    .history-sidebar {
        background: var(--bg-sidebar);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        position: sticky;
        top: 1rem;
        height: calc(100vh - 2rem);
        display: flex;
        flex-direction: column;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }

    .sidebar-header {
        padding: 1.25rem;
        border-bottom: 1px solid var(--border-color);
    }

    .sidebar-title {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--primary); /* Purple Title */
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin: 0;
        display: flex; align-items: center; gap: 0.5rem;
    }

    .sidebar-content {
        flex: 1;
        overflow-y: auto;
        padding: 1.25rem;
    }

    .sidebar-footer {
        padding: 1rem;
        border-top: 1px solid var(--border-color);
    }

    /* Form Inputs Dark */
    .filter-item { margin-bottom: 1.25rem; }
    .filter-label {
        display: block;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--text-muted);
        margin-bottom: 0.5rem;
    }
    .form-control-sm {
        width: 100%;
        padding: 0.6rem 0.8rem;
        font-size: 0.9rem;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        background: #020617; /* Very Dark Input */
        color: var(--text-main);
        transition: all 0.2s;
    }
    .form-control-sm:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 2px rgba(139, 92, 246, 0.2);
    }

    /* --- HEADER --- */
    .content-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--border-color);
    }

    .page-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: #fff;
        margin: 0;
        letter-spacing: -0.02em;
    }

    .header-actions { display: flex; align-items: center; gap: 0.75rem; }

    .bulk-delete-form {
        margin: 0;
    }

    .history-action-wrap {
        position: relative;
    }

    .btn-menu-dots {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        background: transparent;
        color: var(--text-muted);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .btn-menu-dots:hover {
        background: rgba(255,255,255,0.05);
        color: #fff;
    }

    .history-action-menu {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        min-width: 210px;
        background: rgba(10, 14, 27, 0.98);
        border: 1px solid rgba(148, 163, 184, 0.38);
        border-radius: 10px;
        box-shadow: 0 16px 34px rgba(0, 0, 0, 0.45);
        padding: 0.35rem;
        display: none;
        z-index: 12;
    }

    .history-action-wrap.is-open .history-action-menu {
        display: block;
    }

    .history-action-item {
        width: 100%;
        display: block;
        font-size: 0.84rem;
        font-weight: 600;
        border: none;
        background: transparent;
        color: #dbe7ff;
        border-radius: 8px;
        padding: 0.55rem 0.65rem;
        cursor: pointer;
        text-align: left;
    }

    .history-action-item:hover {
        background: rgba(148, 163, 184, 0.16);
    }

    .history-action-item-danger {
        color: #fda4cf;
    }

    .history-action-item-danger:hover {
        background: rgba(236, 72, 153, 0.2);
        color: #fff;
    }

    .history-action-item:disabled,
    .history-action-item:disabled:hover {
        opacity: 0.45;
        cursor: not-allowed;
        background: transparent;
    }

    /* --- BUTTONS (Theme Specific) --- */
    .btn-xs {
        padding: 0.45rem 0.8rem;
        font-size: 0.85rem;
        font-weight: 600;
        border-radius: 6px;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        text-align: center;
    }

    /* Header Buttons */
    .btn-logout {
        background: transparent;
        color: var(--text-muted);
        border: 1px solid var(--border-color);
    }
    .btn-logout:hover { background: rgba(255,255,255,0.05); color: #fff; }

    .btn-create-new {
        background: var(--primary);
        color: #fff;
        box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
    }
    .btn-create-new:hover { background: var(--primary-hover); transform: translateY(-1px); }

    .btn-delete-multi {
        background: var(--btn-delete);
        color: #fff;
        border: 1px solid transparent;
    }

    .btn-delete-multi:hover:not(:disabled) {
        background: var(--btn-delete-hover);
        transform: translateY(-1px);
    }

    .btn-delete-multi:disabled {
        opacity: 0.45;
        cursor: not-allowed;
        transform: none;
    }

    /* --- CARD ACTION BUTTONS (Screenshot 2 Style) --- */
    
    /* 1. Edit (Blue) */
    .btn-edit { background: var(--btn-edit); color: white; }
    .btn-edit:hover { background: var(--btn-edit-hover); }

    /* 2. Lihat, Public Link, Unpub (White/Light) */
    .btn-light {
        background: var(--btn-light);
        color: var(--btn-light-text);
        border: 1px solid transparent;
    }
    .btn-light:hover {
        background: var(--btn-light-hover);
        border-color: #cbd5e1;
    }

    /* 3. Hapus (Pink) */
    .btn-delete { background: var(--btn-delete); color: white; }
    .btn-delete:hover { background: var(--btn-delete-hover); }

    /* --- GRID & CARD --- */
    .history-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 1.5rem;
    }

    .history-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: all 0.3s ease;
        box-shadow: var(--shadow-card);
    }

    .history-card:hover {
        transform: translateY(-4px);
        border-color: var(--primary);
        box-shadow: var(--shadow-glow);
    }

    .history-card.is-selected {
        border-color: rgba(236, 72, 153, 0.9);
        box-shadow: 0 0 0 2px rgba(236, 72, 153, 0.3), var(--shadow-glow);
    }

    /* Thumbnail */
    .thumb-container {
        aspect-ratio: 210/297;
        background: #1e293b;
        border-bottom: 1px solid var(--border-color);
        position: relative;
        overflow: hidden;
        padding: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .thumb-iframe {
        width: 100%;
        height: 100%;
        border: none;
        background: #fff;
        box-shadow: 0 4px 6px rgba(0,0,0,0.3);
        pointer-events: none;
    }

    .card-select {
        position: absolute;
        top: 12px;
        left: 12px;
        z-index: 6;
        display: none;
    }

    .history-grid.selection-mode .card-select {
        display: block;
    }

    .card-select input {
        width: 18px;
        height: 18px;
        accent-color: #ec4899;
        cursor: pointer;
    }

    .card-menu {
        position: absolute;
        top: 12px;
        right: 12px;
        z-index: 5;
    }

    .card-menu-toggle {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        border: 1px solid rgba(148, 163, 184, 0.5);
        background: rgba(2, 6, 23, 0.82);
        color: #e2e8f0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .card-menu-toggle:hover {
        border-color: rgba(139, 92, 246, 0.68);
        color: #fff;
        background: rgba(30, 41, 59, 0.92);
    }

    .card-menu-list {
        position: absolute;
        top: 40px;
        right: 0;
        min-width: 128px;
        background: rgba(10, 14, 27, 0.98);
        border: 1px solid rgba(148, 163, 184, 0.38);
        border-radius: 10px;
        box-shadow: 0 16px 34px rgba(0, 0, 0, 0.45);
        padding: 0.35rem;
        display: none;
    }

    .card-menu.is-open .card-menu-list {
        display: block;
    }

    .card-menu-item {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 0.45rem;
        font-size: 0.82rem;
        font-weight: 600;
        border: none;
        background: transparent;
        color: #dbe7ff;
        border-radius: 8px;
        padding: 0.5rem 0.6rem;
        cursor: pointer;
        text-align: left;
    }

    .card-menu-item:hover {
        background: rgba(148, 163, 184, 0.16);
    }

    .card-menu-item-danger {
        color: #fda4cf;
    }

    .card-menu-item-danger:hover {
        background: rgba(236, 72, 153, 0.2);
        color: #fff;
    }

    .card-menu-delete-form {
        margin: 0;
    }

    /* Card Body */
    .card-body {
        padding: 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        flex: 1;
    }

    .card-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 0.5rem;
        margin-bottom: 0.25rem;
    }

    .card-title {
        font-size: 1rem;
        font-weight: 700;
        color: #fff;
        line-height: 1.3;
        margin: 0;
        word-break: break-word;
    }

    .status-badge {
        font-size: 0.7rem;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        flex-shrink: 0;
    }
    .status-draft { background: rgba(255,255,255,0.1); color: var(--text-muted); }
    .status-published { background: rgba(139, 92, 246, 0.2); color: #a78bfa; border: 1px solid rgba(139, 92, 246, 0.3); }

    .card-meta { font-size: 0.8rem; color: var(--text-muted); margin: 0; }

    /* Action Layout - Flexbox for tidy buttons */
    .card-actions {
        margin-top: auto;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.55rem;
        padding-top: 1rem;
        border-top: 1px solid var(--border-color);
    }

    .card-actions > :last-child:nth-child(odd) {
        grid-column: 1 / -1;
    }

    .card-actions .btn-xs,
    .card-actions form,
    .card-actions form .btn-xs {
        width: 100%;
    }

    .card-actions form {
        margin: 0;
    }

    .card-actions .btn-xs {
        min-height: 38px;
    }

    .btn-icon {
        width: 100%;
        min-height: 38px;
        padding: 0;
        border-radius: 8px;
    }

    .btn-icon svg {
        width: 18px;
        height: 18px;
    }

    /* Toast */
    .copy-toast {
        --toast-offset-top: 86px;
        position: fixed !important;
        top: var(--toast-offset-top) !important;
        right: 16px !important;
        left: auto !important;
        bottom: auto !important;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        width: fit-content !important;
        max-width: min(90vw, 360px);
        min-height: 46px;
        background: rgba(2, 6, 23, 0.92);
        border: 1px solid rgba(139, 92, 246, 0.72);
        color: #f8fafc;
        border-radius: 12px;
        padding: 0.72rem 1rem;
        font-size: 0.92rem;
        font-weight: 700;
        letter-spacing: 0.01em;
        opacity: 0;
        transform: translateY(-12px) scale(0.98);
        pointer-events: none;
        transition: opacity 0.22s ease, transform 0.22s ease;
        z-index: 2500;
        box-shadow: 0 14px 28px rgba(15, 23, 42, 0.44), 0 0 0 1px rgba(139, 92, 246, 0.2);
        backdrop-filter: blur(10px);
    }

    @media (max-width: 768px) {
        .copy-toast {
            --toast-offset-top: 76px;
        }
    }
    .copy-toast.show {
        opacity: 1;
        transform: translateY(0) scale(1);
    }

    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 3rem;
        background: rgba(30, 41, 59, 0.5);
        border-radius: var(--radius);
        border: 2px dashed var(--border-color);
        color: var(--text-muted);
    }

    @media (max-width: 900px) {
        .history-layout { grid-template-columns: 1fr; }
        .history-sidebar { position: static; height: auto; margin-bottom: 2rem; }
    }
</style>

<div class="cv-history-wrap">
    <!-- Header -->
    <div class="content-header">
        <h1 class="page-title">History CV</h1>
        <div class="header-actions">
            <form id="bulkDeleteCvsForm" method="POST" action="{{ route('cvs.bulk-destroy') }}" class="bulk-delete-form">
                @csrf
                @method('DELETE')
            </form>

            <div class="history-action-wrap" id="historyActionWrap">
                <button type="button" id="historyActionMenuToggle" class="btn-menu-dots" aria-haspopup="true" aria-expanded="false" title="Pilihan aksi">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <circle cx="12" cy="5" r="1.8"></circle>
                        <circle cx="12" cy="12" r="1.8"></circle>
                        <circle cx="12" cy="19" r="1.8"></circle>
                    </svg>
                </button>

                <div id="historyActionMenu" class="history-action-menu" role="menu">
                    <button type="button" id="toggleCvSelectionBtn" class="history-action-item" role="menuitem">Pilih</button>
                    <button type="button" id="selectAllCvsBtn" class="history-action-item" role="menuitem" disabled>Pilih Semua</button>
                    <button type="button" id="bulkDeleteCvsBtn" class="history-action-item history-action-item-danger" role="menuitem" disabled>Hapus Terpilih</button>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="btn-xs btn-logout">Logout</button>
            </form>
            <a href="{{ route('cv-builder.templates') }}" class="btn-xs btn-create-new">
                + Buat Baru
            </a>
        </div>
    </div>

    <div class="history-layout">
        <!-- SIDEBAR -->
        <aside class="history-sidebar">
            <div class="sidebar-header">
                <h2 class="sidebar-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                    Filter History
                </h2>
            </div>
            <div class="sidebar-content">
                <div class="filter-item">
                    <label class="filter-label" for="filter-q">Cari Judul</label>
                    <input type="text" id="filter-q" class="form-control-sm" placeholder="Ketik nama CV...">
                </div>
                <div class="filter-item">
                    <label class="filter-label" for="filter-status">Status</label>
                    <select id="filter-status" class="form-control-sm">
                        <option value="">Semua Status</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>
                <div class="filter-item">
                    <label class="filter-label" for="filter-template">Template</label>
                    <select id="filter-template" class="form-control-sm">
                        <option value="">Semua Template</option>
                        @foreach($templates->pluck('slug')->filter()->unique()->sort()->values() as $slug)
                            <option value="{{ $slug }}" {{ request('template') === $slug ? 'selected' : '' }}>
                                {{ \Illuminate\Support\Str::title(str_replace('-', ' ', $slug)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="sidebar-footer">
                <a href="{{ route('cvs.index') }}" class="btn-xs btn-light" style="width: 100%;">Reset Filter</a>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <section>
            @if($cvs->isEmpty())
                <div class="empty-state">
                    <h3>Belum ada CV</h3>
                    <p>Anda belum membuat CV apapun.</p>
                </div>
            @else
                <div class="history-grid" id="historyGrid">
                    @foreach($cvs as $cv)
                        @php
                            $templateLabel = $cv->template?->name ?: \Illuminate\Support\Str::title(str_replace('-', ' ', (string) $cv->template_slug));
                            $cvSlug = $cv->template_slug ?? '';
                        @endphp
                        
                        <article class="history-card" 
                                  data-title="{{ strtolower($cv->title ?: 'Untitled CV') }}" 
                                  data-status="{{ $cv->status }}" 
                                  data-template="{{ strtolower($cvSlug) }}">
                            <input type="checkbox" class="js-cv-select" name="cv_ids[]" value="{{ $cv->id }}" form="bulkDeleteCvsForm" hidden>
                            
                            <div class="thumb-container">
                                <label class="card-select" aria-label="Pilih CV">
                                    <input type="checkbox" class="js-cv-select-proxy" data-cv-id="{{ $cv->id }}">
                                </label>
                                <iframe src="{{ route('cvs.thumb', ['cv' => $cv, 'v' => $cv->updated_at?->timestamp]) }}" class="thumb-iframe" loading="lazy"></iframe>

                                <div class="card-menu js-card-menu">
                                    <button type="button" class="card-menu-toggle js-menu-toggle" aria-haspopup="true" aria-expanded="false" title="Menu aksi">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                            <circle cx="12" cy="5" r="1.8"></circle>
                                            <circle cx="12" cy="12" r="1.8"></circle>
                                            <circle cx="12" cy="19" r="1.8"></circle>
                                        </svg>
                                    </button>
                                    <div class="card-menu-list" role="menu">
                                        <form method="POST" action="{{ route('cvs.destroy', $cv) }}" onsubmit="return confirm('Hapus CV ini?');" class="card-menu-delete-form">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="card-menu-item card-menu-item-danger" role="menuitem">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="card-head">
                                    <h3 class="card-title">{{ $cv->title ?: 'Untitled CV' }}</h3>
                                    <span class="status-badge status-{{ $cv->status }}">{{ $cv->status }}</span>
                                </div>
                                <p class="card-meta"><strong>{{ $templateLabel }}</strong></p>
                                <p class="card-meta">{{ $cv->updated_at->format('d M Y') }}</p>

                                <!-- Button Action Area (Rapikan) -->
                                <div class="card-actions">
                                    <!-- 1. Edit (Blue) -->
                                    <a href="{{ route('cvs.wizard', $cv) }}" class="btn-xs btn-edit btn-icon" aria-label="Edit CV" title="Edit CV">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487a2.1 2.1 0 1 1 2.97 2.97L8.25 19.04 4.5 19.5l.46-3.75L16.862 4.487Z"/>
                                        </svg>
                                    </a>
                                    
                                    <!-- 2. Lihat (White) -->
                                    <a href="{{ route('cvs.render', $cv) }}" class="btn-xs btn-light btn-icon" target="_blank" aria-label="Lihat CV" title="Lihat CV">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12Z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </a>
                                    
                                    @if($cv->status === 'published')
                                        <!-- 3. Public Link (White) -->
                                        <button type="button" class="btn-xs btn-light js-copy-link" data-copy-url="{{ $cv->public_uuid ? route('cvs.public', ['token' => $cv->public_uuid]) : '' }}">Public Link</button>
                                    @endif

                                    <form method="POST" action="{{ route('cvs.toggle-publish', $cv) }}" class="js-toggle-publish-form">
                                        @csrf @method('PATCH')
                                        <!-- 4. Unpub/Pub (White) -->
                                        <button type="submit" class="btn-xs btn-light js-toggle-publish-btn">
                                            {{ $cv->status === 'published' ? 'Unpub' : 'Pub' }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
                
                <div id="noFilterResults" class="empty-state" style="display: none; margin-top: 2rem;">
                    <p>Tidak ada CV yang cocok dengan filter.</p>
                </div>
            @endif
        </section>
    </div>
</div>

<div id="copyToast" class="copy-toast" aria-live="polite">Link copied!</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('filter-q');
        const statusInput = document.getElementById('filter-status');
        const templateInput = document.getElementById('filter-template');
        const cards = Array.from(document.querySelectorAll('.history-card'));
        const noResults = document.getElementById('noFilterResults');
        const historyGrid = document.getElementById('historyGrid');
        const copyToast = document.getElementById('copyToast');
        const historyActionWrap = document.getElementById('historyActionWrap');
        const historyActionMenuToggle = document.getElementById('historyActionMenuToggle');
        const toggleCvSelectionBtn = document.getElementById('toggleCvSelectionBtn');
        const selectAllCvsBtn = document.getElementById('selectAllCvsBtn');
        const bulkDeleteCvsBtn = document.getElementById('bulkDeleteCvsBtn');
        const bulkDeleteCvsForm = document.getElementById('bulkDeleteCvsForm');
        const hiddenSelectionInputs = Array.from(document.querySelectorAll('.js-cv-select'));
        const proxySelectionInputs = Array.from(document.querySelectorAll('.js-cv-select-proxy'));
        let toastTimer = null;

        function closeAllMenus(exceptMenu = null) {
            document.querySelectorAll('.js-card-menu.is-open').forEach((menu) => {
                if (menu === exceptMenu) return;
                menu.classList.remove('is-open');
                const toggle = menu.querySelector('.js-menu-toggle');
                if (toggle) toggle.setAttribute('aria-expanded', 'false');
            });
        }

        document.querySelectorAll('.js-menu-toggle').forEach((toggleBtn) => {
            toggleBtn.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                const menu = toggleBtn.closest('.js-card-menu');
                if (!menu) return;
                const willOpen = !menu.classList.contains('is-open');
                closeAllMenus(menu);
                menu.classList.toggle('is-open', willOpen);
                toggleBtn.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
            });
        });

        document.addEventListener('click', (event) => {
            if (!event.target.closest('.js-card-menu')) {
                closeAllMenus();
            }

            if (!event.target.closest('#historyActionWrap')) {
                historyActionWrap?.classList.remove('is-open');
                historyActionMenuToggle?.setAttribute('aria-expanded', 'false');
            }
        });

        function updateSelectionUI() {
            let selectedCount = 0;
            proxySelectionInputs.forEach((proxyInput) => {
                const cvId = proxyInput.getAttribute('data-cv-id');
                const target = hiddenSelectionInputs.find((input) => input.value === cvId);
                const card = proxyInput.closest('.history-card');

                if (target && proxyInput.checked) {
                    target.checked = true;
                    selectedCount += 1;
                    card?.classList.add('is-selected');
                } else {
                    if (target) target.checked = false;
                    card?.classList.remove('is-selected');
                }
            });

            const hasItems = proxySelectionInputs.length > 0;
            const allSelected = hasItems && selectedCount === proxySelectionInputs.length;

            if (bulkDeleteCvsBtn) {
                bulkDeleteCvsBtn.disabled = selectedCount === 0;
                bulkDeleteCvsBtn.textContent = selectedCount > 0
                    ? `Hapus Terpilih (${selectedCount})`
                    : 'Hapus Terpilih';
            }

            if (selectAllCvsBtn) {
                selectAllCvsBtn.disabled = !hasItems;
                selectAllCvsBtn.textContent = allSelected ? 'Batal Pilih Semua' : 'Pilih Semua';
            }
        }

        function setSelectionMode(enabled) {
            historyGrid?.classList.toggle('selection-mode', enabled);
            if (toggleCvSelectionBtn) {
                toggleCvSelectionBtn.textContent = enabled ? 'Batal Pilih' : 'Pilih';
            }

            if (!enabled) {
                proxySelectionInputs.forEach((input) => {
                    input.checked = false;
                });
            }

            updateSelectionUI();
        }

        historyActionMenuToggle?.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            const willOpen = !historyActionWrap?.classList.contains('is-open');
            historyActionWrap?.classList.toggle('is-open', willOpen);
            historyActionMenuToggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
        });

        toggleCvSelectionBtn?.addEventListener('click', () => {
            const enabled = !!historyGrid?.classList.contains('selection-mode');
            setSelectionMode(!enabled);
        });

        selectAllCvsBtn?.addEventListener('click', () => {
            const shouldSelectAll = proxySelectionInputs.some((input) => !input.checked);
            proxySelectionInputs.forEach((input) => {
                input.checked = shouldSelectAll;
            });
            updateSelectionUI();
        });

        bulkDeleteCvsBtn?.addEventListener('click', () => {
            if (bulkDeleteCvsBtn.disabled) return;
            if (!confirm('Hapus semua CV yang dipilih?')) return;
            bulkDeleteCvsForm?.requestSubmit();
        });

        proxySelectionInputs.forEach((input) => {
            input.addEventListener('change', updateSelectionUI);
        });

        setSelectionMode(false);

        function showCopyToast(message) {
            if (!copyToast) return;
            copyToast.textContent = message;
            copyToast.classList.add('show');
            if (toastTimer) clearTimeout(toastTimer);
            toastTimer = setTimeout(() => copyToast.classList.remove('show'), 2000);
        }

        function handleWizardNotice() {
            const params = new URLSearchParams(window.location.search);
            const notice = params.get('wizard_notice');
            if (!notice) return;

            if (notice === 'link_copied') {
                showCopyToast('link copied!');
            } else if (notice === 'published') {
                showCopyToast('CV berhasil dipublikasikan');
            } else if (notice === 'draft_saved') {
                showCopyToast('Draft berhasil disimpan');
            }

            params.delete('wizard_notice');
            const nextQuery = params.toString();
            const nextUrl = `${window.location.pathname}${nextQuery ? `?${nextQuery}` : ''}${window.location.hash}`;
            window.history.replaceState({}, document.title, nextUrl);
        }

        handleWizardNotice();

        async function copyText(text) {
            if (!text) return false;
            if (navigator.clipboard && window.isSecureContext) {
                try {
                    await navigator.clipboard.writeText(text);
                    return true;
                } catch (e) {}
            }
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed'; textarea.style.top = '-9999px';
            document.body.appendChild(textarea); textarea.focus(); textarea.select();
            try { document.execCommand('copy'); } catch (e) {}
            document.body.removeChild(textarea);
            return true;
        }

        function ensurePublicLinkButton(card, publicUrl) {
            const actions = card.querySelector('.card-actions');
            const toggleForm = card.querySelector('.js-toggle-publish-form');
            if (!actions || !toggleForm) return;
            let copyBtn = card.querySelector('.js-copy-link');
            if (!copyBtn) {
                copyBtn = document.createElement('button');
                copyBtn.type = 'button';
                copyBtn.className = 'btn-xs btn-light js-copy-link';
                copyBtn.textContent = 'Public Link';
                actions.insertBefore(copyBtn, toggleForm);
            }
            copyBtn.setAttribute('data-copy-url', publicUrl || '');
        }

        function removePublicLinkButton(card) {
            const copyBtn = card.querySelector('.js-copy-link');
            if (copyBtn) copyBtn.remove();
        }

        function updateCardPublishState(card, status, publicUrl) {
            const normalizedStatus = status === 'published' ? 'published' : 'draft';
            card.setAttribute('data-status', normalizedStatus);
            const statusBadge = card.querySelector('.status-badge');
            if (statusBadge) {
                statusBadge.className = 'status-badge status-' + normalizedStatus;
                statusBadge.textContent = normalizedStatus;
            }
            const toggleBtn = card.querySelector('.js-toggle-publish-btn');
            if (toggleBtn) toggleBtn.textContent = normalizedStatus === 'published' ? 'Unpub' : 'Pub';
            if (normalizedStatus === 'published') ensurePublicLinkButton(card, publicUrl);
            else removePublicLinkButton(card);
        }

        async function requestTogglePublish(form) {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin', body: formData,
            });
            const payload = await response.json().catch(() => ({}));
            if (!response.ok) throw new Error(payload.message || 'Gagal mengubah status CV.');
            return payload;
        }

        function filterHistory() {
            const query = searchInput.value.toLowerCase().trim();
            const status = statusInput.value.toLowerCase().trim();
            const template = templateInput.value.toLowerCase().trim();
            let visibleCount = 0;
            cards.forEach((card) => {
                const title = card.getAttribute('data-title') || '';
                const cardStatus = card.getAttribute('data-status') || '';
                const cardTemplate = card.getAttribute('data-template') || '';
                const matchQuery = title.includes(query);
                const matchStatus = !status || cardStatus === status;
                const matchTemplate = !template || cardTemplate === template;
                if (matchQuery && matchStatus && matchTemplate) {
                    card.classList.remove('hidden-card');
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.classList.add('hidden-card');
                    card.style.display = 'none';
                }
            });
            noResults.style.display = visibleCount === 0 ? 'block' : 'none';
        }

        searchInput.addEventListener('input', filterHistory);
        statusInput.addEventListener('change', filterHistory);
        templateInput.addEventListener('change', filterHistory);

        historyGrid?.addEventListener('click', async (event) => {
            const button = event.target.closest('.js-copy-link');
            if (!button) return;
            const url = button.getAttribute('data-copy-url') || '';
            const copied = await copyText(url);
            if (copied) showCopyToast('link copied!');
            else window.prompt('Copy this link:', url);
        });

        historyGrid?.addEventListener('submit', async (event) => {
            const form = event.target.closest('.js-toggle-publish-form');
            if (!form) return;
            event.preventDefault();
            const card = form.closest('.history-card');
            if (!card) return;
            const toggleBtn = form.querySelector('.js-toggle-publish-btn');
            if (toggleBtn) toggleBtn.disabled = true;
            try {
                const payload = await requestTogglePublish(form);
                const nextStatus = (payload.status || '').toLowerCase() === 'published' ? 'published' : 'draft';
                const publicUrl = payload.public_url || '';
                updateCardPublishState(card, nextStatus, publicUrl);
                filterHistory();
                if (nextStatus === 'published') {
                    const copied = await copyText(publicUrl);
                    if (copied) showCopyToast('link copied!');
                    else if (publicUrl) window.prompt('Copy this link:', publicUrl);
                    else showCopyToast('CV berhasil dipublikasikan');
                } else {
                    showCopyToast('Status CV diubah ke draft');
                }
            } catch (error) {
                showCopyToast(error.message || 'Gagal mengubah status CV');
            } finally {
                if (toggleBtn) {
                    toggleBtn.disabled = false;
                    toggleBtn.textContent = card.getAttribute('data-status') === 'published' ? 'Unpub' : 'Pub';
                }
            }
        });
    });
</script>
@endsection