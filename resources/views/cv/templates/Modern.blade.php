@extends($layout ?? 'layouts.render')

@section('content')
@php
    $name = data_get($cv, 'personal_name') ?: (data_get($cv, 'user.name') ?? 'CV');
    $email = data_get($cv, 'personal_email') ?: (data_get($cv, 'user.email') ?? '');
    $title = data_get($cv, 'title');
    $summary = data_get($cv, 'summary');
    $phone = data_get($cv, 'personal_phone');
    $location = data_get($cv, 'personal_location');
    $linkedin = data_get($cv, 'personal_linkedin');
    $website = data_get($cv, 'personal_website');
    $thumbMode = ($layout === 'layouts.thumb');

    $photoPreview = data_get($cv, 'photo_preview_url');
    $photoPath = data_get($cv, 'photo_path');
    $photo = $photoPreview ?: ($photoPath ? asset('storage/'.$photoPath) : null);
    $initials = collect(preg_split('/\s+/', (string) $name))
        ->filter()
        ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
        ->take(2)
        ->join('');

    $accent = strtoupper((string) data_get($cv, 'accent_color', '#7C3AED'));
    $themes = [
        '#7C3AED' => ['accent' => '#7C3AED', 'deep' => '#4C1D95', 'soft' => '#EDE9FE', 'text' => '#fff'],
        '#0EA5A4' => ['accent' => '#0EA5A4', 'deep' => '#115E59', 'soft' => '#CCFBF1', 'text' => '#fff'],
        '#3B82F6' => ['accent' => '#3B82F6', 'deep' => '#1E40AF', 'soft' => '#DBEAFE', 'text' => '#fff'],
        '#EA580C' => ['accent' => '#EA580C', 'deep' => '#9A3412', 'soft' => '#FFEDD5', 'text' => '#fff'],
        '#334155' => ['accent' => '#334155', 'deep' => '#0F172A', 'soft' => '#E2E8F0', 'text' => '#fff'],
        '#166534' => ['accent' => '#166534', 'deep' => '#14532D', 'soft' => '#DCFCE7', 'text' => '#fff'],
        '#BE123C' => ['accent' => '#BE123C', 'deep' => '#9F1239', 'soft' => '#FFE4E6', 'text' => '#fff'],
    ];
    $tone = $themes[$accent] ?? $themes['#7C3AED'];

    $experiences = collect(data_get($cv, 'experiences', []));
    $educations = collect(data_get($cv, 'educations', []));
    $skills = collect(data_get($cv, 'skills', []));

    $hasSummary = filled($summary);
    $hasExperiences = $experiences->isNotEmpty();
    $hasEducations = $educations->isNotEmpty();
    $hasSkills = $skills->isNotEmpty();
    $hasContact = filled($email) || filled($phone) || filled($linkedin) || filled($website);
    $hasSideContent = $hasContact || $hasSummary || $hasSkills;
    $hasMainContent = $hasExperiences || $hasEducations;

    $placeholderFlags = collect(data_get($cv, 'preview_placeholder_flags', []));
    $isPlaceholder = fn (string $key): bool => (bool) $placeholderFlags->get($key, false);
    $itemPlaceholder = fn ($item, string $field): bool => (bool) data_get($item, '_placeholder.'.$field, false);
    $thumbRole = filled($title) ? $title : 'Untitled CV';

    $contactRows = collect([
        ['icon' => '@', 'value' => $email, 'fallback' => 'your@email.com', 'field' => 'personal_email'],
        ['icon' => 'P', 'value' => $phone, 'fallback' => '+62 812-3456-7890', 'field' => 'personal_phone'],
        ['icon' => 'L', 'value' => $location, 'fallback' => 'Jakarta, Indonesia', 'field' => 'personal_location'],
        ['icon' => 'in', 'value' => $linkedin, 'fallback' => 'linkedin.com/in/username', 'field' => 'personal_linkedin'],
        ['icon' => 'W', 'value' => $website, 'fallback' => 'yourname.site', 'field' => 'personal_website'],
    ]);

    $thumbSummary = $hasSummary
        ? $summary
        : 'Write a short professional summary about yourself...';

    $thumbEducations = $educations;
    if ($thumbMode && $thumbEducations->isEmpty()) {
        $thumbEducations = collect([
            (object) ['school' => 'School Name', 'degree' => 'Degree', 'year' => 'Year', '_placeholder' => ['school' => true, 'degree' => true, 'year' => true]],
            (object) ['school' => 'University Name', 'degree' => 'Major / Program', 'year' => 'Year', '_placeholder' => ['school' => true, 'degree' => true, 'year' => true]],
        ]);
    }

    $thumbExperiences = $experiences;
    if ($thumbMode && $thumbExperiences->isEmpty()) {
        $thumbExperiences = collect([
            (object) [
                'position' => 'Your Role',
                'company' => 'Company Name',
                'start_date' => '2022',
                'end_date' => 'Present',
                'description' => 'Describe key responsibilities and measurable achievements in this role.',
                '_placeholder' => ['position' => true, 'company' => true, 'start_date' => true, 'end_date' => true, 'description' => true],
            ],
            (object) [
                'position' => 'Previous Role',
                'company' => 'Previous Company',
                'start_date' => '2020',
                'end_date' => '2022',
                'description' => 'Highlight projects or impact that show your professional growth.',
                '_placeholder' => ['position' => true, 'company' => true, 'start_date' => true, 'end_date' => true, 'description' => true],
            ],
        ]);
    }

    $thumbSkills = $skills;
    if ($thumbMode && $thumbSkills->isEmpty()) {
        $thumbSkills = collect([
            (object) ['name' => 'Skill 1', '_placeholder' => ['name' => true]],
            (object) ['name' => 'Skill 2', '_placeholder' => ['name' => true]],
            (object) ['name' => 'Skill 3', '_placeholder' => ['name' => true]],
            (object) ['name' => 'Skill 4', '_placeholder' => ['name' => true]],
            (object) ['name' => 'Skill 5', '_placeholder' => ['name' => true]],
            (object) ['name' => 'Skill 6', '_placeholder' => ['name' => true]],
        ]);
    }
