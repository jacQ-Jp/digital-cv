@extends('layouts.app')

@section('content')
<style>
    :root {
        /* Color Palette */
        --bg-body: #f1f5f9;
        --bg-sidebar: #ffffff;
        --bg-card: #ffffff;
        
        --text-main: #1e293b;
        --text-muted: #64748b;
        --text-light: #94a3b8;
        
        --primary: #2563eb;
        --primary-hover: #1d4ed8;
        --danger: #ef4444;
        --success: #10b981;
        
        --border-light: #e2e8f0;
        --border-card: #cbd5e1;
        
        --shadow-card: 0 2px 4px rgba(0,0,0,0.05);
        --shadow-hover: 0 8px 16px rgba(0,0,0,0.08);
        
        --radius-md: 12px;
        --radius-lg: 16px;
    }

    body {
        background-color: var(--bg-body);
        color: var(--text-main);
        font-family: 'Inter', sans-serif;
        padding-top: 1rem;
    }

    .cv-history-wrap {
        max-width: 1400px; /* Lebar container maksimal */
        margin: 0 auto;
        padding: 0 1rem 2rem;
    }

    /* --- LAYOUT UTAMA --- */
    .history-layout {
        display: grid;
        grid-template-columns: 260px 1fr; /* Sidebar fixed 260px */
        gap: 1.5rem;
        align-items: start;
    }

    /* --- SIDEBAR (Gaya Resume.io) --- */
    .history-sidebar {
        background: var(--bg-sidebar);
        border: 1px solid var(--border-light);
        border-radius: var(--radius-lg);
        /* Sticky Full Height */
        position: sticky;
        top: 1rem;
        height: calc(100vh - 2rem);
        display: flex;
        flex-direction: column;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .sidebar-header {
        padding: 1.25rem;
        border-bottom: 1px solid var(--border-light);
    }

    .sidebar-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-main);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .sidebar-content {
        flex: 1;
        overflow-y: auto; /* Scroll jika konten panjang */
        padding: 1.25rem;
    }

    .sidebar-footer {
        padding: 1rem;
        border-top: 1px solid var(--border-light);
        background: #f8fafc;
    }

    /* Filter Styles */
    .filter-item { margin-bottom: 1.25rem; }
    .filter-label {
        display: block;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        color: var(--text-muted);
        margin-bottom: 0.5rem;
        letter-spacing: 0.05em;
    }
    .form-control-sm {
        width: 100%;
        padding: 0.5rem 0.75rem;
        font-size: 0.9rem;
        border: 1px solid var(--border-card);
        border-radius: 8px;
        background: #fff;
        color: var(--text-main);
        transition: all 0.2s;
    }
    .form-control-sm:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1);
    }

    /* --- CONTENT AREA --- */
    .content-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .header-actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .page-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }

    /* --- GRID & CARD --- */
    .history-grid {
        display: grid;
        /* Lebar kolom diperkecil menjadi 220px */
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 1.25rem;
    }

    .history-card {
        background: var(--bg-card);
        border: 1px solid var(--border-light);
        border-radius: var(--radius-md);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: all 0.2s ease;
        box-shadow: var(--shadow-card);
    }

    .history-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-hover);
        border-color: var(--primary);
    }

    /* Thumbnail Kecil & Rapi */
    .thumb-container {
        aspect-ratio: 210/297; /* Rasio A4 */
        background: #fff;
        border-bottom: 1px solid var(--border-light);
        position: relative;
        padding: 10px; /* Padding di dalam frame agar thumbnail tidak mentok */
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .thumb-iframe {
        width: 100%;
        height: 100%;
        border: 1px solid #eee;
        background: #fff;
        pointer-events: none;
    }

    /* Card Body Compact */
    .card-body {
        padding: 0.75rem;
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
    }

    .card-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--text-main);
        line-height: 1.3;
        margin: 0;
        /* Truncate 2 baris */
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .status-badge {
        font-size: 0.65rem;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 4px;
        text-transform: uppercase;
        flex-shrink: 0;
        letter-spacing: 0.5px;
    }
    .status-draft { background: #fee2e2; color: #b91c1c; }
    .status-published { background: #dcfce7; color: #15803d; }

    .card-meta {
        font-size: 0.75rem;
        color: var(--text-muted);
        margin: 0;
    }

    /* --- ACTION BUTTONS (Compact) --- */
    .card-actions {
        margin-top: auto;
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
        padding-top: 0.5rem;
        border-top: 1px dashed var(--border-light);
    }

    /* Style tombol kecil (btn-xs manual) */
    .btn-xs {
        font-size: 0.75rem;
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        font-weight: 600;
        text-align: center;
        border: 1px solid transparent;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-xs.btn-primary {
        background-color: var(--primary);
        color: #fff;
        flex: 1; /* Tombol edit melebar */
    }
    .btn-xs.btn-primary:hover { background-color: var(--primary-hover); }

    .btn-xs.btn-outline {
        background-color: transparent;
        border-color: var(--border-card);
        color: var(--text-muted);
    }
    .btn-xs.btn-outline:hover {
        background-color: #f8fafc;
        color: var(--text-main);
        border-color: var(--text-light);
    }

    .btn-xs.btn-danger {
        background-color: transparent;
        color: var(--danger);
        border: 1px solid rgba(239, 68, 68, 0.2);
    }
    .btn-xs.btn-danger:hover { background-color: #fef2f2; }

    .btn-xs.btn-logout {
        background: #fff;
        color: #334155;
        border: 1px solid #dbe2ea;
        padding: 0.6rem 1rem;
        font-size: 0.9rem;
    }

    .btn-xs.btn-logout:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #0f172a;
    }

    .copy-toast {
        position: fixed;
        right: 1rem;
        bottom: 1rem;
        background: #0f172a;
        color: #fff;
        border-radius: 10px;
        padding: 0.6rem 0.9rem;
        font-size: 0.85rem;
        font-weight: 600;
        opacity: 0;
        transform: translateY(12px);
        pointer-events: none;
        transition: opacity 0.2s ease, transform 0.2s ease;
        z-index: 1100;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.25);
    }

    .copy-toast.show {
        opacity: 1;
        transform: translateY(0);
    }

    /* Empty State */
    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 3rem;
        background: #fff;
        border-radius: var(--radius-lg);
        border: 2px dashed var(--border-card);
        color: var(--text-muted);
    }

    @media (max-width: 900px) {
        .history-layout { grid-template-columns: 1fr; }
        .history-sidebar {
            position: static;
            height: auto;
            margin-bottom: 1.5rem;
        }
        .sidebar-content, .sidebar-footer { display: block; }
    }
</style>

<div class="cv-history-wrap">
    <!-- Header -->
    <div class="content-header">
        <h1 class="page-title">History CV</h1>
        <div class="header-actions">
            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="btn-xs btn-logout">Logout</button>
            </form>

            <a href="{{ route('cv-builder.templates') }}" class="btn-xs btn-primary" style="padding: 0.6rem 1.2rem; font-size: 0.9rem; flex: initial;">
                + Buat Baru
            </a>
        </div>
    </div>

    <div class="history-layout">
        <!-- SIDEBAR (Panjang, Sticky, Resume.io Style) -->
        <aside class="history-sidebar">
            <div class="sidebar-header">
                <h2 class="sidebar-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
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
                <a href="{{ route('cvs.index') }}" class="btn-xs btn-outline" style="width: 100%; display: block;">Reset Filter</a>
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
                            // Perbaikan syntax penutup php
                            $templateLabel = $cv->template?->name ?: \Illuminate\Support\Str::title(str_replace('-', ' ', (string) $cv->template_slug));
                            // Ambil slug dan pastikan kosong string jika null
                            $cvSlug = $cv->template_slug ?? '';
                        @endphp
                        
                        <!-- Tambahkan Atribut Data untuk Filter Javascript -->
                        <!-- PENTING: Gunakan strtolower() pada data-template agar case-insensitive -->
                        <article class="history-card" 
                                  data-title="{{ strtolower($cv->title ?: 'Untitled CV') }}" 
                                  data-status="{{ $cv->status }}" 
                                  data-template="{{ strtolower($cvSlug) }}">
                            
                            <!-- Thumbnail (Kecil & Rapi) -->
                            <div class="thumb-container">
                                <iframe src="{{ route('cvs.thumb', $cv) }}" class="thumb-iframe" loading="lazy"></iframe>
                            </div>

                            <div class="card-body">
                                <div class="card-head">
                                    <h3 class="card-title">{{ $cv->title ?: 'Untitled CV' }}</h3>
                                    <span class="status-badge status-{{ $cv->status }}">{{ $cv->status }}</span>
                                </div>

                                <p class="card-meta"><strong>{{ $templateLabel }}</strong></p>
                                <p class="card-meta">{{ $cv->updated_at->format('d M Y') }}</p>

                                <!-- Tombol Rapi (Compact) -->
                                <div class="card-actions">
                                    <a href="{{ route('cvs.wizard', $cv) }}" class="btn-xs btn-primary">Edit</a>
                                    <a href="{{ route('cvs.render', $cv) }}" class="btn-xs btn-outline" target="_blank">Lihat</a>
                                    
                                    @if($cv->status === 'published')
                                        <button
                                            type="button"
                                            class="btn-xs btn-outline js-copy-link"
                                            data-copy-url="{{ $cv->public_uuid ? route('cvs.public', ['token' => $cv->public_uuid]) : '' }}"
                                        >
                                            Public Link
                                        </button>
                                    @endif

                                    <form
                                        method="POST"
                                        action="{{ route('cvs.toggle-publish', $cv) }}"
                                        style="display:inline;"
                                        class="js-toggle-publish-form"
                                    >
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn-xs btn-outline js-toggle-publish-btn">
                                            {{ $cv->status === 'published' ? 'Unpub' : 'Pub' }}
                                        </button>
                                    </form>
                                    
                                    <form method="POST" action="{{ route('cvs.destroy', $cv) }}" onsubmit="return confirm('Hapus?');" style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-xs btn-danger">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
                </div>
                
                <div id="noFilterResults" class="empty-state" style="display: none; margin-top: 2rem;">
                    <p>Tidak ada CV yang cocok dengan filter.</p>
                </div>
            @endif
        </section>
    </div>
</div>

<div id="copyToast" class="copy-toast" aria-live="polite">Link berhasil disalin</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('filter-q');
        const statusInput = document.getElementById('filter-status');
        const templateInput = document.getElementById('filter-template');
        const cards = Array.from(document.querySelectorAll('.history-card'));
        const noResults = document.getElementById('noFilterResults');
        const historyGrid = document.getElementById('historyGrid');
        const copyToast = document.getElementById('copyToast');
        let toastTimer = null;

        function showCopyToast(message) {
            if (!copyToast) return;
            copyToast.textContent = message;
            copyToast.classList.add('show');
            if (toastTimer) {
                clearTimeout(toastTimer);
            }
            toastTimer = setTimeout(() => {
                copyToast.classList.remove('show');
            }, 1500);
        }

        async function copyText(text) {
            if (!text) {
                return false;
            }

            if (navigator.clipboard && window.isSecureContext) {
                try {
                    await navigator.clipboard.writeText(text);
                    return true;
                } catch (error) {
                    // Fall through to execCommand fallback below.
                }
            }

            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.setAttribute('readonly', '');
            textarea.style.position = 'fixed';
            textarea.style.top = '-9999px';
            document.body.appendChild(textarea);
            textarea.focus();
            textarea.select();

            let copied = false;
            try {
                copied = document.execCommand('copy');
            } catch (error) {
                copied = false;
            }

            document.body.removeChild(textarea);
            return copied;
        }

        function ensurePublicLinkButton(card, publicUrl) {
            const actions = card.querySelector('.card-actions');
            const toggleForm = card.querySelector('.js-toggle-publish-form');
            if (!actions || !toggleForm) return;

            let copyBtn = card.querySelector('.js-copy-link');

            if (!copyBtn) {
                copyBtn = document.createElement('button');
                copyBtn.type = 'button';
                copyBtn.className = 'btn-xs btn-outline js-copy-link';
                copyBtn.textContent = 'Public Link';
                actions.insertBefore(copyBtn, toggleForm);
            }

            copyBtn.setAttribute('data-copy-url', publicUrl || '');
        }

        function removePublicLinkButton(card) {
            const copyBtn = card.querySelector('.js-copy-link');
            if (copyBtn) {
                copyBtn.remove();
            }
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
            if (toggleBtn) {
                toggleBtn.textContent = normalizedStatus === 'published' ? 'Unpub' : 'Pub';
            }

            if (normalizedStatus === 'published') {
                ensurePublicLinkButton(card, publicUrl);
            } else {
                removePublicLinkButton(card);
            }
        }

        async function requestTogglePublish(form) {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                body: formData,
            });

            const payload = await response.json().catch(() => ({}));

            if (!response.ok) {
                throw new Error(payload.message || 'Gagal mengubah status CV.');
            }

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

            if (visibleCount === 0) {
                noResults.classList.remove('hidden-card');
                noResults.style.display = 'block';
            } else {
                noResults.classList.add('hidden-card');
                noResults.style.display = 'none';
            }
        }

        searchInput.addEventListener('input', filterHistory);
        statusInput.addEventListener('change', filterHistory);
        templateInput.addEventListener('change', filterHistory);

        historyGrid?.addEventListener('click', async (event) => {
            const button = event.target.closest('.js-copy-link');
            if (!button) {
                return;
            }

            const url = button.getAttribute('data-copy-url') || '';
            const copied = await copyText(url);

            if (copied) {
                showCopyToast('Link berhasil disalin');
            } else {
                window.prompt('Copy this link:', url);
            }
        });

        historyGrid?.addEventListener('submit', async (event) => {
            const form = event.target.closest('.js-toggle-publish-form');
            if (!form) {
                return;
            }

            event.preventDefault();

            const card = form.closest('.history-card');
            if (!card) {
                return;
            }

            const toggleBtn = form.querySelector('.js-toggle-publish-btn');
            if (toggleBtn) {
                toggleBtn.disabled = true;
            }

            try {
                const payload = await requestTogglePublish(form);
                const nextStatus = (payload.status || '').toLowerCase() === 'published' ? 'published' : 'draft';
                const publicUrl = payload.public_url || '';

                updateCardPublishState(card, nextStatus, publicUrl);
                filterHistory();

                if (nextStatus === 'published') {
                    const copied = await copyText(publicUrl);
                    if (copied) {
                        showCopyToast('Link berhasil disalin');
                    } else if (publicUrl) {
                        window.prompt('Copy this link:', publicUrl);
                    } else {
                        showCopyToast('CV berhasil dipublikasikan');
                    }
                } else {
                    showCopyToast('Status CV diubah ke draft');
                }
            } catch (error) {
                showCopyToast(error.message || 'Gagal mengubah status CV');
            } finally {
                if (toggleBtn) {
                    toggleBtn.disabled = false;
                    const currentStatus = card.getAttribute('data-status') || 'draft';
                    toggleBtn.textContent = currentStatus === 'published' ? 'Unpub' : 'Pub';
                }
            }
        });

        // Initialize display from URL param if redirected from wizard
        const urlParams = new URLSearchParams(window.location.search);
        const wizardNotice = urlParams.get('wizard_notice');
        
        if (wizardNotice === 'published') {
            showCopyToast('CV berhasil dipublish!');
            window.history.replaceState({}, document.title, window.location.pathname);
        } else if (wizardNotice === 'draft_saved') {
            showCopyToast('Draft berhasil disimpan');
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    });
</script>
@endsection