@extends('layouts.app')

@section('content')
@php
    $name = $cv->personal_name ?: $cv->user->name;
    $email = $cv->personal_email ?: $cv->user->email;
    $photoUrl = $cv->photo_path ? asset('storage/' . $cv->photo_path) : null;
    $initials = collect(explode(' ', $name))->map(fn($w) => strtoupper(mb_substr($w, 0, 1)))->take(2)->join('');
@endphp

<style>
    .cv-page {
        min-height: 100vh;
        padding: 24px 0;
        background: #f3f4f6;
        font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, 'Segoe UI', sans-serif;
    }

    .cv-paper {
        width: min(794px, calc(100% - 24px));
        min-height: 1123px;
        margin: 0 auto;
        padding: 48px;
        background: #ffffff;
        box-shadow: 0 16px 40px rgba(15, 23, 42, 0.12);
        color: #111827;
    }

    .cv-header {
        border-bottom: 1px solid #e5e7eb;
        padding-bottom: 22px;
    }

    .cv-headline-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
    }

    .cv-identity {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .cv-photo,
    .cv-photo-fallback {
        width: 64px;
        height: 64px;
        border-radius: 10px;
        border: 1px solid #d1d5db;
        object-fit: cover;
        flex-shrink: 0;
    }

    .cv-photo-fallback {
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f9fafb;
        color: #374151;
        font-weight: 700;
    }

    .cv-name {
        margin: 0;
        font-size: 34px;
        line-height: 1.05;
        font-weight: 800;
        letter-spacing: -0.02em;
        color: #111827;
    }

    .cv-role {
        margin-top: 4px;
        font-size: 15px;
        color: #374151;
        font-weight: 500;
    }

    .cv-email {
        margin-top: 8px;
        display: inline-block;
        font-size: 14px;
        color: #374151;
        text-decoration: none;
    }

    .cv-summary {
        margin-top: 16px;
        color: #374151;
        font-size: 15px;
        line-height: 1.65;
    }

    .cv-paper-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 28px;
        margin-top: 30px;
    }

    .cv-section {
        margin-top: 30px;
        border-bottom: 1px solid #e5e7eb;
        padding-bottom: 18px;
    }

    .cv-section:first-child {
        margin-top: 0;
    }

    .cv-section:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }

    .cv-section-title {
        margin: 0 0 12px;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.16em;
        color: #6b7280;
        font-weight: 700;
    }

    .cv-item {
        margin-bottom: 14px;
    }

    .cv-item:last-child {
        margin-bottom: 0;
    }

    .cv-item-head {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        align-items: baseline;
    }

    .cv-item-main {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        color: #111827;
    }

    .cv-item-sub {
        margin: 2px 0 0;
        font-size: 14px;
        color: #4b5563;
    }

    .cv-item-date {
        font-size: 13px;
        color: #6b7280;
        white-space: nowrap;
    }

    .cv-item-desc {
        margin: 8px 0 0;
        font-size: 14px;
        color: #374151;
        line-height: 1.6;
    }

    .cv-skills {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .cv-skill-tag {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        border: 1px solid #d1d5db;
        border-radius: 999px;
        padding: 5px 11px;
        font-size: 13px;
        color: #374151;
        background: #ffffff;
    }

    .cv-skill-level {
        color: #6b7280;
        font-weight: 400;
    }

    .cv-muted {
        color: #6b7280;
        font-size: 14px;
        font-style: italic;
    }

    .cv-info-block {
        border: 1px solid #e5e7eb;
        border-left: 4px solid #111827;
        border-radius: 10px;
        padding: 12px;
        background: #ffffff;
        font-size: 14px;
        color: #374151;
        line-height: 1.6;
    }

    .cv-right-column {
        position: relative;
    }

    .cv-right-column::before {
        content: '';
        position: absolute;
        top: 0;
        left: -14px;
        width: 3px;
        height: 100%;
        background: linear-gradient(180deg, #111827 0%, rgba(107, 114, 128, 0.15) 100%);
        border-radius: 3px;
    }

    @media (max-width: 900px) {
        .cv-page {
            padding: 10px 0;
        }

        .cv-paper {
            width: calc(100% - 10px);
            min-height: auto;
            padding: 26px 20px;
        }

        .cv-paper-grid {
            grid-template-columns: 1fr;
            gap: 22px;
        }

        .cv-right-column::before {
            display: none;
        }

        .cv-name {
            font-size: 30px;
        }

        .cv-headline-row {
            flex-direction: column;
            align-items: flex-start;
        }
    }

    @media print {
        .cv-page {
            background: #ffffff !important;
            padding: 0 !important;
        }

        .cv-paper {
            width: 100% !important;
            min-height: auto !important;
            margin: 0 !important;
            padding: 24px !important;
            box-shadow: none !important;
        }

        .cv-right-column::before {
            opacity: 0.25;
        }
    }
</style>

<div class="cv-page">
    <article class="cv-paper">
        <header class="cv-header">
            <div class="cv-headline-row">
                <div class="cv-identity">
                    @if($photoUrl)
                        <img src="{{ $photoUrl }}" alt="Photo" class="cv-photo" />
                    @else
                        <div class="cv-photo-fallback">{{ $initials }}</div>
                    @endif
                    <div>
                        <h1 class="cv-name">{{ $name }}</h1>
                        <div class="cv-role">{{ $cv->title ?: 'Professional Profile' }}</div>
                    </div>
                </div>
                <a href="mailto:{{ $email }}" class="cv-email">{{ $email }}</a>
            </div>

            @if($cv->summary)
                <p class="cv-summary">{{ $cv->summary }}</p>
            @endif
        </header>

        <div class="cv-paper-grid">
            <main>
                <section class="cv-section">
                    <h2 class="cv-section-title">Experience</h2>
                    @forelse($cv->experiences as $exp)
                        <article class="cv-item">
                            <div class="cv-item-head">
                                <div>
                                    <p class="cv-item-main">{{ $exp->position }}</p>
                                    <p class="cv-item-sub">{{ $exp->company }}</p>
                                </div>
                                <div class="cv-item-date">{{ $exp->start_date }} - {{ $exp->end_date ?? 'Present' }}</div>
                            </div>
                            @if($exp->description)
                                <p class="cv-item-desc">{{ $exp->description }}</p>
                            @endif
                        </article>
                    @empty
                        <p class="cv-muted">No experience listed.</p>
                    @endforelse
                </section>

                <section class="cv-section">
                    <h2 class="cv-section-title">Education</h2>
                    @forelse($cv->educations as $edu)
                        <article class="cv-item">
                            <div class="cv-item-head">
                                <div>
                                    <p class="cv-item-main">{{ $edu->school }}</p>
                                    <p class="cv-item-sub">{{ $edu->degree }}</p>
                                </div>
                                <div class="cv-item-date">{{ $edu->year }}</div>
                            </div>
                        </article>
                    @empty
                        <p class="cv-muted">No education listed.</p>
                    @endforelse
                </section>
            </main>

            <aside class="cv-right-column">
                <section class="cv-section">
                    <h2 class="cv-section-title">Skills</h2>
                    @if($cv->skills->isEmpty())
                        <p class="cv-muted">No skills listed.</p>
                    @else
                        <div class="cv-skills">
                            @foreach($cv->skills as $skill)
                                <span class="cv-skill-tag">
                                    {{ $skill->name }}
                                    @if($skill->level)
                                        <span class="cv-skill-level">{{ $skill->level }}</span>
                                    @endif
                                </span>
                            @endforeach
                        </div>
                    @endif
                </section>

                <section class="cv-section">
                    <h2 class="cv-section-title">Information</h2>
                    <div class="cv-info-block">
                        <div><strong>Email:</strong> {{ $email }}</div>
                        <div><strong>Template:</strong> Mono Poster</div>
                    </div>
                </section>
            </aside>
        </div>
    </article>
</div>
@endsection