@endphp

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Manrope:wght@400;500;600&display=swap');

.cv-paper {
    --ac: {{ $tone['accent'] }};
    --ac-deep: {{ $tone['deep'] }};
    --ac-soft: {{ $tone['soft'] }};
    --text-light: {{ $tone['text'] }};
    --paper-bg: #f6f7fb;
}

* { box-sizing: border-box; }

.cv-wrapper {
    font-family: 'Manrope', sans-serif;
    min-height: 297mm;
    height: {{ $thumbMode ? '100%' : 'auto' }};
    background: var(--paper-bg);
    color: #334155;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
}

.cv-wrapper,
.cv-wrapper * {
    min-width: 0;
}

.cv-wrapper :where(h1, h2, h3, h4, p, span, div, li, a) {
    overflow-wrap: anywhere;
    word-break: break-word;
}

.cv-top {
    background: var(--ac-deep);
    color: var(--text-light);
    padding: {{ $thumbMode ? '24px 24px 18px' : '34px 48px 28px' }};
}

.cv-avatar-wrap {
    width: {{ $thumbMode ? '72px' : '92px' }};
    height: {{ $thumbMode ? '72px' : '92px' }};
    border-radius: 50%;
    border: 3px solid rgba(255, 255, 255, 0.25);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.22);
    margin: 0 auto 14px;
    overflow: hidden;
    background: rgba(255, 255, 255, 0.12);
}

.cv-avatar-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.cv-profile-fallback {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Poppins', sans-serif;
    font-size: {{ $thumbMode ? '26px' : '34px' }};
    font-weight: 700;
    letter-spacing: 0.04em;
    color: #fff;
}

.cv-identity {
    text-align: center;
    margin-bottom: 20px;
}

.cv-name {
    font-family: 'Poppins', sans-serif;
    font-size: {{ $thumbMode ? '24px' : '40px' }};
    font-weight: 800;
    line-height: 1.05;
    margin: 0;
    color: #fff;
}

.cv-role {
    margin-top: 6px;
    font-family: 'Poppins', sans-serif;
    font-size: {{ $thumbMode ? '10px' : '12px' }};
    text-transform: uppercase;
    letter-spacing: 2px;
    font-weight: 600;
    opacity: 0.86;
}

.cv-top-section + .cv-top-section {
    margin-top: 14px;
}

