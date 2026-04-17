@extends($layout ?? 'layouts.render')

@section('content')
@php
    // === DATA EXTRACTION ===
    $name = data_get($cv, 'personal_name') ?? data_get($cv, 'user.name') ?? 'CV';
    $email = data_get($cv, 'personal_email') ?? data_get($cv, 'user.email') ?? '';
    $title = data_get($cv, 'title');
    $summary = data_get($cv, 'summary');
    $phone = data_get($cv, 'personal_phone');
    $location = data_get($cv, 'personal_location');
    $linkedin = data_get($cv, 'personal_linkedin');
    $website = data_get($cv, 'personal_website');
    $thumbMode = ($layout === 'layouts.thumb');

    // === PHOTO HANDLING ===
    $photo = data_get($cv, 'photo_preview_url') ?? (data_get($cv, 'photo_path') ? asset('storage/'.data_get($cv, 'photo_path')) : null);
    
    // Generate Initials
    $initials = collect(explode(' ', $name))
        ->filter()
        ->map(fn ($word) => strtoupper(substr($word, 0, 1)))
        ->take(2)
        ->join('');

    // === THEME CONFIGURATION ===
    $accent = strtoupper((string) data_get($cv, 'accent_color', '#334155'));
    $themes = [
        '#7C3AED' => ['accent' => '#7C3AED', 'deep' => '#4C1D95', 'soft' => '#EDE9FE'],
        '#0EA5A4' => ['accent' => '#0EA5A4', 'deep' => '#0F766E', 'soft' => '#CCFBF1'],
        '#3B82F6' => ['accent' => '#3B82F6', 'deep' => '#1D4ED8', 'soft' => '#DBEAFE'],
        '#EA580C' => ['accent' => '#EA580C', 'deep' => '#C2410C', 'soft' => '#FFEDD5'],
        '#334155' => ['accent' => '#334155', 'deep' => '#0F172A', 'soft' => '#E2E8F0'],
        '#166534' => ['accent' => '#166534', 'deep' => '#14532D', 'soft' => '#DCFCE7'],
        '#BE123C' => ['accent' => '#BE123C', 'deep' => '#9F1239', 'soft' => '#FFE4E6'],
    ];
    $tone = $themes[$accent] ?? $themes['#334155'];

    // === DATA COLLECTIONS ===
    $experiences = collect(data_get($cv, 'experiences', []));
    $educations = collect(data_get($cv, 'educations', []));
    $skills = collect(data_get($cv, 'skills', []));

    // === PLACEHOLDER FLAGS ===
    $placeholderFlags = collect(data_get($cv, 'preview_placeholder_flags', []));
    $isPlaceholder = fn (string $key): bool => (bool) $placeholderFlags->get($key, false);
    $itemPlaceholder = fn ($item, string $field): bool => (bool) data_get($item, '_placeholder.'.$field, false);
