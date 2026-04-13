@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6" style="padding-top:2.5rem; padding-bottom:4rem;">

    {{-- ==================== HEADER ==================== --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2" style="margin-bottom:4px;">
                <a href="{{ route('home') }}" style="font-size:0.75rem; color:#94a3b8; text-decoration:none;">Dashboard</a>
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                <span style="font-size:0.75rem; color:#64748b; font-weight:600;">My CVs</span>
            </div>
            <h1 style="font-size:1.625rem; font-weight:800; color:#0f172a; letter-spacing:-0.025em; margin:0; line-height:1.3;">
                My CVs
            </h1>
            <p style="margin-top:4px; font-size:0.8125rem; color:#94a3b8; line-height:1.5;">
                {{ $cvs->count() }} CV tersimpan
            </p>
        </div>
        <a href="{{ route('cv-builder.templates') }}"
           class="cv-btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Buat CV Baru
        </a>
    </div>

    {{-- ==================== FILTER BAR ==================== --}}
    <form method="GET" action="{{ route('cvs.index') }}" class="cv-filter-bar">
        <div class="flex flex-col sm:flex-row" style="gap:10px;">
            <div class="cv-search-wrap" style="flex:1;">
                <svg class="cv-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input id="q" name="q" value="{{ $search ?? request('q') }}" placeholder="Cari CV…" class="cv-search-input">
            </div>
            <div class="flex" style="gap:10px;">
                <select id="status" name="status" class="cv-select">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                </select>
                {{-- Template picker with search (type to filter) --}}
                <div style="position:relative;">
                    <input
                        id="template_search"
                        class="cv-select"
                        style="min-width:190px; padding-right:28px;"
                        list="template_list"
                        placeholder="Semua Template"
                        autocomplete="off"
                        value="{{ request('template') }}"
                    />
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position:absolute; right:10px; top:50%; transform:translateY(-50%); pointer-events:none;">
                        <polyline points="6 9 12 15 18 9" />
                    </svg>

                    <datalist id="template_list">
                        <option value="">Semua Template</option>
                        @foreach(($templates ?? collect()) as $tpl)
                            <option value="{{ $tpl->slug }}">{{ $tpl->name }}</option>
                        @endforeach
                    </datalist>

                    {{-- actual submitted value --}}
                    <input type="hidden" id="template" name="template" value="{{ request('template') }}" />
                </div>
            </div>
            <div class="flex" style="gap:8px;">
                @if(request('q') || request('status') || request('template'))
                    <a href="{{ route('cvs.index') }}" class="cv-btn-ghost">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        Reset
                    </a>
                @endif
                <button type="submit" class="cv-btn-dark">Terapkan</button>
            </div>
        </div>
    </form>

    <script>
        // Keep hidden template field in sync with the searchable input
        (() => {
            const input = document.getElementById('template_search');
            const hidden = document.getElementById('template');
            if (!input || !hidden) return;

            const sync = () => {
                hidden.value = (input.value || '').trim();
            };

            input.addEventListener('input', sync);
            input.addEventListener('change', sync);
            sync();
        })();
    </script>

    {{-- ==================== EMPTY STATE ==================== --}}
    @if($cvs->isEmpty())
        <div class="cv-empty-state">
            <div class="cv-empty-icon">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <path d="M14 2v6h6"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                    <polyline points="10 9 9 9 8 9"/>
                </svg>
            </div>
            <h3 style="font-size:1.0625rem; font-weight:700; color:#0f172a; margin:0 0 6px;">Belum ada CV</h3>
            <p style="font-size:0.8125rem; color:#94a3b8; margin:0; max-width:300px; margin-left:auto; margin-right:auto; line-height:1.6;">
                Mulai buat CV pertamamu dengan memilih template yang sesuai kebutuhan.
            </p>
            <a href="{{ route('cv-builder.templates') }}" class="cv-btn-primary" style="margin-top:1.25rem;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Buat CV Pertama
            </a>
        </div>

    {{-- ==================== CV CARDS GRID ==================== --}}
    @else
        <div class="cv-grid">

            @foreach($cvs as $cv)
                <div class="cv-card">
                    {{-- Top color accent --}}
                    <div class="cv-card-accent" style="background:{{ $cv->status === 'published' ? 'linear-gradient(135deg,#10b981,#34d399)' : 'linear-gradient(135deg,#f59e0b,#fbbf24)' }};"></div>

                    {{-- Card header area --}}
                    <div class="cv-card-header">
                        <div class="cv-card-dots">
                            <span></span><span></span><span></span>
                        </div>
                        <span class="cv-badge {{ $cv->status === 'published' ? 'cv-badge-green' : 'cv-badge-amber' }}">
                            {{ $cv->status === 'published' ? '● Published' : '● Draft' }}
                        </span>
                    </div>

                    {{-- Card body --}}
                    <div class="cv-card-body">
                        <span class="cv-card-template">{{ $cv->template_slug }}</span>
                        <h3 class="cv-card-title">{{ $cv->title }}</h3>
                        @if($cv->summary)
                            <p class="cv-card-summary">{{ \Illuminate\Support\Str::limit($cv->summary, 90) }}</p>
                        @endif
                        <div class="cv-card-meta">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            {{ $cv->updated_at?->diffForHumans() }}
                        </div>
                    </div>

                    {{-- Card actions --}}
                    <div class="cv-card-actions">
                        <a href="{{ route('cvs.edit', $cv) }}" class="cv-action-primary">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            Edit
                        </a>
                        <a href="{{ route('cvs.render', $cv) }}" target="_blank" rel="noopener" class="cv-action-secondary">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                            Preview
                        </a>

                        {{-- Dropdown menu --}}
                        <div class="cv-dropdown" id="dropdown-{{ $cv->id }}">
                            <button type="button" class="cv-action-ghost" onclick="toggleDropdown({{ $cv->id }})">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg>
                            </button>
                            <div class="cv-dropdown-menu" id="menu-{{ $cv->id }}">
                                <a href="{{ route('cvs.show', $cv) }}" class="cv-dropdown-item">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    Lihat Detail
                                </a>
                                @if($cv->status === 'published')
                                    <button type="button" class="cv-dropdown-item" data-copy="{{ route('cvs.public', $cv) }}">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                        Salin Link Publik
                                    </button>
                                @endif
                                <form method="POST" action="{{ route('cvs.toggle-publish', $cv) }}" class="cv-dropdown-item-form">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="cv-dropdown-item" style="width:100%; text-align:left;">
                                        @if($cv->status === 'published')
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                            Tarik sebagai Draft
                                        @else
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v4"/><path d="M10 14 21 3"/><path d="M21 10v11a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                                            Publikasikan
                                        @endif
                                    </button>
                                </form>
                                <div class="cv-dropdown-divider"></div>
                                <form method="POST" action="{{ route('cvs.destroy', $cv) }}" class="cv-dropdown-item-form" onsubmit="return confirm('Yakin ingin menghapus CV ini? Aksi ini tidak bisa dibatalkan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="cv-dropdown-item cv-dropdown-item-danger" style="width:100%; text-align:left;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                        Hapus CV
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>
    @endif

</div>

<style>
/* ── Primary Button ── */
.cv-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #0f172a;
    color: #fff;
    padding: 10px 20px;
    border-radius: 10px;
    font-size: 0.8125rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.15s ease;
    white-space: nowrap;
}
.cv-btn-primary:hover {
    background: #1e293b;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(15,23,42,0.25);
    color: #fff;
}

/* ── Dark Button ── */
.cv-btn-dark {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #0f172a;
    color: #fff;
    padding: 9px 16px;
    border-radius: 9px;
    font-size: 0.8125rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.15s ease;
    white-space: nowrap;
}
.cv-btn-dark:hover { background: #1e293b; }

/* ── Ghost Button ── */
.cv-btn-ghost {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: transparent;
    color: #64748b;
    padding: 9px 14px;
    border-radius: 9px;
    font-size: 0.8125rem;
    font-weight: 500;
    text-decoration: none;
    border: 1px solid #e2e8f0;
    transition: all 0.15s ease;
    white-space: nowrap;
}
.cv-btn-ghost:hover { background: #f8fafc; border-color: #cbd5e1; color: #334155; }

/* ── Filter Bar ── */
.cv-filter-bar {
    margin-top: 1.5rem;
    background: #fff;
    border: 1px solid #f1f5f9;
    border-radius: 12px;
    padding: 14px 16px;
}

/* ── Search Input ── */
.cv-search-wrap {
    position: relative;
    display: flex;
    align-items: center;
}
.cv-search-icon {
    position: absolute;
    left: 12px;
    color: #cbd5e1;
    pointer-events: none;
}
.cv-search-input {
    width: 100%;
    height: 40px;
    border: 1px solid #e2e8f0;
    border-radius: 9px;
    padding: 0 12px 0 38px;
    font-size: 0.8125rem;
    color: #0f172a;
    background: #fff;
    outline: none;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}
.cv-search-input::placeholder { color: #cbd5e1; }
.cv-search-input:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
}

/* ── Select ── */
.cv-select {
    height: 40px;
    min-width: 140px;
    border: 1px solid #e2e8f0;
    border-radius: 9px;
    padding: 0 10px;
    font-size: 0.8125rem;
    color: #334155;
    background: #fff;
    outline: none;
    cursor: pointer;
    transition: border-color 0.15s ease;
    -webkit-appearance: none;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' xmlns='http://www.w3.org/2000/svg'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 10px center;
    padding-right: 28px;
}
.cv-select:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }

/* ── Grid ── */
.cv-grid {
    display: grid;
    grid-template-columns: repeat(1, 1fr);
    gap: 16px;
    margin-top: 1.5rem;
}
@media (min-width: 768px) {
    .cv-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (min-width: 1280px) {
    .cv-grid { grid-template-columns: repeat(3, 1fr); }
}

/* ── Card ── */
.cv-card {
    background: #fff;
    border: 1px solid #f1f5f9;
    border-radius: 14px;
    overflow: hidden;
    transition: all 0.2s cubic-bezier(0.4,0,0.2,1);
    display: flex;
    flex-direction: column;
}
.cv-card:hover {
    border-color: #e2e8f0;
    box-shadow: 0 8px 30px -8px rgba(0,0,0,0.08);
    transform: translateY(-2px);
}

/* ── Card Accent ── */
.cv-card-accent {
    height: 4px;
    width: 100%;
    flex-shrink: 0;
}

/* ── Card Header ── */
.cv-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 18px 0;
}
.cv-card-dots {
    display: flex;
    gap: 5px;
}
.cv-card-dots span {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #f1f5f9;
}

/* ── Badge ── */
.cv-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 0.6875rem;
    font-weight: 700;
    letter-spacing: 0.02em;
}
.cv-badge-green {
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
}
.cv-badge-amber {
    background: #fffbeb;
    color: #d97706;
    border: 1px solid #fde68a;
}

/* ── Card Body ── */
.cv-card-body {
    padding: 14px 18px 0;
    flex: 1;
}
.cv-card-template {
    display: inline-block;
    font-size: 0.6875rem;
    font-weight: 600;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin-bottom: 6px;
}
.cv-card-title {
    font-size: 0.9375rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 6px;
    line-height: 1.35;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}
.cv-card-summary {
    font-size: 0.8125rem;
    color: #94a3b8;
    line-height: 1.55;
    margin: 0 0 12px;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}
.cv-card-meta {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 0.75rem;
    color: #cbd5e1;
}

/* ── Card Actions ── */
.cv-card-actions {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 14px 18px;
    border-top: 1px solid #f8fafc;
    margin-top: auto;
}
.cv-action-primary {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 8px 0;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 600;
    color: #fff;
    background: #0f172a;
    text-decoration: none;
    transition: all 0.15s ease;
}
.cv-action-primary:hover { background: #1e293b; color: #fff; }

.cv-action-secondary {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 8px 0;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 600;
    color: #4f46e5;
    background: #eef2ff;
    text-decoration: none;
    transition: all 0.15s ease;
}
.cv-action-secondary:hover { background: #e0e7ff; color: #3730a3; }

.cv-action-ghost {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 34px;
    border-radius: 8px;
    color: #94a3b8;
    background: #f8fafc;
    border: none;
    cursor: pointer;
    transition: all 0.15s ease;
    flex-shrink: 0;
}
.cv-action-ghost:hover { background: #f1f5f9; color: #475569; }

/* ── Dropdown ── */
.cv-dropdown {
    position: relative;
}
.cv-dropdown-menu {
    display: none;
    position: absolute;
    bottom: calc(100% + 6px);
    right: 0;
    min-width: 190px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    box-shadow: 0 10px 40px -8px rgba(0,0,0,0.12), 0 0 0 1px rgba(0,0,0,0.02);
    padding: 5px;
    z-index: 50;
}
.cv-dropdown-menu.is-open { display: block; }
.cv-dropdown-item {
    display: flex;
    align-items: center;
    gap: 8px;
    width: 100%;
    padding: 8px 10px;
    border-radius: 7px;
    font-size: 0.8125rem;
    font-weight: 500;
    color: #334155;
    background: transparent;
    border: none;
    cursor: pointer;
    transition: all 0.1s ease;
    text-decoration: none;
}
.cv-dropdown-item:hover { background: #f8fafc; color: #0f172a; }
.cv-dropdown-item-danger { color: #dc2626; }
.cv-dropdown-item-danger:hover { background: #fef2f2; color: #b91c1c; }
.cv-dropdown-divider {
    height: 1px;
    background: #f1f5f9;
    margin: 4px 6px;
}
.cv-dropdown-item-form {
    display: block;
}

/* ── Empty State ── */
.cv-empty-state {
    margin-top: 3rem;
    background: #fff;
    border: 1px solid #f1f5f9;
    border-radius: 16px;
    padding: 3.5rem 2rem;
    text-align: center;
}
.cv-empty-icon {
    width: 72px;
    height: 72px;
    margin: 0 auto 1.25rem;
    border-radius: 18px;
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>

<script>
// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.cv-dropdown')) {
        document.querySelectorAll('.cv-dropdown-menu').forEach(function(m) {
            m.classList.remove('is-open');
        });
    }

    // Copy to clipboard
    var copyBtn = e.target.closest('button[data-copy]');
    if (copyBtn) {
        var url = copyBtn.getAttribute('data-copy');
        if (url) {
            navigator.clipboard.writeText(url).then(function() {
                var prev = copyBtn.innerHTML;
                copyBtn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Tersalin!';
                copyBtn.style.color = '#059669';
                setTimeout(function() {
                    copyBtn.innerHTML = prev;
                    copyBtn.style.color = '';
                }, 1200);
                // close dropdown
                copyBtn.closest('.cv-dropdown-menu').classList.remove('is-open');
            }).catch(function() {
                window.prompt('Copy this link:', url);
            });
        }
    }
});

function toggleDropdown(id) {
    var menu = document.getElementById('menu-' + id);
    var wasOpen = menu.classList.contains('is-open');
    // close all
    document.querySelectorAll('.cv-dropdown-menu').forEach(function(m) {
        m.classList.remove('is-open');
    });
    if (!wasOpen) {
        menu.classList.add('is-open');
    }
}
</script>
@endsection