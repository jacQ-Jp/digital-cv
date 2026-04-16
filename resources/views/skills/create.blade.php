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

    .wizard-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 8px;
        border-radius: 999px;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #334155;
        font-size: .68rem;
        font-weight: 600;
    }

    .wizard-chip-level {
        color: #94a3b8;
        font-weight: 500;
    }
</style>

<div class="container py-2">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h4 mb-0">Step 4: Add Skill</h1>
        <span class="badge text-bg-dark">Step 4/5</span>
    </div>

    <form method="POST" action="{{ route('cvs.skills.store', $cv) }}" id="skillForm">
        @csrf

        <div class="row g-4 align-items-start">
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label">Skill Name</label>
                            <input id="nameInput" name="name" class="form-control" value="{{ old('name') }}" required>
                            @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Level</label>
                            <input id="levelInput" name="level" class="form-control" value="{{ old('level') }}" placeholder="beginner / intermediate / expert">
                            @error('level')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-primary" type="submit">Save Skill & Next: Finalize</button>
                            <a class="btn btn-outline-secondary" href="{{ route('cvs.skills.index', $cv) }}">Skip to Finalize</a>
                            <a class="btn btn-outline-dark" href="{{ route('cvs.educations.create', $cv) }}">Back to Education</a>
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
                                <div class="text-muted small">Skill baru yang kamu ketik langsung muncul di kanan.</div>
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

                                <div class="mt-2" style="font-size:.68rem; color:#475569; line-height:1.4; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;">
                                    {{ $cv->summary ?: 'Summary belum diisi.' }}
                                </div>

                                <div class="mt-2 border-top pt-2">
                                    <div class="wizard-label mb-1">Experience</div>
                                    <div style="font-size:.68rem; color:#64748b;">{{ $cv->experiences->count() }} item</div>
                                </div>

                                <div class="mt-2 border-top pt-2">
                                    <div class="wizard-label mb-1">Education</div>
                                    <div style="font-size:.68rem; color:#64748b;">{{ $cv->educations->count() }} item</div>
                                </div>

                                <div class="mt-2 border-top pt-2">
                                    <div class="wizard-label mb-1">Skills</div>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($cv->skills->take(6) as $item)
                                            <span class="wizard-chip">{{ $item->name }} @if($item->level)<span class="wizard-chip-level">{{ $item->level }}</span>@endif</span>
                                        @endforeach
                                        <span class="wizard-chip" id="draftSkillChip">Skill baru</span>
                                    </div>
                                </div>

                                <div class="mt-auto border-top pt-2" style="font-size:.68rem; color:#64748b;">
                                    Next: Finalize dan pilih Draft/Public Link.
                                </div>
                            </div>
                        </div>

                        <div class="small text-muted">Setelah ini, di step akhir kamu pilih status Draft atau Public Link.</div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
(() => {
    const nameInput = document.getElementById('nameInput');
    const levelInput = document.getElementById('levelInput');
    const draftSkillChip = document.getElementById('draftSkillChip');

    const updateDraft = () => {
        const name = (nameInput?.value || '').trim();
        const level = (levelInput?.value || '').trim();

        draftSkillChip.textContent = name || 'Skill baru';
        if (level) {
            draftSkillChip.textContent += ' ' + level;
        }
    };

    nameInput?.addEventListener('input', updateDraft);
    levelInput?.addEventListener('input', updateDraft);
    updateDraft();
})();
</script>
@endsection
