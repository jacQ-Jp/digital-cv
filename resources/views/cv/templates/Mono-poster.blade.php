@extends($layout ?? 'layouts.render')

@section('content')
@php
    // VARIABEL DATA
    $name = data_get($cv, 'personal_name') ?: (data_get($cv, 'user.name') ?? 'Henry Madison');
    $email = data_get($cv, 'personal_email') ?: (data_get($cv, 'user.email') ?? 'info@yourmail.com');
    $title = data_get($cv, 'title') ?: 'Graphic Designer';
    $summary = data_get($cv, 'summary');
    $phone = data_get($cv, 'personal_phone') ?: '+0000 1234 5678';
    $location = data_get($cv, 'personal_location') ?: 'New York City - 000';
    $linkedin = data_get($cv, 'personal_linkedin');
    $website = data_get($cv, 'personal_website');
    $thumbMode = ($layout === 'layouts.thumb');

    $photoPreview = trim((string) data_get($cv, 'photo_preview_url'));
    $photoPath = trim((string) data_get($cv, 'photo_path'));
    if ($photoPreview !== '') {
        $photo = $photoPreview;
    } elseif ($photoPath !== '') {
        if (
            preg_match('/^(https?:)?\/\//i', $photoPath)
            || str_starts_with($photoPath, 'data:')
            || str_starts_with($photoPath, 'blob:')
            || str_starts_with($photoPath, '/storage/')
        ) {
            $photo = $photoPath;
        } else {
            $normalizedPhotoPath = ltrim($photoPath, '/');
            $photo = str_starts_with($normalizedPhotoPath, 'storage/')
                ? '/'.$normalizedPhotoPath
                : '/storage/'.$normalizedPhotoPath;
        }
    } else {
        $photo = null;
    }

    $nameParts = preg_split('/\s+/', trim((string) $name)) ?: [];
    $initials = collect($nameParts)
        ->filter()
        ->map(fn ($part) => strtoupper((string) \Illuminate\Support\Str::substr($part, 0, 1)))
        ->take(2)
        ->join('');

    $accent = '#000000'; 
    $themes = [
        '#000000' => ['accent' => '#000000', 'deep' => '#000000', 'soft' => '#f3f3f3'],
    ];
    $tone = $themes['#000000'];

    $experiences = collect(data_get($cv, 'experiences', []));
    $educations = collect(data_get($cv, 'educations', []));
    $skills = collect(data_get($cv, 'skills', []));
    // Asumsi ada field languages di DB, jika tidak gunakan array kosong
    $languages = collect(data_get($cv, 'languages', []));

    $placeholderFlags = collect(data_get($cv, 'preview_placeholder_flags', []));
    $isPlaceholder = fn (string $key): bool => (bool) $placeholderFlags->get($key, false);
    $itemPlaceholder = fn ($item, string $field): bool => (bool) data_get($item, '_placeholder.'.$field, false);
@endphp

<!-- Load Icons untuk Social Media & UI -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<style>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Lato:wght@300;400;700&display=swap');

.cv-paper {
    --ac: {{ $tone['accent'] }};
    --ac-deep: {{ $tone['deep'] }};
    --ac-soft: {{ $tone['soft'] }};
    --font-head: 'Montserrat', sans-serif;
    --font-body: 'Lato', sans-serif;
}

.cv-henry {
    font-family: var(--font-body);
    color: #333;
    font-size: 14px;
    line-height: 1.6;
    display: grid;
    grid-template-columns: 32% 1fr; /* 32% Sidebar Kiri */
    min-height: {{ $thumbMode ? '100%' : '1123px' }};
    background: #fff;
}

.cv-henry,
.cv-henry * {
    min-width: 0;
}

.cv-henry :where(h1, h2, h3, h4, p, span, div, li, a) {
    overflow-wrap: anywhere;
    word-break: break-word;
}

/* --- LEFT SIDEBAR (GELAP) --- */
.cv-sidebar-left {
    background-color: var(--ac-deep);
    color: #fff;
    padding: {{ $thumbMode ? '30px 20px' : '50px 30px' }};
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: visible;
}

