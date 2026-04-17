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
@endphp

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Manrope:wght@400;500;600&display=swap');

/* --- VARIABLES & RESET --- */
.cv-paper {
    --ac: {{ $tone['accent'] }};
    --ac-deep: {{ $tone['deep'] }};
    --ac-soft: {{ $tone['soft'] }};
    --text-light: {{ $tone['text'] }};
}

* { box-sizing: border-box; }

.cv-wrapper {
    font-family: 'Manrope', sans-serif;
    display: grid;
    grid-template-columns: 280px 1fr; /* Lebar sidebar fix, konten fleksibel */
    min-height: 297mm;
    height: {{ $thumbMode ? '100%' : 'auto' }};
    background: #fff;
    color: #334155;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.cv-wrapper,
.cv-wrapper * {
    min-width: 0;
}

.cv-wrapper :where(h1, h2, h3, h4, p, span, div, li, a) {
    overflow-wrap: anywhere;
    word-break: break-word;
}

/* --- SIDEBAR (Left Column) --- */
.cv-sidebar {
    background: var(--ac-deep);
    color: var(--text-light);
    padding: 40px 24px;
    display: flex;
    flex-direction: column;
    gap: {{ $thumbMode ? '20px' : '30px' }};
    justify-content: {{ $thumbMode ? 'space-between' : 'flex-start' }};
    text-align: center;
    /* Anti-potong */
    break-inside: avoid;
}

/* Large Photo Style */
.cv-profile-img {
    width: 160px;
    height: 160px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid rgba(255,255,255,0.2);
    margin: 0 auto;
    box-shadow: 0 8px 16px rgba(0,0,0,0.2);
    background-color: rgba(255,255,255,0.1);
}

.cv-profile-fallback {
    width: 160px;
    height: 160px;
    border-radius: 50%;
    margin: 0 auto;
    border: 4px solid rgba(255,255,255,0.2);
    box-shadow: 0 8px 16px rgba(0,0,0,0.2);
    background: rgba(255,255,255,0.14);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Poppins', sans-serif;
    font-size: 38px;
    font-weight: 700;
    letter-spacing: 0.04em;
}

.cv-name {
    font-family: 'Poppins', sans-serif;
    font-size: 26px;
    font-weight: 700;
    line-height: 1.2;
    margin: 0;
}

.cv-role {
    font-family: 'Poppins', sans-serif;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 1px;
    opacity: 0.8;
    margin-top: 4px;
    font-weight: 600;
}

/* Sidebar Sections */
.cv-side-section {
    text-align: left;
    width: 100%;
    margin-top: 10px;
}

.cv-side-title {
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 600;
    border-bottom: 1px solid rgba(255,255,255,0.2);
    padding-bottom: 8px;
    margin-bottom: 16px;
}

.cv-contact-item {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 12px;
    margin-bottom: 12px;
    word-break: break-word;
}

.cv-contact-icon {
    width: 24px;
    height: 24px;
    background: rgba(255,255,255,0.15);
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-weight: bold;
    font-size: 10px;
}

.cv-about-text {
    font-size: 12px;
    line-height: 1.6;
    opacity: 0.9;
    text-align: justify;
}

/* --- MAIN CONTENT (Right Column) --- */
.cv-main {
    padding: 40px 50px;
    display: flex;
    flex-direction: column;
    justify-content: {{ $thumbMode ? 'space-between' : 'flex-start' }};
    gap: {{ $thumbMode ? '18px' : '0' }};
}

.cv-main > .cv-section {
    margin-bottom: 0;
}

.cv-main-title {
    font-family: 'Poppins', sans-serif;
    font-size: 18px;
    color: var(--ac-deep);
    text-transform: uppercase;
    font-weight: 700;
    margin-bottom: 20px;
    position: relative;
    padding-left: 16px;
}

.cv-main-title::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 6px;
    height: 24px;
    background: var(--ac);
    border-radius: 3px;
}

/* Experience & Education Items */
.cv-timeline-item {
    position: relative;
    padding-left: 20px;
    margin-bottom: 24px;
    border-left: 2px solid var(--ac-soft);
}

.cv-timeline-item::before {
    content: '';
    position: absolute;
    left: -6px;
    top: 6px;
    width: 10px;
    height: 10px;
    background: #fff;
    border: 2px solid var(--ac);
    border-radius: 50%;
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
    font-size: 15px;
    font-weight: 700;
    color: #1e293b;
}

.cv-date {
    font-size: 11px;
    font-weight: 600;
    color: var(--ac-deep);
    background: var(--ac-soft);
    padding: 4px 8px;
    border-radius: 4px;
    white-space: normal;
    max-width: 100%;
    text-align: right;
}

