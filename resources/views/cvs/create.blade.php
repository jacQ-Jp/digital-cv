@extends('layouts.app')

@section('content')
@php
    $selectedSlug = old('template_slug', $selectedTemplateSlug ?? $templates->first()?->slug);
    $selectedTemplate = $templates->firstWhere('slug', $selectedSlug) ?? $templates->first();
    $authName = auth()->user()?->name ?? 'Your Name';
    $authEmail = auth()->user()?->email ?? 'email@example.com';

    $templateOptions = $templates->map(function ($tpl) {
        return [
            'slug' => $tpl->slug,
            'name' => $tpl->name,
            'thumbnail' => $tpl->thumbnail ?: asset('images/templates/' . $tpl->slug . '.png'),
        ];
    })->values();

    $defaultThumb = $selectedTemplate
        ? ($selectedTemplate->thumbnail ?: asset('images/templates/' . $selectedTemplate->slug . '.png'))
        : null;
@endphp

<style>
    .wizard-step-badge {
        font-size: .78rem;
        font-weight: 700;
        letter-spacing: .04em;
    }

    .cv-live-frame {
        aspect-ratio: 210 / 297;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        overflow: hidden;
        position: relative;
        background: #f8fafc;
    }

    .cv-live-bg {
        position: absolute;
        inset: 0;
    }

    .cv-live-bg img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: top center;
        opacity: .22;
        filter: grayscale(1) contrast(1.05);
        display: block;
    }

    .cv-live-overlay {
        position: absolute;
        inset: 0;
        padding: 14px;
        display: flex;
        flex-direction: column;
        background: linear-gradient(180deg, rgba(255,255,255,.92) 0%, rgba(248,250,252,.96) 100%);
    }

    .cv-live-name {
        font-size: 1rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.25;
        overflow-wrap: anywhere;
    }

    .cv-live-email {
        font-size: .72rem;
        color: #64748b;
        line-height: 1.2;
        overflow-wrap: anywhere;
    }

    .cv-live-title {
        margin-top: 10px;
        font-size: .92rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.35;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow-wrap: anywhere;
    }

    .cv-live-summary {
        margin-top: 8px;
        font-size: .76rem;
        line-height: 1.45;
        color: #475569;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 6;
        -webkit-box-orient: vertical;
        overflow-wrap: anywhere;
    }

    .cv-live-section {
        margin-top: 12px;
        border-top: 1px solid #e2e8f0;
        padding-top: 8px;
    }

    .cv-live-label {
        font-size: .62rem;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #94a3b8;
        margin-bottom: 6px;
    }

    .cv-live-line {
        height: 7px;
        border-radius: 4px;
        background: #e2e8f0;
        margin-bottom: 6px;
    }

    .cv-live-line.w-85 { width: 85%; }
    .cv-live-line.w-70 { width: 70%; }
    .cv-live-line.w-95 { width: 95%; }

    .cv-live-bottom {
        margin-top: auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        font-size: .68rem;
        color: #64748b;
        border-top: 1px solid #e2e8f0;
        padding-top: 8px;
        overflow-wrap: anywhere;
    }
</style>