.cv-henry-photo-wrap {
    display: flex;
    justify-content: center;
    margin-bottom: 26px;
}

.cv-henry-photo {
    width: {{ $thumbMode ? '112px' : '132px' }};
    height: {{ $thumbMode ? '112px' : '132px' }};
    border-radius: 999px;
    object-fit: cover;
    border: 4px solid rgba(255, 255, 255, 0.45);
    box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.12);
    background: rgba(255, 255, 255, 0.08);
}

.cv-henry-photo-fallback {
    width: {{ $thumbMode ? '112px' : '132px' }};
    height: {{ $thumbMode ? '112px' : '132px' }};
    border-radius: 999px;
    border: 4px solid rgba(255, 255, 255, 0.45);
    box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.12);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-family: var(--font-head);
    font-size: {{ $thumbMode ? '30px' : '36px' }};
    font-weight: 700;
    color: rgba(255, 255, 255, 0.9);
    background: rgba(255, 255, 255, 0.08);
}

/* Nama Besar di Kiri */
.cv-henry-name {
    font-family: var(--font-head);
    font-size: {{ $thumbMode ? '32px' : '42px' }};
    font-weight: 700;
    line-height: 1.1;
    margin-bottom: 5px;
    text-transform: uppercase;
    max-width: 100%;
    white-space: normal;
    overflow-wrap: normal;
    word-break: normal;
    hyphens: none;
}

.cv-henry-role {
    font-family: var(--font-head);
    font-size: 14px;
    font-weight: 400;
    text-transform: uppercase;
    letter-spacing: 2px;
    margin-bottom: 30px;
    color: rgba(255,255,255,0.8);
    white-space: normal;
    overflow-wrap: normal;
    word-break: normal;
}

/* Social Icons */
.cv-socials {
    display: flex;
    gap: 15px;
    margin-bottom: 40px;
}

.cv-socials a {
    color: #fff;
    font-size: 20px;
    text-decoration: none;
    transition: color 0.3s;
}

.cv-socials a:hover {
    color: var(--ac-soft);
}

/* Section Headers di Sidebar */
.cv-sidebar-section-title {
    font-family: var(--font-head);
    font-size: 16px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid rgba(255,255,255,0.2);
}

/* Contact Items */
.cv-contact-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 15px;
    font-size: 13px;
}

.cv-contact-item i {
    font-size: 16px;
    color: var(--ac-soft); /* Icon pakai warna soft/terang */
    margin-top: 2px;
}

.cv-contact-text {
    word-break: break-word;
}

/* Language List */
.cv-lang-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.cv-lang-item {
    display: flex;
    justify-content: space-between;
    font-size: 13px;
}

.cv-lang-bar-bg {
    width: 60%;
    height: 4px;
    background: rgba(255,255,255,0.2);
    border-radius: 2px;
    margin-top: 6px;
}

.cv-lang-bar-fill {
    height: 100%;
    background: #fff;
    border-radius: 2px;
}


/* --- RIGHT MAIN CONTENT (PUTIH) --- */
.cv-main-right {
    padding: {{ $thumbMode ? '30px 35px' : '50px 40px' }};
    background: #ffffff;
}

/* Section Headers di Kanan */
.cv-main-section-title {
    font-family: var(--font-head);
    font-size: 20px;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--ac-deep);
    margin-bottom: 25px;
    margin-top: {{ $thumbMode ? '25px' : '40px' }}; /* Jarak antar section */
    border-bottom: 3px solid var(--ac-soft);
    padding-bottom: 10px;
    display: block;
    width: 100%;
}

.cv-main-section-title:first-child {
    margin-top: 0;
}

/* Profile Text */
.cv-profile-text {
    font-size: 14px;
    color: #4b5563;
    line-height: 1.8;
}

/* Experience & Education Items */
.cv-exp-item {
    margin-bottom: 25px;
}

