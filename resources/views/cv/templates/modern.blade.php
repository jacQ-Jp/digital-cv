@extends('layouts.app')

@section('content')
@php
    $name = $cv->user->name;
    $email = $cv->user->email;
    $initials = collect(explode(' ', $name))->map(fn($w) => strtoupper(mb_substr($w, 0, 1)))->take(2)->join('');
@endphp

<style>
    /* ── Base ── */
    .cv-root { max-width: 1060px; margin: 0 auto; padding: 2rem 1rem; }
    @media (min-width: 768px) { .cv-root { padding: 3rem 1.5rem; } }

    /* ── Hero ── */
    .cv-hero {
        position: relative;
        border-radius: 20px;
        overflow: hidden;
        background: #0f0f0f;
        color: #fff;
        padding: 2.5rem 2rem;
    }
    @media (min-width: 768px) { .cv-hero { padding: 3rem 2.5rem; } }
    .cv-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(ellipse 60% 80% at 85% 20%, rgba(234,88,12,.12) 0%, transparent 70%),
                    radial-gradient(ellipse 50% 60% at 10% 80%, rgba(124,58,237,.08) 0%, transparent 60%);
        pointer-events: none;
    }
    .cv-hero::after {
        content: '';
        position: absolute;
        inset: 0;
        background-image: radial-gradient(rgba(255,255,255,.04) 1px, transparent 1px);
        background-size: 20px 20px;
        pointer-events: none;
    }
    .cv-hero > * { position: relative; z-index: 1; }

    .cv-avatar {
        width: 72px; height: 72px;
        border-radius: 16px;
        background: linear-gradient(135deg, #ea580c, #f59e0b);
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 1.25rem; letter-spacing: .02em;
        flex-shrink: 0;
        box-shadow: 0 8px 24px -6px rgba(234,88,12,.35);
    }
    @media (min-width: 768px) { .cv-avatar { width: 80px; height: 80px; font-size: 1.35rem; } }

    .cv-hero-label {
        font-size: .7rem; font-weight: 600;
        letter-spacing: .12em; text-transform: uppercase;
        color: #ea580c;
    }
    .cv-hero-name {
        font-size: 1.75rem; font-weight: 700;
        line-height: 1.15; margin: .35rem 0 .25rem;
        letter-spacing: -.02em;
    }
    @media (min-width: 768px) { .cv-hero-name { font-size: 2.25rem; } }
    .cv-hero-email { color: rgba(255,255,255,.5); font-size: .875rem; }
    .cv-hero-email:hover { color: #fff; }
    .cv-hero-title {
        font-size: .95rem; font-weight: 600;
        color: rgba(255,255,255,.85);
    }
    .cv-hero-status {
        display: inline-flex; align-items: center; gap: 5px;
        font-size: .72rem; font-weight: 600;
        text-transform: uppercase; letter-spacing: .06em;
        padding: 3px 10px; border-radius: 99px;
        margin-top: .5rem;
    }
    .cv-hero-status--active { background: rgba(16,185,129,.12); color: #34d399; }
    .cv-hero-status--other  { background: rgba(255,255,255,.08); color: rgba(255,255,255,.55); }
    .cv-hero-status .pulse {
        width: 6px; height: 6px; border-radius: 50%;
        background: currentColor;
        animation: pulse 2s ease-in-out infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: .4; transform: scale(.8); }
    }

    .cv-hero-summary {
        color: rgba(255,255,255,.5);
        font-size: .875rem; line-height: 1.7;
        max-width: 65ch; margin-top: 1.25rem;
    }

    /* ── Cards ── */
    .cv-card {
        border: 1px solid rgba(0,0,0,.06);
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        transition: box-shadow .4s ease, transform .4s ease;
    }
    .cv-card:hover {
        box-shadow: 0 12px 32px -8px rgba(0,0,0,.08);
        transform: translateY(-1px);
    }
    .cv-card-body { padding: 1.5rem; }
    @media (min-width: 768px) { .cv-card-body { padding: 1.75rem 2rem; } }

    /* ── Section Title ── */
    .cv-section-title {
        display: flex; align-items: center; gap: .6rem;
        font-size: .8rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: .1em;
        color: #a3a3a3; margin-bottom: 1.25rem;
    }
    .cv-section-title::after {
        content: ''; flex: 1; height: 1px;
        background: #f0f0f0;
    }
    .cv-section-icon {
        width: 28px; height: 28px; border-radius: 8px;
        background: #fff7ed; color: #ea580c;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    /* ── Timeline ── */
    .cv-timeline { position: relative; padding-left: 24px; }
    .cv-timeline::before {
        content: ''; position: absolute;
        left: 5px; top: 10px; bottom: 10px;
        width: 2px; background: #f0f0f0; border-radius: 1px;
    }
    .cv-timeline-item { position: relative; padding-bottom: 1.75rem; }
    .cv-timeline-item:last-child { padding-bottom: 0; }
    .cv-timeline-dot {
        position: absolute; left: -24px; top: 6px;
        width: 12px; height: 12px; border-radius: 50%;
        background: #fff; border: 2.5px solid #e5e5e5;
        transition: all .3s ease; z-index: 1;
    }
    .cv-timeline-item:hover .cv-timeline-dot {
        border-color: #ea580c; background: #ea580c;
        box-shadow: 0 0 0 4px rgba(234,88,12,.1);
    }
    .cv-timeline-position {
        font-size: .9375rem; font-weight: 600;
        color: #171717; margin-bottom: 2px;
        transition: color .3s ease;
    }
    .cv-timeline-item:hover .cv-timeline-position { color: #ea580c; }
    .cv-timeline-company { font-size: .8125rem; color: #737373; margin-bottom: 2px; }
    .cv-timeline-date {
        font-size: .7rem; font-weight: 600; color: #a3a3a3;
        letter-spacing: .02em;
    }
    .cv-timeline-desc {
        font-size: .8125rem; color: #525252;
        line-height: 1.65; margin-top: .5rem;
    }

    /* ── Education ── */
    .cv-edu-item {
        display: flex; align-items: flex-start; gap: .875rem;
        padding: .875rem 0;
    }
    .cv-edu-item + .cv-edu-item { border-top: 1px solid #f5f5f5; }
    .cv-edu-icon {
        width: 36px; height: 36px; border-radius: 10px;
        background: #fafafa; border: 1px solid #f0f0f0;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; color: #a3a3a3;
    }
    .cv-edu-school { font-size: .875rem; font-weight: 600; color: #171717; }
    .cv-edu-meta { font-size: .78rem; color: #a3a3a3; margin-top: 1px; }

    /* ── Skills ── */
    .cv-skill-chip {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 5px 14px; border-radius: 10px;
        font-size: .78rem; font-weight: 500;
        background: #fafafa; color: #525252;
        border: 1px solid #f0f0f0;
        transition: all .3s ease;
    }
    .cv-skill-chip:hover {
        background: #171717; color: #fff;
        border-color: #171717;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,.1);
    }
    .cv-skill-chip .level {
        color: #a3a3a3; font-weight: 400;
    }
    .cv-skill-chip:hover .level { color: rgba(255,255,255,.5); }

    /* ── Empty State ── */
    .cv-empty {
        font-size: .8125rem; color: #d4d4d4;
        padding: 1.5rem 0; text-align: center;
        font-style: italic;
    }

    /* ── Template badge ── */
    .cv-template-badge {
        border-radius: 12px;
        background: #fafafa;
        border: 1px dashed #e5e5e5;
        padding: 1rem 1.25rem;
        font-size: .75rem; color: #a3a3a3;
    }
    .cv-template-badge code {
        background: #f0f0f0; padding: 1px 6px;
        border-radius: 4px; font-size: .7rem;
    }

    /* ── Print ── */
    @media print {
        body { background: #fff !important; }
        .cv-root { padding: 0 !important; max-width: 100% !important; }
        .cv-card { box-shadow: none !important; border-color: #eee !important; break-inside: avoid; }
        .cv-card:hover { transform: none !important; box-shadow: none !important; }
        .cv-skill-chip:hover { transform: none !important; box-shadow: none !important; background: #fafafa !important; color: #525252 !important; border-color: #f0f0f0 !important; }
        .cv-template-badge { display: none !important; }
        .no-print { display: none !important; }
        .cv-hero { border-radius: 0 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .cv-timeline-item:hover .cv-timeline-dot { border-color: #e5e5e5; background: #fff; box-shadow: none; }
        .cv-timeline-item:hover .cv-timeline-position { color: #171717; }
    }

    /* ── Animation ── */
    .cv-anim {
        opacity: 0; transform: translateY(16px);
        animation: cvFadeUp .7s cubic-bezier(.25,1,.5,1) forwards;
    }
    .cv-anim-d1 { animation-delay: .08s; }
    .cv-anim-d2 { animation-delay: .16s; }
    .cv-anim-d3 { animation-delay: .24s; }
    .cv-anim-d4 { animation-delay: .32s; }
    .cv-anim-d5 { animation-delay: .40s; }
    @keyframes cvFadeUp {
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="cv-root">
    {{-- ━━━ HERO ━━━ --}}
    <div class="cv-hero cv-anim mb-4">
        <div class="d-flex align-items-start gap-3 gap-md-4 flex-wrap flex-md-nowrap">
            <div class="cv-avatar">{{ $initials }}</div>
            <div class="flex-grow-1">
                <div class="cv-hero-label">Digital CV</div>
                <h1 class="cv-hero-name mb-0">{{ $name }}</h1>
                <a href="mailto:{{ $email }}" class="cv-hero-email text-decoration-none d-inline-flex align-items-center gap-1">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                    {{ $email }}
                </a>
            </div>
            <div class="text-md-end mt-2 mt-md-0">
                <div class="cv-hero-title">{{ $cv->title }}</div>
                <div class="cv-hero-status {{ strtolower($cv->status) === 'active' ? 'cv-hero-status--active' : 'cv-hero-status--other' }}">
                    <span class="pulse"></span>
                    {{ ucfirst($cv->status) }}
                </div>
            </div>
        </div>
        @if($cv->summary)
            <p class="cv-hero-summary mb-0">{{ $cv->summary }}</p>
        @endif
    </div>

    {{-- ━━━ BODY ━━━ --}}
    <div class="row g-4">
        {{-- LEFT --}}
        <div class="col-lg-7">
            {{-- Experience --}}
            <div class="cv-card cv-anim cv-anim-d1 mb-4">
                <div class="cv-card-body">
                    <div class="cv-section-title">
                        <span class="cv-section-icon">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                        </span>
                        Experience
                    </div>
                    @forelse($cv->experiences as $exp)
                        <div class="cv-timeline">
                            <div class="cv-timeline-item">
                                <span class="cv-timeline-dot"></span>
                                <div class="cv-timeline-position">{{ $exp->position }}</div>
                                <div class="cv-timeline-company">{{ $exp->company }}</div>
                                <div class="cv-timeline-date">{{ $exp->start_date }} — {{ $exp->end_date ?? 'Present' }}</div>
                                @if($exp->description)
                                    <div class="cv-timeline-desc">{{ $exp->description }}</div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="cv-empty">No experience added yet.</div>
                    @endforelse
                </div>
            </div>

            {{-- Education --}}
            <div class="cv-card cv-anim cv-anim-d3">
                <div class="cv-card-body">
                    <div class="cv-section-title">
                        <span class="cv-section-icon">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c0 1.66 2.69 3 6 3s6-1.34 6-3v-5"/></svg>
                        </span>
                        Education
                    </div>
                    @forelse($cv->educations as $edu)
                        <div class="cv-edu-item">
                            <div class="cv-edu-icon">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c0 1.66 2.69 3 6 3s6-1.34 6-3v-5"/></svg>
                            </div>
                            <div>
                                <div class="cv-edu-school">{{ $edu->school }}</div>
                                <div class="cv-edu-meta">{{ $edu->degree }} · {{ $edu->year }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="cv-empty">No education added yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- RIGHT --}}
        <div class="col-lg-5">
            {{-- Skills --}}
            <div class="cv-card cv-anim cv-anim-d2">
                <div class="cv-card-body">
                    <div class="cv-section-title">
                        <span class="cv-section-icon">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        </span>
                        Skills
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        @forelse($cv->skills as $skill)
                            <span class="cv-skill-chip">
                                {{ $skill->name }}
                                @if($skill->level)
                                    <span class="level">· {{ $skill->level }}</span>
                                @endif
                            </span>
                        @empty
                            <div class="cv-empty w-100">No skills added yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Template Info --}}
            <div class="cv-template-badge no-print cv-anim cv-anim-d5 mt-4">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.375 2.625a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4Z"/></svg>
                    <span class="fw-semibold" style="color:#737373">Template: Modern</span>
                </div>
                <div>Buat variasi desain dengan membuat file baru di <code>resources/views/cv/templates</code> dan set slug yang sama.</div>
            </div>
        </div>
    </div>
</div>
@endsection