.cv-company {
    font-size: 13px;
    color: #64748b;
    font-weight: 600;
    margin-bottom: 6px;
}

.cv-desc {
    font-size: 13px;
    line-height: 1.6;
    color: #475569;
}

/* Skills Grid */
.cv-skills-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.cv-skill-badge {
    font-size: 11px;
    font-weight: 600;
    color: var(--ac-deep);
    border: 1px solid var(--ac);
    padding: 6px 12px;
    border-radius: 20px;
    background: #fff;
}

/* Utilities */
.cv-placeholder { opacity: 0.5; font-style: italic; }

/* Responsive */
@media screen and (max-width: {{ $thumbMode ? '0px' : '768px' }}) {
    .cv-wrapper { grid-template-columns: 1fr; display: flex; flex-direction: column; }
    .cv-sidebar { padding: 30px 20px; text-align: center; align-items: center; }
    .cv-side-section { text-align: center; }
    .cv-main { padding: 30px 20px; }
}
</style>

<div class="cv-wrapper">
    
    <!-- LEFT SIDEBAR (Photo, Contact, About) -->
    <aside class="cv-sidebar">
        @if($photo)
            <img src="{{ $photo }}" alt="Profile" class="cv-profile-img">
        @else
            <div class="cv-profile-fallback">{{ $initials ?: 'CV' }}</div>
        @endif
        
        <div>
            <h1 class="cv-name {{ $isPlaceholder('personal_name') ? 'cv-placeholder' : '' }}">{{ $thumbMode ? Str::limit($name, 30) : $name }}</h1>
            @if($title)
                <div class="cv-role {{ $isPlaceholder('title') ? 'cv-placeholder' : '' }}">{{ $title }}</div>
            @endif
        </div>

        @if($hasContact || $hasSummary)
            <!-- About Me Section -->
            @if($hasSummary)
                <div class="cv-side-section">
                    <h3 class="cv-side-title">About Me</h3>
                    <p class="cv-about-text {{ $isPlaceholder('summary') ? 'cv-placeholder' : '' }}">
                        {{ $thumbMode ? Str::limit($summary, 240) : $summary }}
                    </p>
                </div>
            @endif

            <!-- Contact Section -->
            @if($hasContact)
                <div class="cv-side-section">
                    <h3 class="cv-side-title">Contact</h3>
                    @if($email)
                        <div class="cv-contact-item {{ $isPlaceholder('personal_email') ? 'cv-placeholder' : '' }}">
                            <div class="cv-contact-icon">@</div>
                            <span>{{ $email }}</span>
                        </div>
                    @endif
                    @if($phone)
                        <div class="cv-contact-item">
                            <div class="cv-contact-icon">P</div>
                            <span>{{ $phone }}</span>
                        </div>
                    @endif
                    @if($location)
                        <div class="cv-contact-item">
                            <div class="cv-contact-icon">L</div>
                            <span>{{ $location }}</span>
                        </div>
                    @endif
                    @if($linkedin)
                        <div class="cv-contact-item">
                            <div class="cv-contact-icon">in</div>
                            <span>{{ $linkedin }}</span>
                        </div>
                    @endif
                    @if($website)
                        <div class="cv-contact-item">
                            <div class="cv-contact-icon">W</div>
                            <span>{{ $website }}</span>
                        </div>
                    @endif
                </div>
            @endif
        @endif
    </aside>

    <!-- MAIN CONTENT (Edu, Exp, Skills) -->
    <main class="cv-main">
        
        <!-- Education (Top Right in Ref) -->
        @if($hasEducations)
            <section class="cv-section">
                <h2 class="cv-main-title">Education</h2>
                @foreach($educations as $edu)
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

        <!-- Experience -->
        @if($hasExperiences)
            <section class="cv-section">
                <h2 class="cv-main-title">Work Experience</h2>
                @foreach(($thumbMode ? $experiences->take(4) : $experiences) as $exp)
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
                                {{ $thumbMode ? Str::limit((string) data_get($exp, 'description'), 250) : data_get($exp, 'description') }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </section>
        @endif

        <!-- Skills (Bottom Right in Ref) -->
        @if($hasSkills)
            <section class="cv-section">
                <h2 class="cv-main-title">Skills</h2>
                <div class="cv-skills-grid">
                    @foreach($skills as $skill)
                        <span class="cv-skill-badge {{ $itemPlaceholder($skill, 'name') ? 'cv-placeholder' : '' }}">{{ data_get($skill, 'name') }}</span>
                    @endforeach
                </div>
            </section>
        @endif

    </main>
</div>
@endsection