<div class="container py-2">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h3 mb-0">Create CV</h1>
        <span class="badge text-bg-dark wizard-step-badge">Step 1/5 • Basic</span>
    </div>

    <div class="alert alert-info">
        Isi data dasar dulu, lalu lanjut ke Experience, Education, dan Skill.
        Pilihan Draft/Public Link akan ditentukan di langkah terakhir.
        <a href="{{ route('cv-builder.templates') }}" class="alert-link">Ganti template</a>.
    </div>

    <form method="POST" action="{{ route('cvs.store') }}" id="createCvForm">
        @csrf
        <input type="hidden" name="status" value="draft">

        <div class="row g-4 align-items-start">
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input name="title" id="titleInput" class="form-control" value="{{ old('title') }}" required>
                            @error('title')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Summary</label>
                            <textarea name="summary" id="summaryInput" class="form-control" rows="6">{{ old('summary') }}</textarea>
                            @error('summary')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Template</label>
                            <select name="template_slug" id="templateInput" class="form-select" required>
                                @foreach($templates as $tpl)
                                    <option value="{{ $tpl->slug }}" @selected($selectedSlug === $tpl->slug)>
                                        {{ $tpl->name }}@if($tpl->is_default) (default)@endif
                                    </option>
                                @endforeach
                            </select>
                            @error('template_slug')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>

                        <div class="small text-muted mb-4">
                            Status CV default: Draft. Kamu pilih Draft/Public Link di langkah terakhir.
                        </div>

                        <div class="d-flex gap-2">
                            <button class="btn btn-primary" type="submit">Next: Experience</button>
                            <a class="btn btn-outline-secondary" href="{{ route('cvs.index') }}">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm position-sticky" style="top:1rem;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                            <div>
                                <h2 class="h6 mb-1">Live Review</h2>
                                <div class="text-muted small">Review kanan selalu mengikuti input kiri.</div>
                            </div>
                            <span class="badge rounded-pill text-bg-secondary">Draft</span>
                        </div>

                        <div class="cv-live-frame mb-3">
                            <div class="cv-live-bg">
                                <img id="previewThumb" src="{{ $defaultThumb }}" alt="Template preview">
                            </div>

                            <div class="cv-live-overlay">
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <div>
                                        <div class="cv-live-name">{{ $authName }}</div>
                                        <div class="cv-live-email">{{ $authEmail }}</div>
                                    </div>
                                    <span class="badge rounded-pill text-bg-secondary">Draft</span>
                                </div>

                                <h3 class="cv-live-title" id="previewTitle">{{ old('title') ?: 'Judul CV kamu' }}</h3>
                                <p class="cv-live-summary mb-0" id="previewSummary">
                                    {{ old('summary') ?: 'Ringkasan profil akan muncul di sini agar kamu bisa cek hasil sebelum lanjut step berikutnya.' }}
                                </p>

                                <div class="cv-live-section">
                                    <div class="cv-live-label">Experience</div>
                                    <div class="cv-live-line w-95"></div>
                                    <div class="cv-live-line w-85"></div>
                                    <div class="cv-live-line w-70"></div>
                                </div>

                                <div class="cv-live-section">
                                    <div class="cv-live-label">Education</div>
                                    <div class="cv-live-line w-95"></div>
                                    <div class="cv-live-line w-70"></div>
                                </div>

                                <div class="cv-live-bottom">
                                    <span id="previewTemplateName">{{ $selectedTemplate?->name ?? '-' }}</span>
                                    <span>Draft</span>
                                </div>
                            </div>
                        </div>

                        <div class="small text-muted">
                            Setelah step ini, kamu akan lanjut isi Experience, Education, lalu Skill.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
(() => {
    const templates = @json($templateOptions);
    const templateMap = Object.fromEntries(templates.map((item) => [item.slug, item]));

    const titleInput = document.getElementById('titleInput');
    const summaryInput = document.getElementById('summaryInput');
    const templateInput = document.getElementById('templateInput');

    const previewTitle = document.getElementById('previewTitle');
    const previewSummary = document.getElementById('previewSummary');
    const previewTemplateName = document.getElementById('previewTemplateName');
    const previewThumb = document.getElementById('previewThumb');

    const updateTitle = () => {
        const value = (titleInput?.value || '').trim();
        previewTitle.textContent = value || 'Judul CV kamu';
    };

    const updateSummary = () => {
        const value = (summaryInput?.value || '').trim();
        previewSummary.textContent = value || 'Ringkasan profil akan muncul di sini agar kamu bisa cek hasil sebelum lanjut step berikutnya.';
    };

    const updateTemplate = () => {
        const selected = templateMap[templateInput?.value] || null;
        previewTemplateName.textContent = selected?.name || 'Template';

        if (selected?.thumbnail) {
            previewThumb.src = selected.thumbnail;
            previewThumb.style.display = 'block';
        }
    };

    previewThumb?.addEventListener('error', () => {
        previewThumb.style.display = 'none';
    });

    titleInput?.addEventListener('input', updateTitle);
    summaryInput?.addEventListener('input', updateSummary);
    templateInput?.addEventListener('change', updateTemplate);

    updateTitle();
    updateSummary();
    updateTemplate();
})();
</script>
@endsection