.cv-top-title {
    font-family: 'Poppins', sans-serif;
    font-size: {{ $thumbMode ? '10px' : '13px' }};
    text-transform: uppercase;
    letter-spacing: 1.8px;
    font-weight: 700;
    margin: 0 0 8px;
    padding-bottom: 6px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.24);
}

.cv-top-text {
    margin: 0;
    font-size: {{ $thumbMode ? '10px' : '12px' }};
    line-height: 1.55;
    color: rgba(255, 255, 255, 0.9);
}

.cv-contact-grid {
    display: grid;
    gap: 8px;
}

.cv-contact-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: {{ $thumbMode ? '10px' : '11px' }};
    color: rgba(255, 255, 255, 0.9);
}

.cv-contact-icon {
    width: 18px;
    height: 18px;
    border-radius: 6px;
    background: rgba(255, 255, 255, 0.14);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 9px;
    font-weight: 700;
    flex-shrink: 0;
}

.cv-body {
    padding: {{ $thumbMode ? '20px 24px 22px' : '30px 52px 34px' }};
    display: flex;
    flex-direction: column;
    gap: {{ $thumbMode ? '18px' : '26px' }};
    flex: 1;
    background: var(--paper-bg);
}

.cv-section {
    break-inside: avoid;
}

.cv-main-title {
    font-family: 'Poppins', sans-serif;
    font-size: {{ $thumbMode ? '16px' : '18px' }};
    line-height: 1.1;
    color: var(--ac-deep);
    text-transform: uppercase;
    font-weight: 800;
    margin: 0 0 14px;
    position: relative;
    padding-left: 16px;
    letter-spacing: 0.02em;
}

.cv-main-title::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 6px;
    height: 22px;
    border-radius: 4px;
    background: var(--ac);
}

.cv-timeline-item {
    position: relative;
    padding-left: 20px;
    border-left: 2px solid var(--ac-soft);
    margin-bottom: 18px;
}

.cv-timeline-item:last-child {
    margin-bottom: 0;
}

.cv-timeline-item::before {
    content: '';
    position: absolute;
    left: -7px;
    top: 6px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #fff;
    border: 2px solid var(--ac);
}

.cv-item-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    row-gap: 6px;
    margin-bottom: 4px;
}

.cv-pos-title {
    font-family: 'Poppins', sans-serif;
    font-size: {{ $thumbMode ? '12px' : '15px' }};
    font-weight: 700;
    color: #1f2937;
}

.cv-company {
    font-size: {{ $thumbMode ? '10px' : '13px' }};
    font-weight: 600;
    color: #64748b;
    margin-bottom: 6px;
}

.cv-desc {
    font-size: {{ $thumbMode ? '10px' : '12px' }};
    color: #475569;
    line-height: 1.55;
}

.cv-date {
    font-size: {{ $thumbMode ? '9px' : '11px' }};
    font-weight: 700;
    color: var(--ac-deep);
    background: var(--ac-soft);
    border-radius: 5px;
    padding: 3px 8px;
    text-align: right;
}

.cv-skills-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.cv-skill-badge {
    font-size: {{ $thumbMode ? '9px' : '11px' }};
    font-weight: 600;
    color: var(--ac-deep);
    border: 1px solid var(--ac);
    border-radius: 999px;
    padding: 4px 10px;
    background: #fff;
}

.cv-placeholder {
    opacity: 0.5;
    font-style: italic;
}

@media screen and (max-width: {{ $thumbMode ? '0px' : '760px' }}) {
    .cv-top {
        padding: 26px 22px 20px;
    }

    .cv-name {
        font-size: 28px;
    }

    .cv-body {
        padding: 22px;
    }

    .cv-main-title {
        font-size: 20px;
    }
}
</style>

