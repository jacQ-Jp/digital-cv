@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="h3 mb-3">Step 1 — Select Template</h1>

    @if($templates->isEmpty())
        <div class="alert alert-warning">No active templates available.</div>
    @else
        {{-- Search template --}}
        <div class="mb-3" style="max-width:520px;">
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
                    <div class="col-md-4">
                        <label class="card h-100 js-template-card" style="cursor:pointer;" data-name="{{ strtolower($tpl->name) }}" data-slug="{{ strtolower($tpl->slug) }}">
                            <div class="card-body">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="template_slug" value="{{ $tpl->slug }}" @checked(old('template_slug', $templates->firstWhere('is_default', true)?->slug) === $tpl->slug)>
                                    <span class="form-check-label fw-semibold">{{ $tpl->name }}</span>
                                    @if($tpl->is_default)
                                        <span class="badge text-bg-primary ms-2">default</span>
                                    @endif
                                </div>
                                @if($tpl->description)
                                    <p class="text-muted small mt-2 mb-0">{{ $tpl->description }}</p>
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
