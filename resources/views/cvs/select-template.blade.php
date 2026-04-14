@extends('layouts.app')

@section('content')
<style>
    .tpl-page {
        max-width: 1240px;
        margin: 0 auto;
    }

    .tpl-search {
        max-width: 560px;
    }

    .tpl-card {
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        overflow: hidden;
        background: #fff;
        transition: all .22s ease;
        cursor: pointer;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }

    .tpl-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 26px -14px rgba(15, 23, 42, 0.35);
        border-color: #cbd5e1;
    }

    .tpl-card.is-selected {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12), 0 10px 26px -14px rgba(15, 23, 42, 0.35);
    }

    .tpl-preview {
        aspect-ratio: 210 / 297;
        background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
        border-bottom: 1px solid #e2e8f0;
        overflow: hidden;
        position: relative;
    }

    .tpl-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: top center;
        display: block;
    }

    .tpl-preview::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(15, 23, 42, 0) 70%, rgba(15, 23, 42, 0.05) 100%);
        pointer-events: none;
    }

    .tpl-meta {
        padding: 14px 14px 16px;
    }

    .tpl-name {
        font-size: 1.05rem;
        font-weight: 700;
        color: #0f172a;
    }

    .tpl-desc {
        color: #64748b;
        font-size: .9rem;
        line-height: 1.45;
    }

    @media (max-width: 575.98px) {
        .tpl-meta {
            padding: 12px 12px 14px;
        }
    }
</style>

<div class="container tpl-page">
    <h1 class="h3 mb-2">Step 1 — Select Template</h1>
    <p class="text-muted mb-3">Pilih desain CV yang paling cocok. Semua card preview sudah dalam frame penuh dan responsif.</p>

    @if($templates->isEmpty())
        <div class="alert alert-warning">No active templates available.</div>
    @else
        {{-- Search template --}}
        <div class="mb-3 tpl-search">
            <div class="input-group">
                <span class="input-group-text" aria-hidden="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                </span>
                <input id="tplSearch" type="search" class="form-control" placeholder="Cari template…" autocomplete="off">
            </div>
            <div class="form-text">Ketik nama/slug untuk memfilter template.</div>
        </div>

        <form method="POST" action="{{ route('cv-builder.templates.save') }}">
            @csrf

            <div class="row g-3">
                @foreach($templates as $tpl)
                    <div class="col-12 col-sm-6 col-xl-4">
                        <label class="h-100 js-template-card tpl-card" data-name="{{ strtolower($tpl->name) }}" data-slug="{{ strtolower($tpl->slug) }}">
                            @php($thumb = $tpl->thumbnail ?: asset('images/templates/'.$tpl->slug.'.png'))
                            <div class="tpl-preview">
                                <img src="{{ $thumb }}" alt="Preview {{ $tpl->name }}" onerror="this.style.display='none'; this.parentElement.classList.add('thumb-fallback');">
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
                    </div>
                @endforeach
            </div>

            <script>
                (() => {
                    const input = document.getElementById('tplSearch');
                    if (!input) return;

                    const cards = Array.from(document.querySelectorAll('.js-template-card'));
                    const radios = Array.from(document.querySelectorAll('input[name="template_slug"]'));

                    const syncSelected = () => {
                        cards.forEach((card) => {
                            const radio = card.querySelector('input[name="template_slug"]');
                            card.classList.toggle('is-selected', !!radio?.checked);
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

            @error('template_slug')
                <div class="text-danger small mt-2">{{ $message }}</div>
            @enderror

            <div class="mt-3 d-flex gap-2">
                <button class="btn btn-primary" type="submit">Continue</button>
                <a class="btn btn-outline-secondary" href="{{ route('cvs.index') }}">Cancel</a>
            </div>
        </form>
    @endif
</div>
@endsection
