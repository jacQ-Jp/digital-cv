@extends('layouts.app')

@section('content')
<div class="cv-page">

    {{-- ==================== SIDEBAR FILTER ==================== --}}
    <aside class="cv-sidebar" id="cvSidebar">
        <div class="cv-sidebar-inner">
            {{-- Sidebar header --}}
            <div class="cv-sidebar-header">
                <div class="cv-sidebar-dots">
                    <span></span><span></span><span></span>
                </div>
                <button class="cv-sidebar-close" onclick="toggleSidebar()" aria-label="Close filters">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <h2 class="cv-sidebar-title">Filters</h2>

            <form method="GET" action="{{ route('cvs.index') }}" class="cv-sidebar-form">
                {{-- Search --}}
                <div class="cv-sidebar-group">
                    <label class="cv-sidebar-label">Cari CV</label>
                    <div class="cv-sidebar-search">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input name="q" value="{{ request('q') }}" placeholder="Ketik nama CV…" autocomplete="off" />
                    </div>
                </div>

                {{-- Status --}}
                <div class="cv-sidebar-group">
                    <label class="cv-sidebar-label">Status</label>
                    <div class="cv-sidebar-radios">
                        <label class="cv-radio">
                            <input type="radio" name="status" value="" {{ !request('status') ? 'checked' : '' }} />
                            <span class="cv-radio-mark"></span>
                            Semua Status
                        </label>
                        <label class="cv-radio">
                            <input type="radio" name="status" value="published" {{ request('status') === 'published' ? 'checked' : '' }} />
                            <span class="cv-radio-mark cv-radio-green"></span>
                            Published
                        </label>
                        <label class="cv-radio">
                            <input type="radio" name="status" value="draft" {{ request('status') === 'draft' ? 'checked' : '' }} />
                            <span class="cv-radio-mark cv-radio-amber"></span>
                            Draft
                        </label>
                    </div>
                </div>

                {{-- Template --}}
                <div class="cv-sidebar-group">
                    <label class="cv-sidebar-label">Template</label>
                    <select name="template" class="cv-sidebar-select">
                        <option value="">Semua Template</option>
                        @foreach(($templates ?? collect()) as $tpl)
                            <option value="{{ $tpl->slug }}" {{ request('template') === $tpl->slug ? 'selected' : '' }}>{{ $tpl->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Actions --}}
                <div class="cv-sidebar-actions">
                    @if(request('q') || request('status') || request('template'))
                        <a href="{{ route('cvs.index') }}" class="cv-sidebar-reset">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                            Reset Filter
                        </a>
                    @endif
                    <button type="submit" class="cv-sidebar-apply">
                        Terapkan Filter
                    </button>
                </div>
            </form>
        </div>
    </aside>

    {{-- Overlay for mobile --}}
    <div class="cv-sidebar-overlay" id="cvSidebarOverlay" onclick="toggleSidebar()"></div>

    {{-- ==================== MAIN CONTENT ==================== --}}
    <main class="cv-main">

        {{-- Top bar --}}
        <div class="cv-topbar">
            <div class="cv-topbar-left">
                <button class="cv-filter-toggle" onclick="toggleSidebar()">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="12" x2="14" y2="12"/><line x1="4" y1="18" x2="8" y2="18"/></svg>
                    Filters
                    @if(request('q') || request('status') || request('template'))
                        <span class="cv-filter-count">{{ (request('q') ? 1 : 0) + (request('status') ? 1 : 0) + (request('template') ? 1 : 0) }}</span>
                    @endif
                </button>

                <div class="cv-breadcrumb">
                    <a href="{{ route('home') }}">Dashboard</a>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                    <span>My CVs</span>
                </div>
            </div>

            <a href="{{ route('cv-builder.templates') }}" class="cv-btn-create">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Buat CV Baru
            </a>
        </div>

        {{-- Page header --}}
        <div class="cv-page-header">
            <div>
                <h1 class="cv-page-title">My CVs</h1>
                <p class="cv-page-subtitle">
                    {{ $cvs->count() }} CV tersimpan
                    @if(request('q'))
                        &mdash; hasil pencarian "<strong>{{ request('q') }}</strong>"
                    @endif
                </p>
            </div>
        </div>

        {{-- ==================== EMPTY STATE ==================== --}}
        @if($cvs->isEmpty())
            <div class="cv-empty">
                <div class="cv-empty-visual">
                    <div class="cv-empty-doc">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <path d="M14 2v6h6"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                            <line x1="10" y1="9" x2="8" y2="9"/>
                        </svg>
                    </div>
                    <div class="cv-empty-circle cv-empty-circle-1"></div>
                    <div class="cv-empty-circle cv-empty-circle-2"></div>
                </div>
                <h3 class="cv-empty-title">Belum ada CV</h3>
                <p class="cv-empty-text">
                    Mulai buat CV pertamamu dengan memilih template profesional yang sesuai kebutuhan.
                </p>
                <a href="{{ route('cv-builder.templates') }}" class="cv-btn-create cv-btn-create-lg">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Buat CV Pertama
                </a>
            </div>

        {{-- ==================== CV CARDS GRID ==================== --}}
        @else
            <div class="cv-grid">

                @foreach($cvs as $cv)
                    @php
                        $isPublished = $cv->status === 'published';

                        // Warna accent per card — rotasi 4 warna
                        $accents = [
                            ['border' => '#8b5cf6', 'bg' => '#f5f3ff', 'glow' => 'rgba(139,92,246,0.08)'],
                            ['border' => '#10b981', 'bg' => '#ecfdf5', 'glow' => 'rgba(16,185,129,0.08)'],
                            ['border' => '#f59e0b', 'bg' => '#fffbeb', 'glow' => 'rgba(245,158,11,0.08)'],
                            ['border' => '#3b82f6', 'bg' => '#eff6ff', 'glow' => 'rgba(59,130,246,0.08)'],
                        ];
                        $accent = $accents[$loop->index % 4];

                        $tpl = method_exists($cv, 'template') ? $cv->template : null;
                        $thumb = $tpl?->thumbnail ?? asset('images/templates/' . ($cv->template_slug ?? ($tpl?->slug ?? '')) . '.png');
                    @endphp

                    <div class="cv-card" style="--accent: {{ $accent['border'] }}; --accent-bg: {{ $accent['bg'] }}; --accent-glow: {{ $accent['glow'] }};">

                        {{-- Thumbnail area --}}
                        <div class="cv-card-thumb">
                            @if(!empty($thumb))
                                <img src="{{ $thumb }}" alt="{{ $cv->template_slug }}" loading="lazy" onerror="this.parentElement.classList.add('cv-thumb-fallback')" />
                            @else
                                <div class="cv-thumb-placeholder">
                                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                                        <line x1="3" y1="9" x2="21" y2="9"/>
                                        <line x1="9" y1="3" x2="9" y2="21"/>
                                    </svg>
                                </div>
                            @endif

                            {{-- Status badge overlay --}}
                            <div class="cv-card-badge-wrap">
                                @if($isPublished)
                                    <span class="cv-status-badge cv-status-published">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                        Published
                                    </span>
                                @else
                                    <span class="cv-status-badge cv-status-draft">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                        Draft
                                    </span>
                                @endif
                            </div>

                            {{-- Hover overlay with quick actions --}}
                            <div class="cv-card-hover-overlay">
                                <a href="{{ route('cvs.edit', $cv) }}" class="cv-quick-btn">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    Edit
                                </a>
                                <a href="{{ route('cvs.render', $cv) }}" target="_blank" class="cv-quick-btn">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    Preview
                                </a>
                            </div>
                        </div>

                        {{-- Card info --}}
                        <div class="cv-card-info">
                            <span class="cv-card-tpl">{{ $cv->template_slug }}</span>
                            <h3 class="cv-card-name">{{ $cv->title }}</h3>
                            @if($cv->summary)
                                <p class="cv-card-desc">{{ \Illuminate\Support\Str::limit($cv->summary, 80) }}</p>
                            @endif
                            <div class="cv-card-footer">
                                <span class="cv-card-time">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    {{ $cv->updated_at?->diffForHumans() }}
                                </span>

                                {{-- More menu --}}
                                <div class="cv-more" id="more-{{ $cv->id }}">
                                    <button type="button" class="cv-more-btn" onclick="toggleMore({{ $cv->id }})">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg>
                                    </button>
                                    <div class="cv-more-menu" id="menu-{{ $cv->id }}">
                                        <a href="{{ route('cvs.show', $cv) }}" class="cv-more-item">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                            Lihat Detail
                                        </a>
                                        @if($isPublished)
                                            <button type="button" class="cv-more-item" data-copy="{{ route('cvs.public', ['token' => $cv->public_uuid ?: $cv->id]) }}">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                                Salin Link Publik
                                            </button>
                                        @endif
                                        <form method="POST" action="{{ route('cvs.toggle-publish', $cv) }}" class="cv-more-form">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="cv-more-item">
                                                @if($isPublished)
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                                    Tarik sebagai Draft
                                                @else
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v4"/><path d="M10 14 21 3"/><path d="M21 10v11a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                                                    Publikasikan
                                                @endif
                                            </button>
                                        </form>
                                        <div class="cv-more-divider"></div>
                                        <form method="POST" action="{{ route('cvs.destroy', $cv) }}" class="cv-more-form" onsubmit="return confirm('Yakin ingin menghapus CV ini? Aksi ini tidak bisa dibatalkan.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="cv-more-item cv-more-danger">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                                Hapus CV
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Bottom action bar --}}
                        <div class="cv-card-bar">
                            <a href="{{ route('cvs.edit', $cv) }}" class="cv-bar-btn cv-bar-primary">Edit CV</a>
                            <a href="{{ route('cvs.render', $cv) }}" target="_blank" class="cv-bar-btn cv-bar-outline">Preview</a>
                        </div>
                    </div>
                @endforeach

            </div>
        @endif

    </main>
