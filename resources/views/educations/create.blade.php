@extends('layouts.app')

@section('content')
@php
    $thumb = $cv->template?->thumbnail ? asset('storage/'.$cv->template->thumbnail) : null;
@endphp

<style>
    .wizard-preview-frame {
        aspect-ratio: 210 / 297;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        overflow: hidden;
        position: relative;
        background: #f8fafc;
    }

    .wizard-preview-bg {
        position: absolute;
        inset: 0;
    }

    .wizard-preview-bg img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: top center;
        opacity: .14;
        filter: grayscale(1);
    }

    .wizard-preview-overlay {
        position: absolute;
        inset: 0;
        padding: 12px;
        background: linear-gradient(180deg, rgba(255,255,255,.93) 0%, rgba(248,250,252,.97) 100%);
        display: flex;
        flex-direction: column;
    }

    .wizard-label {
        font-size: .62rem;
        letter-spacing: .08em;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
    }

    .wizard-item-title {
        font-size: .78rem;
        font-weight: 700;
        color: #0f172a;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .wizard-item-sub {
        font-size: .68rem;
        color: #64748b;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>

<div class="container py-2">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h4 mb-0">Step 3: Add Education</h1>
        <span class="badge text-bg-dark">Step 3/5</span>
    </div>

    <form method="POST" action="{{ route('cvs.educations.store', $cv) }}" id="educationForm">
        @csrf

        <div class="row g-4 align-items-start">
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label">School</label>
                            <input id="schoolInput" name="school" class="form-control" value="{{ old('school') }}" required>
                            @error('school')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Degree</label>
                            <input id="degreeInput" name="degree" class="form-control" value="{{ old('degree') }}" required>
                            @error('degree')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Year</label>
                            <input id="yearInput" name="year" class="form-control" value="{{ old('year') }}" required>
                            @error('year')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-primary" type="submit">Save & Next: Skill</button>
                            <a class="btn btn-outline-secondary" href="{{ route('cvs.skills.create', $cv) }}">Skip to Skill</a>
                            <a class="btn btn-outline-dark" href="{{ route('cvs.experiences.create', $cv) }}">Back to Experience</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm position-sticky" style="top:1rem;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h2 class="h6 mb-1">Live Review</h2>
                                <div class="text-muted small">Review mengikuti input Education yang sedang kamu isi.</div>
                            </div>
                            <span class="badge text-bg-secondary">Draft</span>
                        </div>

                        <div class="wizard-preview-frame mb-3">
                            <div class="wizard-preview-bg">
                                <img src="{{ $thumb }}" alt="Template preview" onerror="this.style.display='none'">
                            </div>

                            <div class="wizard-preview-overlay">
                                <div class="d-flex justify-content-between gap-2">
                                    <div>
                                        <div class="fw-bold" style="font-size:.88rem; color:#0f172a;">{{ $cv->title }}</div>
                                        <div style="font-size:.68rem; color:#64748b;">Template: {{ $cv->template?->name ?? $cv->template_slug }}</div>
                                    </div>
                                    <span class="badge text-bg-secondary" style="height:max-content;">Draft</span>
                                </div>

                                <div class="mt-2" style="font-size:.68rem; color:#475569; line-height:1.4; overflow:hidden; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical;">
                                    {{ $cv->summary ?: 'Summary belum diisi.' }}
                                </div>

                                <div class="mt-2 border-top pt-2">
                                    <div class="wizard-label mb-1">Experience</div>
                                    @foreach($cv->experiences->take(2) as $item)
                                        <div class="mb-2">
                                            <div class="wizard-item-title">{{ $item->position }}</div>
                                            <div class="wizard-item-sub">{{ $item->company }}</div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-2 border-top pt-2">
                                    <div class="wizard-label mb-1">Education</div>
                                    @foreach($cv->educations->take(2) as $item)
                                        <div class="mb-2">
                                            <div class="wizard-item-title">{{ $item->school }}</div>
                                            <div class="wizard-item-sub">{{ $item->degree }} • {{ $item->year }}</div>
                                        </div>
                                    @endforeach

                                    <div class="mb-2 p-2 rounded-2 border bg-white">
                                        <div class="wizard-item-title" id="draftSchool">School baru</div>
                                        <div class="wizard-item-sub" id="draftDegreeYear">Degree • Year</div>
                                    </div>
                                </div>

                                <div class="mt-auto border-top pt-2" style="font-size:.68rem; color:#64748b;">
                                    Skills: {{ $cv->skills->count() }}
                                </div>
                            </div>
                        </div>

                        <div class="small text-muted">Setelah step ini, kamu lanjut isi Skill.</div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
(() => {
    const schoolInput = document.getElementById('schoolInput');
    const degreeInput = document.getElementById('degreeInput');
    const yearInput = document.getElementById('yearInput');

    const draftSchool = document.getElementById('draftSchool');
    const draftDegreeYear = document.getElementById('draftDegreeYear');

    const updateDraft = () => {
        const school = (schoolInput?.value || '').trim();
        const degree = (degreeInput?.value || '').trim();
        const year = (yearInput?.value || '').trim();

        draftSchool.textContent = school || 'School baru';
        draftDegreeYear.textContent = (degree || 'Degree') + ' • ' + (year || 'Year');
    };

    [schoolInput, degreeInput, yearInput].forEach((el) => {
        el?.addEventListener('input', updateDraft);
    });

    updateDraft();
})();
</script>
@endsection