.cv-exp-header {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    flex-wrap: wrap;
    row-gap: 6px;
    margin-bottom: 5px;
}

.cv-exp-role {
    font-family: var(--font-head);
    font-size: 16px;
    font-weight: 700;
    color: #1f2937;
}

.cv-exp-date {
    font-size: 13px;
    color: var(--ac-deep);
    font-weight: 600;
    font-style: italic;
    text-align: right;
    max-width: 100%;
}

.cv-exp-sub {
    font-size: 14px;
    color: #6b7280;
    font-weight: 600;
    margin-bottom: 8px;
}

.cv-exp-desc {
    font-size: 13px;
    color: #4b5563;
    line-height: 1.6;
}

/* Expertise / Skills Tags */
.cv-expertise-list {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.cv-skill-tag {
    background: var(--ac-soft);
    color: var(--ac-deep);
    padding: 6px 14px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
    font-family: var(--font-head);
    text-transform: uppercase;
}

/* Placeholder Helper */
.cv-henry .cv-placeholder { opacity: 0.4; font-style: italic; }

/* Mobile Responsive */
@media screen and (max-width: {{ $thumbMode ? '0px' : '760px' }}) {
    .cv-henry {
        grid-template-columns: 1fr;
    }
    .cv-sidebar-left {
        padding: 30px 20px;
        text-align: center;
    }
    .cv-contact-item {
        justify-content: center;
    }
    .cv-lang-item {
        justify-content: center;
    }
    .cv-socials {
        justify-content: center;
    }
    .cv-main-right {
        padding: 30px 20px;
    }
}
</style>

<div class="cv-henry">
    
    <!-- SIDEBAR KIRI (GELAP) -->
    <aside class="cv-sidebar-left">
        <div class="cv-henry-photo-wrap">
            @if($photo)
                <img src="{{ $photo }}" alt="Photo" class="cv-henry-photo">
            @else
                <div class="cv-henry-photo-fallback">{{ $initials !== '' ? $initials : 'CV' }}</div>
            @endif
        </div>

        <!-- Nama & Role -->
        <h1 class="cv-henry-name {{ $isPlaceholder('personal_name') ? 'cv-placeholder' : '' }}">{{ $thumbMode ? Str::limit($name, 20) : $name }}</h1>
        <div class="cv-henry-role {{ $isPlaceholder('title') ? 'cv-placeholder' : '' }}">{{ $title }}</div>

        <!-- Social Icons -->
        <div class="cv-socials">
            @if($linkedin)
                <a href="{{ $linkedin }}" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
            @endif
            @if($website)
                <a href="{{ $website }}" title="Website"><i class="bi bi-globe"></i></a>
            @endif
            @if(!$linkedin && !$website)
                <!-- Placeholder Icons -->
                <span class="cv-placeholder"><i class="bi bi-linkedin"></i></span>
                <span class="cv-placeholder"><i class="bi bi-behance"></i></span>
                <span class="cv-placeholder"><i class="bi bi-dribbble"></i></span>
            @endif
        </div>

        <!-- Contact Section -->
        <div class="cv-sidebar-section-title">Contact</div>
        @if($email)
        <div class="cv-contact-item">
            <i class="bi bi-envelope"></i>
            <div class="cv-contact-text {{ $isPlaceholder('personal_email') ? 'cv-placeholder' : '' }}">{{ $email }}</div>
        </div>
        @endif
        @if($phone)
        <div class="cv-contact-item">
            <i class="bi bi-telephone"></i>
            <div class="cv-contact-text">{{ $phone }}</div>
        </div>
        @endif
        @if($location)
        <div class="cv-contact-item">
            <i class="bi bi-geo-alt"></i>
            <div class="cv-contact-text">{{ $location }}</div>
        </div>
        @endif

        <!-- Languages Section -->
        <div class="cv-sidebar-section-title" style="margin-top: 40px;">Languages</div>
        <div class="cv-lang-list">
            <!-- Menggunakan data languages jika ada, atau placeholder -->
            @if($languages->isNotEmpty())
                @foreach($languages as $lang)
                    <div class="cv-lang-item">
                        <span>{{ data_get($lang, 'name') }}</span>
                    </div>
                @endforeach
            @else
                <!-- Placeholder data sesuai request (English, Urdu, Spanish) -->
                <div class="cv-lang-item">
                    <span>English</span>
                </div>
                <div class="cv-lang-item">
                    <span>Urdu</span>
                </div>
                <div class="cv-lang-item">
                    <span>Spanish</span>
                </div>
            @endif
        </div>
    </aside>

    <!-- MAIN KANAN (PUTIH) -->
    <main class="cv-main-right">
        
        <!-- Profile Section -->
        @if($summary)
        <h2 class="cv-main-section-title">Profile</h2>
        <p class="cv-profile-text {{ $isPlaceholder('summary') ? 'cv-placeholder' : '' }}">
            {{ $thumbMode ? Str::limit((string) $summary, 300) : $summary }}
        </p>
        @endif

        <!-- Experience Section -->
        <h2 class="cv-main-section-title">Experience</h2>
        @foreach(($thumbMode ? $experiences->take(3) : $experiences) as $exp)
            <article class="cv-exp-item">
                <div class="cv-exp-header">
                    <div class="cv-exp-role {{ $itemPlaceholder($exp, 'position') ? 'cv-placeholder' : '' }}">
                        {{ data_get($exp, 'position') }}
                    </div>
                    <div class="cv-exp-date {{ ($itemPlaceholder($exp, 'start_date') || $itemPlaceholder($exp, 'end_date')) ? 'cv-placeholder' : '' }}">
                        {{ data_get($exp, 'start_date') }} {{ data_get($exp, 'end_date') ? '- ' . data_get($exp, 'end_date') : '- Present' }}
                    </div>
                </div>
                <div class="cv-exp-sub {{ $itemPlaceholder($exp, 'company') ? 'cv-placeholder' : '' }}">
                    {{ data_get($exp, 'company') }}
                </div>
                @if(data_get($exp, 'description'))
                <div class="cv-exp-desc {{ $itemPlaceholder($exp, 'description') ? 'cv-placeholder' : '' }}">
                    {{ $thumbMode ? Str::limit((string) data_get($exp, 'description'), 200) : data_get($exp, 'description') }}
                </div>
                @endif
            </article>
        @endforeach

        <!-- Education Section -->
        <h2 class="cv-main-section-title">Education</h2>
        @foreach($educations as $edu)
            <article class="cv-exp-item">
                <div class="cv-exp-header">
                    <div class="cv-exp-role {{ $itemPlaceholder($edu, 'school') ? 'cv-placeholder' : '' }}">
                        {{ data_get($edu, 'school') }}
                    </div>
                    <div class="cv-exp-date {{ $itemPlaceholder($edu, 'year') ? 'cv-placeholder' : '' }}">
                        {{ data_get($edu, 'year') }}
                    </div>
                </div>
                <div class="cv-exp-sub {{ $itemPlaceholder($edu, 'degree') ? 'cv-placeholder' : '' }}">
                    {{ data_get($edu, 'degree') }}
                </div>
            </article>
        @endforeach

        <!-- Expertise Section (Skills) -->
        <h2 class="cv-main-section-title">Expertise</h2>
        <div class="cv-expertise-list">
            @foreach($skills as $skill)
                <span class="cv-skill-tag {{ $itemPlaceholder($skill, 'name') ? 'cv-placeholder' : '' }}">
                    {{ data_get($skill, 'name') }}
                </span>
            @endforeach
            <!-- Fallback jika kosong, agar tampilan sesuai request -->
            @if($skills->isEmpty())
                <span class="cv-skill-tag cv-placeholder">Premiere Pro</span>
                <span class="cv-skill-tag cv-placeholder">After Effect</span>
                <span class="cv-skill-tag cv-placeholder">Photoshop</span>
                <span class="cv-skill-tag cv-placeholder">Illustrator</span>
            @endif
        </div>

    </main>
</div>
@endsection