<div class="cv-wrapper">
    <header class="cv-top">
        <div class="cv-avatar-wrap">
            @if($photo)
                <img src="{{ $photo }}" alt="Profile">
            @else
                <div class="cv-profile-fallback">{{ $initials ?: 'CV' }}</div>
            @endif
        </div>

        <div class="cv-identity">
            <h1 class="cv-name {{ $isPlaceholder('personal_name') ? 'cv-placeholder' : '' }}">{{ $thumbMode ? Str::limit($name, 22) : $name }}</h1>
            @if($title || $thumbMode)
                <p class="cv-role {{ (!$title || $isPlaceholder('title')) ? 'cv-placeholder' : '' }}">
                    {{ $thumbMode ? Str::limit($thumbRole, 28) : $title }}
                </p>
            @endif
        </div>

        @if($hasSummary || $thumbMode)
            <section class="cv-top-section">
                <h2 class="cv-top-title">About Me</h2>
                <p class="cv-top-text {{ (!$hasSummary || $isPlaceholder('summary')) ? 'cv-placeholder' : '' }}">
                    {{ $thumbMode ? Str::limit((string) $thumbSummary, 260) : $summary }}
                </p>
            </section>
        @endif

        @if($hasContact || $thumbMode)
            <section class="cv-top-section">
                <h2 class="cv-top-title">Contact</h2>
                <div class="cv-contact-grid">
                    @foreach($contactRows as $row)
                        @php
                            $rawValue = trim((string) data_get($row, 'value', ''));
                            $displayValue = $rawValue !== '' ? $rawValue : ($thumbMode ? (string) data_get($row, 'fallback', '') : '');
                            $isRowPlaceholder = $rawValue === '' || $isPlaceholder((string) data_get($row, 'field', ''));
                        @endphp
                        @if($displayValue !== '')
                            <div class="cv-contact-item {{ $isRowPlaceholder ? 'cv-placeholder' : '' }}">
                                <span class="cv-contact-icon">{{ data_get($row, 'icon') }}</span>
                                <span>{{ $displayValue }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </section>
        @endif
    </header>

    <main class="cv-body">
        @if($hasEducations || ($thumbMode && $thumbEducations->isNotEmpty()))
            <section class="cv-section">
                <h2 class="cv-main-title">Education</h2>
                @foreach(($thumbMode ? $thumbEducations->take(3) : $educations) as $edu)
                    <div class="cv-timeline-item">
                        <div class="cv-item-header">
                            <div class="cv-pos-title {{ $itemPlaceholder($edu, 'school') ? 'cv-placeholder' : '' }}">{{ data_get($edu, 'school') }}</div>
                            <span class="cv-date {{ $itemPlaceholder($edu, 'year') ? 'cv-placeholder' : '' }}">{{ data_get($edu, 'year') }}</span>
                        </div>
                        <div class="cv-company {{ $itemPlaceholder($edu, 'degree') ? 'cv-placeholder' : '' }}">{{ data_get($edu, 'degree') }}</div>
                    </div>
                @endforeach
            </section>
        @endif

        @if($hasExperiences || ($thumbMode && $thumbExperiences->isNotEmpty()))
            <section class="cv-section">
                <h2 class="cv-main-title">Work Experience</h2>
                @foreach(($thumbMode ? $thumbExperiences->take(4) : $experiences) as $exp)
                    <div class="cv-timeline-item">
                        <div class="cv-item-header">
                            <div class="cv-pos-title {{ $itemPlaceholder($exp, 'position') ? 'cv-placeholder' : '' }}">{{ data_get($exp, 'position') }}</div>
                            <span class="cv-date {{ ($itemPlaceholder($exp, 'start_date') || $itemPlaceholder($exp, 'end_date')) ? 'cv-placeholder' : '' }}">
                                {{ data_get($exp, 'start_date') }}{{ data_get($exp, 'end_date') ? ' - '.data_get($exp, 'end_date') : ' - Present' }}
                            </span>
                        </div>
                        <div class="cv-company {{ $itemPlaceholder($exp, 'company') ? 'cv-placeholder' : '' }}">{{ data_get($exp, 'company') }}</div>
                        @if(data_get($exp, 'description'))
                            <div class="cv-desc {{ $itemPlaceholder($exp, 'description') ? 'cv-placeholder' : '' }}">
                                {{ $thumbMode ? Str::limit((string) data_get($exp, 'description'), 220) : data_get($exp, 'description') }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </section>
        @endif

        @if($hasSkills || ($thumbMode && $thumbSkills->isNotEmpty()))
            <section class="cv-section">
                <h2 class="cv-main-title">Skills</h2>
                <div class="cv-skills-grid">
                    @foreach(($thumbMode ? $thumbSkills->take(8) : $skills) as $skill)
                        <span class="cv-skill-badge {{ $itemPlaceholder($skill, 'name') ? 'cv-placeholder' : '' }}">{{ data_get($skill, 'name') }}</span>
                    @endforeach
                </div>
            </section>
        @endif
    </main>
</div>
@endsection