</div>

<style>
/* ═══════════════════════════════════════════════
   PAGE LAYOUT
   ═══════════════════════════════════════════════ */
.cv-page {
    display: flex;
    min-height: calc(100vh - 64px);
    background: #f1f5f9;
}

/* ═══════════════════════════════════════════════
   SIDEBAR
   ═══════════════════════════════════════════════ */
.cv-sidebar {
    width: 280px;
    flex-shrink: 0;
    background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
    position: sticky;
    top: 64px;
    height: calc(100vh - 64px);
    overflow-y: auto;
    z-index: 40;
    transition: transform 0.3s cubic-bezier(0.4,0,0.2,1);
}
.cv-sidebar::-webkit-scrollbar { width: 4px; }
.cv-sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }

.cv-sidebar-inner {
    padding: 24px 20px 32px;
}

.cv-sidebar-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 28px;
}
.cv-sidebar-dots {
    display: flex;
    gap: 6px;
}
.cv-sidebar-dots span {
    width: 10px;
    height: 10px;
    border-radius: 50%;
}
.cv-sidebar-dots span:nth-child(1) { background: #ef4444; }
.cv-sidebar-dots span:nth-child(2) { background: #f59e0b; }
.cv-sidebar-dots span:nth-child(3) { background: #22c55e; }
.cv-sidebar-close {
    display: none;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: rgba(255,255,255,0.06);
    border: none;
    color: #94a3b8;
    cursor: pointer;
    transition: all 0.15s;
}
.cv-sidebar-close:hover { background: rgba(255,255,255,0.12); color: #fff; }

.cv-sidebar-title {
    font-size: 0.6875rem;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    margin: 0 0 24px;
}

.cv-sidebar-group {
    margin-bottom: 22px;
}
.cv-sidebar-label {
    display: block;
    font-size: 0.8125rem;
    font-weight: 600;
    color: #e2e8f0;
    margin-bottom: 10px;
}

/* Search in sidebar */
.cv-sidebar-search {
    position: relative;
    display: flex;
    align-items: center;
}
.cv-sidebar-search svg {
    position: absolute;
    left: 12px;
    color: #475569;
    pointer-events: none;
}
.cv-sidebar-search input {
    width: 100%;
    height: 40px;
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 10px;
    padding: 0 12px 0 36px;
    font-size: 0.8125rem;
    color: #f1f5f9;
    background: rgba(255,255,255,0.04);
    outline: none;
    transition: all 0.2s;
}
.cv-sidebar-search input::placeholder { color: #475569; }
.cv-sidebar-search input:focus {
    border-color: rgba(139,92,246,0.5);
    background: rgba(255,255,255,0.07);
    box-shadow: 0 0 0 3px rgba(139,92,246,0.15);
}

/* Radio buttons */
.cv-sidebar-radios {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.cv-radio {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 0.8125rem;
    color: #cbd5e1;
    cursor: pointer;
    padding: 6px 0;
    transition: color 0.15s;
}
.cv-radio:hover { color: #f1f5f9; }
.cv-radio input { display: none; }
.cv-radio-mark {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid #475569;
    flex-shrink: 0;
    position: relative;
    transition: all 0.2s;
}
.cv-radio input:checked + .cv-radio-mark {
    border-color: #8b5cf6;
    background: #8b5cf6;
    box-shadow: inset 0 0 0 3px #0f172a;
}
.cv-radio input:checked + .cv-radio-mark.cv-radio-green {
    border-color: #10b981;
    background: #10b981;
}
.cv-radio input:checked + .cv-radio-mark.cv-radio-amber {
    border-color: #f59e0b;
    background: #f59e0b;
}

/* Select in sidebar */
.cv-sidebar-select {
    width: 100%;
    height: 40px;
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 10px;
    padding: 0 32px 0 12px;
    font-size: 0.8125rem;
    color: #cbd5e1;
    background: rgba(255,255,255,0.04);
    outline: none;
    cursor: pointer;
    -webkit-appearance: none;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' xmlns='http://www.w3.org/2000/svg'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    transition: all 0.2s;
}
.cv-sidebar-select:focus {
    border-color: rgba(139,92,246,0.5);
    box-shadow: 0 0 0 3px rgba(139,92,246,0.15);
}
.cv-sidebar-select option { background: #1e293b; color: #cbd5e1; }

/* Sidebar actions */
.cv-sidebar-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 28px;
    padding-top: 24px;
    border-top: 1px solid rgba(255,255,255,0.06);
}
.cv-sidebar-reset {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    height: 40px;
    border-radius: 10px;
    font-size: 0.8125rem;
    font-weight: 500;
    color: #94a3b8;
    text-decoration: none;
    border: 1px solid rgba(255,255,255,0.08);
    transition: all 0.15s;
}
.cv-sidebar-reset:hover { background: rgba(255,255,255,0.04); color: #e2e8f0; border-color: rgba(255,255,255,0.15); }
.cv-sidebar-apply {
    height: 42px;
    border-radius: 10px;
    font-size: 0.8125rem;
    font-weight: 700;
    color: #fff;
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: 0 2px 8px rgba(139,92,246,0.3);
}
.cv-sidebar-apply:hover {
    background: linear-gradient(135deg, #7c3aed, #6d28d9);
    box-shadow: 0 4px 16px rgba(139,92,246,0.4);
    transform: translateY(-1px);
}

/* Sidebar overlay (mobile) */
.cv-sidebar-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    backdrop-filter: blur(4px);
    z-index: 35;
}
.cv-sidebar-overlay.is-open { display: block; }

/* ═══════════════════════════════════════════════
   MAIN AREA
   ═══════════════════════════════════════════════ */
.cv-main {
    flex: 1;
    padding: 28px 32px 48px;
    max-width: 100%;
    overflow-x: hidden;
}

/* Top bar */
.cv-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
}
.cv-topbar-left {
    display: flex;
    align-items: center;
    gap: 16px;
}
.cv-filter-toggle {
    display: none;
    align-items: center;
    gap: 6px;
    height: 38px;
    padding: 0 14px;
    border-radius: 9px;
    font-size: 0.8125rem;
    font-weight: 600;
    color: #334155;
    background: #fff;
    border: 1px solid #e2e8f0;
    cursor: pointer;
    transition: all 0.15s;
}
.cv-filter-toggle:hover { background: #f8fafc; border-color: #cbd5e1; }
.cv-filter-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 18px;
    height: 18px;
    padding: 0 5px;
    border-radius: 999px;
    font-size: 0.6875rem;
    font-weight: 700;
    color: #fff;
    background: #8b5cf6;
}

.cv-breadcrumb {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.8125rem;
}
.cv-breadcrumb a {
    color: #94a3b8;
    text-decoration: none;
    transition: color 0.15s;
}
.cv-breadcrumb a:hover { color: #64748b; }
.cv-breadcrumb span {
    color: #334155;
    font-weight: 600;
}
.cv-breadcrumb svg { color: #cbd5e1; }

/* Create button */
.cv-btn-create {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    height: 42px;
    padding: 0 22px;
    border-radius: 11px;
    font-size: 0.8125rem;
    font-weight: 700;
    color: #fff;
    background: linear-gradient(135deg, #0f172a, #1e293b);
    text-decoration: none;
    transition: all 0.2s;
    box-shadow: 0 2px 8px rgba(15,23,42,0.2);
    white-space: nowrap;
}
.cv-btn-create:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(15,23,42,0.3);
    color: #fff;
}
.cv-btn-create-lg {
    height: 48px;
    padding: 0 28px;
    font-size: 0.875rem;
    border-radius: 12px;
}

/* Page header */
.cv-page-header {
    margin-bottom: 28px;
}
.cv-page-title {
    font-size: 1.75rem;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -0.03em;
    margin: 0;
    line-height: 1.2;
}
.cv-page-subtitle {
    margin: 6px 0 0;
    font-size: 0.875rem;
    color: #94a3b8;
    line-height: 1.5;
}
.cv-page-subtitle strong { color: #64748b; font-weight: 600; }

/* ═══════════════════════════════════════════════
   GRID
   ═══════════════════════════════════════════════ */
.cv-grid {
    display: grid;
    grid-template-columns: repeat(1, 1fr);
    gap: 20px;
}
@media (min-width: 640px) {
    .cv-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (min-width: 1024px) {
    .cv-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (min-width: 1400px) {
    .cv-grid { grid-template-columns: repeat(3, 1fr); }
}

/* ═══════════════════════════════════════════════
   CARD
   ═══════════════════════════════════════════════ */
.cv-card {
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid #e2e8f0;
    border-top: 3px solid var(--accent);
    transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
    display: flex;
    flex-direction: column;
}
.cv-card:hover {
    border-color: var(--accent);
    box-shadow: 0 12px 40px -10px var(--accent-glow), 0 4px 16px -4px rgba(0,0,0,0.06);
    transform: translateY(-4px);
    border-top-color: var(--accent);
}

/* Thumbnail */
.cv-card-thumb {
    position: relative;
    height: 180px;
    background: var(--accent-bg);
    overflow: hidden;
}
.cv-card-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.4s cubic-bezier(0.4,0,0.2,1);
}
.cv-card:hover .cv-card-thumb img {
    transform: scale(1.04);
}
.cv-card-thumb.cv-thumb-fallback {
    background: linear-gradient(135deg, var(--accent-bg), #f8fafc);
}
.cv-thumb-placeholder {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
}
.cv-thumb-fallback .cv-thumb-placeholder { display: flex; }
.cv-thumb-fallback img { display: none; }

/* Gradient overlay on thumbnail */
.cv-card-thumb::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 60px;
    background: linear-gradient(to top, rgba(255,255,255,0.9), transparent);
    pointer-events: none;
}

/* Status badge on thumbnail */
.cv-card-badge-wrap {
    position: absolute;
    top: 12px;
    right: 12px;
    z-index: 2;
}
.cv-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 12px;
    border-radius: 999px;
    font-size: 0.6875rem;
    font-weight: 700;
    letter-spacing: 0.02em;
    backdrop-filter: blur(8px);
}
.cv-status-published {
    background: rgba(16,185,129,0.12);
    color: #059669;
    border: 1px solid rgba(16,185,129,0.25);
}
.cv-status-draft {
    background: rgba(245,158,11,0.12);
    color: #d97706;
    border: 1px solid rgba(245,158,11,0.25);
}

/* Hover overlay with quick actions */
.cv-card-hover-overlay {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    background: rgba(15,23,42,0.5);
    backdrop-filter: blur(2px);
    opacity: 0;
    transition: opacity 0.25s;
    z-index: 3;
}
.cv-card:hover .cv-card-hover-overlay { opacity: 1; }
.cv-quick-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 9px 18px;
    border-radius: 9px;
    font-size: 0.8125rem;
    font-weight: 600;
    color: #fff;
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.25);
    text-decoration: none;
    transition: all 0.15s;
    transform: translateY(6px);
}
.cv-card:hover .cv-quick-btn { transform: translateY(0); }
.cv-quick-btn:nth-child(2) { transition-delay: 0.05s; }
.cv-quick-btn:hover { background: rgba(255,255,255,0.25); color: #fff; }

/* Card info */
.cv-card-info {
    padding: 16px 20px 14px;
    flex: 1;
}
.cv-card-tpl {
    display: inline-block;
    font-size: 0.625rem;
    font-weight: 700;
    color: var(--accent);
    text-transform: uppercase;
    letter-spacing: 0.1em;
    background: var(--accent-bg);
    padding: 3px 8px;
    border-radius: 5px;
    margin-bottom: 10px;
}
.cv-card-name {
    font-size: 1rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 6px;
    line-height: 1.3;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}
.cv-card-desc {
    font-size: 0.8125rem;
    color: #94a3b8;
    line-height: 1.55;
    margin: 0 0 14px;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

/* Card footer */
.cv-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.cv-card-time {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 0.75rem;
    color: #cbd5e1;
}
.cv-card-time svg { color: #e2e8f0; }

/* Bottom action bar */
.cv-card-bar {
    display: flex;
    gap: 0;
    border-top: 1px solid #f1f5f9;
}
.cv-bar-btn {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 12px 0;
    font-size: 0.8125rem;
    font-weight: 600;
    text-decoration: none;
    text-align: center;
    transition: all 0.15s;
}
.cv-bar-primary {
    color: #fff;
    background: #0f172a;
}
.cv-bar-primary:hover { background: #1e293b; color: #fff; }
.cv-bar-outline {
    color: #64748b;
    background: transparent;
    border-left: 1px solid #f1f5f9;
}
.cv-bar-outline:hover { background: #f8fafc; color: #334155; }

/* ═══════════════════════════════════════════════
   MORE MENU (Dropdown)
   ═══════════════════════════════════════════════ */
.cv-more { position: relative; }
.cv-more-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    border-radius: 8px;
    color: #94a3b8;
    background: transparent;
    border: none;
    cursor: pointer;
    transition: all 0.15s;
}
.cv-more-btn:hover { background: #f1f5f9; color: #475569; }
.cv-more-menu {
    display: none;
    position: absolute;
    bottom: calc(100% + 6px);
    right: -8px;
    min-width: 200px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 12px 48px -8px rgba(0,0,0,0.15), 0 0 0 1px rgba(0,0,0,0.02);
    padding: 6px;
    z-index: 50;
    animation: cvMenuIn 0.15s ease;
}
@keyframes cvMenuIn {
    from { opacity: 0; transform: translateY(4px); }
    to { opacity: 1; transform: translateY(0); }
}
.cv-more-menu.is-open { display: block; }
.cv-more-item {
    display: flex;
    align-items: center;
    gap: 9px;
    width: 100%;
    padding: 9px 12px;
    border-radius: 8px;
    font-size: 0.8125rem;
    font-weight: 500;
    color: #334155;
    background: transparent;
    border: none;
    cursor: pointer;
    transition: all 0.1s;
    text-decoration: none;
    text-align: left;
}
.cv-more-item:hover { background: #f8fafc; color: #0f172a; }
.cv-more-danger { color: #dc2626; }
.cv-more-danger:hover { background: #fef2f2; color: #b91c1c; }
.cv-more-divider { height: 1px; background: #f1f5f9; margin: 4px 8px; }
.cv-more-form { display: block; }

/* ═══════════════════════════════════════════════
   EMPTY STATE
   ═══════════════════════════════════════════════ */
.cv-empty {
    margin-top: 48px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 56px 32px 48px;
    text-align: center;
}
.cv-empty-visual {
    position: relative;
    width: 120px;
    height: 100px;
    margin: 0 auto 28px;
}
.cv-empty-doc {
    width: 80px;
    height: 80px;
    border-radius: 16px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    position: absolute;
    top: 10px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 2;
}
.cv-empty-circle {
    position: absolute;
    border-radius: 50%;
    border: 2px dashed #e2e8f0;
}
.cv-empty-circle-1 {
    width: 100px;
    height: 100px;
    top: 0;
    left: 10px;
    animation: cvFloat 6s ease-in-out infinite;
}
.cv-empty-circle-2 {
    width: 60px;
    height: 60px;
    bottom: 0;
    right: 0;
    animation: cvFloat 6s ease-in-out infinite reverse;
    animation-delay: -3s;
}
@keyframes cvFloat {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-8px); }
}
.cv-empty-title {
    font-size: 1.125rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 8px;
}
.cv-empty-text {
    font-size: 0.875rem;
    color: #94a3b8;
    margin: 0 auto 28px;
    max-width: 340px;
    line-height: 1.65;
}

/* ═══════════════════════════════════════════════
   RESPONSIVE
   ═══════════════════════════════════════════════ */
@media (max-width: 1023px) {
    .cv-sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        z-index: 40;
        transform: translateX(-100%);
    }
    .cv-sidebar.is-open { transform: translateX(0); }
    .cv-sidebar-close { display: flex; }
    .cv-filter-toggle { display: inline-flex; }
    .cv-main { padding: 20px 16px 40px; }
}

@media (max-width: 639px) {
    .cv-page-title { font-size: 1.375rem; }
    .cv-card-thumb { height: 140px; }
    .cv-grid { gap: 14px; }
    .cv-card-info { padding: 14px 16px 12px; }
}
</style>

<script>
function toggleSidebar() {
    document.getElementById('cvSidebar').classList.toggle('is-open');
    document.getElementById('cvSidebarOverlay').classList.toggle('is-open');
    document.body.style.overflow = document.getElementById('cvSidebar').classList.contains('is-open') ? 'hidden' : '';
}

function toggleMore(id) {
    var menu = document.getElementById('menu-' + id);
    var wasOpen = menu.classList.contains('is-open');
    document.querySelectorAll('.cv-more-menu').forEach(function(m) { m.classList.remove('is-open'); });
    if (!wasOpen) menu.classList.add('is-open');
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('.cv-more')) {
        document.querySelectorAll('.cv-more-menu').forEach(function(m) { m.classList.remove('is-open'); });
    }

    var copyBtn = e.target.closest('button[data-copy]');
    if (copyBtn) {
        var url = copyBtn.getAttribute('data-copy');
        if (url) {
            navigator.clipboard.writeText(url).then(function() {
                var prev = copyBtn.innerHTML;
                copyBtn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Tersalin!';
                copyBtn.style.color = '#059669';
                setTimeout(function() { copyBtn.innerHTML = prev; copyBtn.style.color = ''; }, 1200);
                copyBtn.closest('.cv-more-menu').classList.remove('is-open');
            }).catch(function() { window.prompt('Copy this link:', url); });
        }
    }
});
</script>
@endsection