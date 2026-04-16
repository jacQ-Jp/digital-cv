@extends('layouts.app')

@section('content')
<style>
    .tpl-shell {
        --tpl-bg: #f3f6fb;
        --tpl-panel: #ffffff;
        --tpl-border: #dbe4f0;
        --tpl-text: #0f172a;
        --tpl-muted: #64748b;
        --tpl-accent: #2563eb;
        --tpl-accent-soft: #dbeafe;
        background: radial-gradient(1200px 420px at 50% -180px, #dbeafe 0%, transparent 70%), var(--tpl-bg);
        min-height: calc(100vh - 88px);
        padding: 24px 12px 36px;
    }

    .tpl-page {
        max-width: 1320px;
        margin: 0 auto;
    }

    .tpl-headline {
        text-align: center;
        margin-bottom: 20px;
    }

    .tpl-headline h1 {
        margin: 0;
        font-size: clamp(1.55rem, 2.2vw, 2.1rem);
        font-weight: 800;
        color: var(--tpl-text);
        letter-spacing: -0.02em;
    }

    .tpl-headline p {
        margin: 8px 0 0;
        color: var(--tpl-muted);
        font-size: 1rem;
    }

    .tpl-workbench {
        display: grid;
        grid-template-columns: 270px 1fr;
        gap: 16px;
    }

    .tpl-panel {
        border: 1px solid var(--tpl-border);
        border-radius: 16px;
        background: var(--tpl-panel);
        box-shadow: 0 10px 25px -20px rgba(15, 23, 42, 0.45);
    }

    .tpl-filter-panel {
        padding: 16px;
        position: sticky;
        top: 1rem;
        align-self: start;
    }

    .tpl-filter-panel h2 {
        margin: 0 0 10px;
        font-size: 1.06rem;
        color: var(--tpl-text);
        font-weight: 750;
    }

    .tpl-search {
        max-width: none;
    }

    .tpl-filter-hint {
        margin-top: 10px;
        font-size: 0.82rem;
        color: var(--tpl-muted);
        line-height: 1.4;
    }

    .tpl-content-panel {
        padding: 16px;
    }

    .tpl-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }

    .tpl-card {
        border: 1px solid var(--tpl-border);
        border-radius: 14px;
        overflow: hidden;
        background: #fff;
        transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
        cursor: pointer;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
    }

    .tpl-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 30px -20px rgba(15, 23, 42, 0.42);
        border-color: #bfdbfe;
    }

    .tpl-card.is-selected {
        border-color: var(--tpl-accent);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.16), 0 16px 30px -20px rgba(15, 23, 42, 0.42);
    }

    .tpl-preview {
        aspect-ratio: 210 / 297;
        padding: 10px;
        background:
            radial-gradient(circle at 14% 12%, rgba(255, 255, 255, 0.85), rgba(255, 255, 255, 0) 42%),
            linear-gradient(160deg, #f8fafc 0%, #edf2ff 100%);
        border-bottom: 1px solid var(--tpl-border);
        overflow: hidden;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .tpl-paper {
        position: relative;
        width: 100%;
        height: 100%;
        border-radius: 2px;
        border: 1px solid #dbe4f0;
        background: #fff;
        box-shadow: 0 14px 22px -14px rgba(15, 23, 42, 0.45), 0 8px 16px -14px rgba(15, 23, 42, 0.3);
        overflow: hidden;
    }

    .tpl-paper::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(125deg, rgba(255, 255, 255, 0.45) 0%, rgba(255, 255, 255, 0) 34%);
        pointer-events: none;
        z-index: 1;
    }

    .tpl-preview-image {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
        object-position: top center;
        background: #fff;
    }

    .tpl-preview-empty {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        font-size: 0.82rem;
        font-weight: 700;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        background: linear-gradient(135deg, #f8fafc, #eef2ff);
    }

    .tpl-preview::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(15, 23, 42, 0) 70%, rgba(15, 23, 42, 0.05) 100%);
        pointer-events: none;
    }

    .tpl-meta {
        padding: 14px;
    }

    .tpl-name {
        font-size: 1rem;
        font-weight: 780;
        color: var(--tpl-text);
    }

    .tpl-desc {
        color: var(--tpl-muted);
        font-size: .88rem;
        line-height: 1.45;
    }

    .tpl-actions-bar {
        margin-top: 14px;
        padding-top: 14px;
        border-top: 1px solid var(--tpl-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .tpl-actions-bar .btn {
        border-radius: 10px;
        font-weight: 700;
    }

    .tpl-selected-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: .82rem;
        color: #1e3a8a;
        background: var(--tpl-accent-soft);
        border: 1px solid #bfdbfe;
        padding: 6px 10px;
        border-radius: 999px;
        font-weight: 700;
    }

    @media (max-width: 1199.98px) {
        .tpl-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 991.98px) {
        .tpl-workbench {
            grid-template-columns: 1fr;
        }

        .tpl-filter-panel {
            position: static;
        }
    }

    @media (max-width: 575.98px) {
        .tpl-shell {
            padding: 16px 8px 24px;
        }

        .tpl-grid {
            grid-template-columns: 1fr;
        }

        .tpl-meta {
            padding: 12px 12px 14px;
        }

        .tpl-actions-bar {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>

<div class="tpl-shell">
<div class="container tpl-page">
    <div class="tpl-headline">
        <h1>Pilih Template CV</h1>
        <p>Pilih desain yang paling cocok. Kamu bisa ganti template kapan saja nanti.</p>
    </div>

    @if($templates->isEmpty())
        <div class="alert alert-warning">No active templates available.</div>
    @else
        <form method="POST" action="{{ route('cv-builder.templates.save') }}">
            @csrf

            <div class="tpl-workbench">
                <aside class="tpl-panel tpl-filter-panel">
                    <h2>Filter Template</h2>
                    <div class="tpl-search">
                        <div class="input-group">
                            <span class="input-group-text" aria-hidden="true">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8" />
                                    <line x1="21" y1="21" x2="16.65" y2="16.65" />
                                </svg>
                            </span>
                            <input id="tplSearch" type="search" class="form-control" placeholder="Cari template…" autocomplete="off">
                        </div>
                    </div>
                    <div class="tpl-filter-hint">Ketik nama atau slug template untuk menyaring daftar.</div>
                    <div class="tpl-filter-hint">Template terpilih akan dipakai untuk preview, print, dan publikasi CV.</div>
                </aside>

                <section class="tpl-panel tpl-content-panel">
                    <div class="tpl-grid">
                        @foreach($templates as $tpl)
                            @php
                                $thumbnailPath = $tpl->thumbnail;
                                $thumbnailUrl = ($thumbnailPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($thumbnailPath))
                                    ? asset('storage/'.$thumbnailPath)
                                    : null;
                            @endphp
                            <label class="h-100 js-template-card tpl-card" data-name="{{ strtolower($tpl->name) }}" data-slug="{{ strtolower($tpl->slug) }}">
                                <div class="tpl-preview">
                                    <div class="tpl-paper">
                                        @if($thumbnailUrl)
                                            <img
                                                src="{{ $thumbnailUrl }}"
                                                alt="Thumbnail {{ $tpl->name }}"
                                                loading="lazy"
                                                class="tpl-preview-image"
                                            >
                                        @else
                                            <div class="tpl-preview-empty">No Thumbnail</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="tpl-meta">
                                    <div class="form-check d-flex align-items-start gap-2">
                                        <input class="form-check-input mt-1" type="radio" name="template_slug" value="{{ $tpl->slug }}" @checked(old('template_slug', $templates->firstWhere('is_default', true)?->slug) === $tpl->slug)>
                                        <span class="form-check-label fw-semibold tpl-name">{{ $tpl->name }}</span>
                                        @if($tpl->is_default)
                                            <span class="badge text-bg-primary ms-2">default</span>
                                        @endif
                                    </div>
                                    @if($tpl->description)
                                        <p class="tpl-desc mt-2 mb-0">{{ $tpl->description }}</p>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>

                    @error('template_slug')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror

                    <div class="tpl-actions-bar">
                        <span class="tpl-selected-chip" id="selectedTemplateChip">Template terpilih: -</span>
                        <div class="d-flex gap-2">
                            <a class="btn btn-outline-secondary" href="{{ route('cvs.index') }}">Pilih Nanti</a>
                            <button class="btn btn-primary" type="submit">Gunakan Template Ini</button>
                        </div>
                    </div>
                </section>
            </div>

            <script>
                (() => {
                    const input = document.getElementById('tplSearch');
                    if (!input) return;

                    const cards = Array.from(document.querySelectorAll('.js-template-card'));
                    const radios = Array.from(document.querySelectorAll('input[name="template_slug"]'));
                    const selectedChip = document.getElementById('selectedTemplateChip');

                    const syncSelected = () => {
                        cards.forEach((card) => {
                            const radio = card.querySelector('input[name="template_slug"]');
                            card.classList.toggle('is-selected', !!radio?.checked);

                            if (radio?.checked && selectedChip) {
                                const name = card.querySelector('.tpl-name')?.textContent?.trim() || radio.value;
                                selectedChip.textContent = `Template terpilih: ${name}`;
                            }
                        });
                    };

                    radios.forEach((radio) => radio.addEventListener('change', syncSelected));
                    syncSelected();

                    const apply = () => {
                        const q = (input.value || '').trim().toLowerCase();
                        cards.forEach((card) => {
                            const name = card.getAttribute('data-name') || '';
                            const slug = card.getAttribute('data-slug') || '';
                            const match = !q || name.includes(q) || slug.includes(q);
                            const col = card.closest('[class*="col-"]');
                            (col || card).style.display = match ? '' : 'none';
                        });
                    };

                    input.addEventListener('input', apply);
                    apply();
                })();
            </script>
        </form>
    @endif
</div>
</div>
@endsection