@endphp

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Open+Sans:wght@400;600&display=swap');

    .cv-paper {
        --ac: {{ $tone['accent'] }};
        --ac-deep: {{ $tone['deep'] }};
        --ac-soft: {{ $tone['soft'] }};
        background-color: #f1f5f9;
    }

    .cv-modern {
        font-family: 'Open Sans', sans-serif;
        color: #333;
        font-size: 11px;
        line-height: 1.6;
        max-width: 210mm;
        margin: 0 auto;
        background: #fff;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        min-height: 297mm;
        display: flex;
        overflow: hidden;
    }

    .cv-modern .cv-placeholder { opacity: .5; font-style: italic; }

    .cv-modern,
    .cv-modern * {
        min-width: 0;
    }

    .cv-modern :where(h1, h2, h3, h4, p, span, div, li, a) {
        overflow-wrap: anywhere;
        word-break: break-word;
    }

    /* === SIDEBAR (Left) === */
    .cv-sidebar {
        width: 32%;
        background-color: var(--ac-deep);
        color: #fff;
        padding: 30px 20px;
        display: flex;
        flex-direction: column;
        gap: 35px;
    }

    .cv-photo-container {
        width: 130px;
        height: 130px;
        margin: 0 auto;
        border: 4px solid rgba(255,255,255,0.3);
        border-radius: 50%;
        overflow: hidden;
    }

    .cv-photo { width: 100%; height: 100%; object-fit: cover; }

    .cv-photo-fallback {
        width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;
        font-family: 'Poppins', sans-serif; font-size: 40px; font-weight: 700; color: rgba(255,255,255,0.8);
        background: rgba(0,0,0,0.2);
    }

    .cv-sidebar-heading {
        font-family: 'Poppins', sans-serif;
        font-size: 14px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        border-bottom: 2px solid rgba(255,255,255,0.3);
        padding-bottom: 8px;
        margin-bottom: 15px;
        margin-top: 0;
    }

    .cv-contact-item { margin-bottom: 12px; display: flex; align-items: flex-start; gap: 10px; }
    .cv-contact-item b { display: block; font-weight: 600; font-size: 10px; opacity: 0.8; text-transform: uppercase; }
    .cv-contact-item span { display: block; font-size: 11px; }

    /* Skills in Sidebar */
    .cv-skill-tag {
        display: inline-block;
        background: rgba(255,255,255,0.1);
        padding: 4px 8px;
        border-radius: 4px;
        margin: 0 5px 5px 0;
        font-size: 10px;
    }

    /* Education in Sidebar */
    .cv-edu-item { margin-bottom: 15px; }
    .cv-edu-school { font-weight: 700; display: block; font-size: 11px; }
    .cv-edu-degree { display: block; font-size: 10px; opacity: 0.8; margin-bottom: 2px; }
    .cv-edu-year { display: block; font-size: 9px; font-style: italic; opacity: 0.7; }

    /* === MAIN CONTENT (Right) === */
    .cv-main {
        width: 68%;
        padding: 40px;
        background: #fff;
    }

    /* Header */
    .cv-header-name {
        font-family: 'Poppins', sans-serif;
        font-size: 36px;
        font-weight: 700;
        color: var(--ac-deep);
        margin: 0 0 5px 0;
        line-height: 1.1;
        text-transform: uppercase;
    }

    .cv-header-title {
        font-family: 'Poppins', sans-serif;
        font-size: 16px;
        font-weight: 600;
        color: var(--ac);
        margin-bottom: 25px;
        letter-spacing: 0.5px;
    }

    .cv-section { margin-bottom: 30px; }

    .cv-main-heading {
        font-family: 'Poppins', sans-serif;
        font-size: 14px;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--ac);
        border-bottom: 2px solid var(--ac-soft);
        padding-bottom: 6px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
    }
    
    .cv-main-heading::before {
        content: '';
        display: inline-block;
        width: 8px; height: 8px;
        background: var(--ac);
        margin-right: 10px;
        transform: rotate(45deg);
    }

    .cv-profile-text { font-size: 11px; color: #475569; text-align: justify; }

    /* Timeline Experience */
    .cv-job { position: relative; padding-left: 20px; border-left: 1px solid #e2e8f0; margin-bottom: 25px; padding-bottom: 25px; }
    .cv-job:last-child { border-left: none; padding-bottom: 0; }
    
    .cv-job::before {
        content: ''; position: absolute; left: -5px; top: 2px;
        width: 9px; height: 9px; background: var(--ac); border-radius: 50%;
        border: 2px solid #fff; box-shadow: 0 0 0 2px var(--ac-soft);
    }

    .cv-job-header { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 4px; }
    .cv-job-title { font-family: 'Poppins', sans-serif; font-size: 13px; font-weight: 700; color: #1e293b; margin: 0; }
    .cv-job-date { font-size: 10px; font-weight: 600; color: var(--ac); background: var(--ac-soft); padding: 2px 6px; border-radius: 3px; max-width: 100%; text-align: right; }
    .cv-job-company { font-weight: 600; font-size: 11px; color: #64748b; margin-bottom: 8px; }
    .cv-job-desc { font-size: 11px; color: #475569; text-align: justify; }

    .cv-job-header { flex-wrap: wrap; row-gap: 6px; }

    @media screen and (max-width: {{ $thumbMode ? '0px' : '760px' }}) {
        .cv-modern { flex-direction: column; }
        .cv-sidebar { width: 100%; padding: 30px; }
        .cv-main { width: 100%; padding: 30px; }
        .cv-job { border-left: none; padding-left: 0; }
        .cv-job::before { display: none; }
    }
</style>

<div class="cv-modern">
    <!-- SIDEBAR SECTION -->
    <aside class="cv-sidebar">
        <!-- Photo -->
        <div class="cv-photo-container">
            @if($photo)
                <img src="{{ $photo }}" alt="Photo" class="cv-photo">
            @else
                <div class="cv-photo-fallback">{{ $initials ?: 'CV' }}</div>
            @endif
        </div>

        <!-- Contact Info -->
        <div>
            <h3 class="cv-sidebar-heading">Contact</h3>
            @if($email)<div class="cv-contact-item"><b>Email</b><span>{{ $email }}</span></div>@endif
            @if($phone)<div class="cv-contact-item"><b>Phone</b><span>{{ $phone }}</span></div>@endif
            @if($location)<div class="cv-contact-item"><b>Address</b><span>{{ $location }}</span></div>@endif
            @if($linkedin)<div class="cv-contact-item"><b>LinkedIn</b><span>{{ $linkedin }}</span></div>@endif
            @if($website)<div class="cv-contact-item"><b>Website</b><span>{{ $website }}</span></div>@endif
        </div>

        <!-- Skills -->
        @if($skills->isNotEmpty())
        <div>
            <h3 class="cv-sidebar-heading">Skills</h3>
            @foreach($skills as $skill)
                <span class="cv-skill-tag {{ $itemPlaceholder($skill, 'name') ? 'cv-placeholder' : '' }}">{{ data_get($skill, 'name') }}</span>
            @endforeach
        </div>
        @endif

        <!-- Education -->
        @if($educations->isNotEmpty())
        <div>
            <h3 class="cv-sidebar-heading">Education</h3>
            @foreach($educations as $edu)
                <div class="cv-edu-item">
                    <span class="cv-edu-school {{ $itemPlaceholder($edu, 'school') ? 'cv-placeholder' : '' }}">{{ data_get($edu, 'school') }}</span>
                    <span class="cv-edu-degree {{ $itemPlaceholder($edu, 'degree') ? 'cv-placeholder' : '' }}">{{ data_get($edu, 'degree') }}</span>
                    <span class="cv-edu-year {{ $itemPlaceholder($edu, 'year') ? 'cv-placeholder' : '' }}">{{ data_get($edu, 'year') }}</span>
                </div>
            @endforeach
        </div>
        @endif
    </aside>

    <!-- MAIN CONTENT SECTION -->
    <main class="cv-main">
        <!-- Header -->
        <header>
            <h1 class="cv-header-name {{ $isPlaceholder('personal_name') ? 'cv-placeholder' : '' }}">
                {{ $thumbMode ? Str::limit($name, 38) : $name }}
            </h1>
            @if($title)
                <p class="cv-header-title {{ $isPlaceholder('title') ? 'cv-placeholder' : '' }}">{{ $title }}</p>
            @endif
        </header>

        <!-- Profile Summary -->
        @if($summary)
        <section class="cv-section">
            <h3 class="cv-main-heading">Profile</h3>
            <div class="cv-profile-text {{ $isPlaceholder('summary') ? 'cv-placeholder' : '' }}">{{ $summary }}</div>
        </section>
        @endif

        <!-- Work Experience -->
        @if($experiences->isNotEmpty())
        <section class="cv-section">
            <h3 class="cv-main-heading">Work Experience</h3>
            @foreach(($thumbMode ? $experiences->take(4) : $experiences) as $job)
                <div class="cv-job">
                    <div class="cv-job-header">
                        <h4 class="cv-job-title {{ $itemPlaceholder($job, 'position') ? 'cv-placeholder' : '' }}">
                            {{ data_get($job, 'position') }}
                        </h4>
                        <span class="cv-job-date {{ ($itemPlaceholder($job, 'start_date') || $itemPlaceholder($job, 'end_date')) ? 'cv-placeholder' : '' }}">
                            {{ data_get($job, 'start_date') }}{{ data_get($job, 'end_date') ? ' - '.data_get($job, 'end_date') : ' - Present' }}
                        </span>
                    </div>
                    <div class="cv-job-company {{ $itemPlaceholder($job, 'company') ? 'cv-placeholder' : '' }}">
                        {{ data_get($job, 'company') }}
                    </div>
                    @if(data_get($job, 'description'))
                        <div class="cv-job-desc {{ $itemPlaceholder($job, 'description') ? 'cv-placeholder' : '' }}">
                            {{ $thumbMode ? Str::limit((string) data_get($job, 'description'), 230) : data_get($job, 'description') }}
                        </div>
                    @endif
                </div>
            @endforeach
        </section>
        @endif
    </main>
</div>
@endsection