@extends($layout ?? 'layouts.render')

@section('content')
@php
    $name = data_get($cv, 'personal_name') ?: (data_get($cv, 'user.name') ?? 'BENJAMIN TAYLOR');
    $email = data_get($cv, 'personal_email') ?: (data_get($cv, 'user.email') ?? 'info@benjamintaylor.com');
    $title = data_get($cv, 'title') ?: 'JOB TITLE GOES HERE';
    $summary = data_get($cv, 'summary');
    $phone = data_get($cv, 'personal_phone');
    $location = data_get($cv, 'personal_location');
    $linkedin = data_get($cv, 'personal_linkedin');
    $website = data_get($cv, 'personal_website');
    $thumbMode = ($layout === 'layouts.thumb');

    $photoPreview = data_get($cv, 'photo_preview_url');
    $photoPath = data_get($cv, 'photo_path');
    $photo = $photoPreview ?: ($photoPath ? asset('storage/'.$photoPath) : null);

    // B&W Theme: Hanya menggunakan Hitam, Putih, dan Abu-abu
    $accent = '#000000'; 
    $themes = [
        '#000000' => ['accent' => '#000000', 'deep' => '#000000', 'soft' => '#f3f3f3'],
    ];
    $tone = $themes['#000000'];

    $experiences = collect(data_get($cv, 'experiences', []));
    $educations = collect(data_get($cv, 'educations', []));
    $skills = collect(data_get($cv, 'skills', []));

    $placeholderFlags = collect(data_get($cv, 'preview_placeholder_flags', []));
    $isPlaceholder = fn (string $key): bool => (bool) $placeholderFlags->get($key, false);
    $itemPlaceholder = fn ($item, string $field): bool => (bool) data_get($item, '_placeholder.'.$field, false);
@endphp

<style>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Open+Sans:wght@400;600&display=swap');

.cv-paper {
    --ac: {{ $tone['accent'] }};
    --ac-deep: {{ $tone['deep'] }};
    --ac-soft: {{ $tone['soft'] }};
    --font-head: 'Montserrat', sans-serif;
    --font-body: 'Open Sans', sans-serif;
}

.cv-sidebar {
    font-family: var(--font-body);
    color: #111;
    font-size: 14px;
    line-height: 1.6;
    display: grid;
    grid-template-columns: 30% 1fr; /* Lebar Sidebar 30% */
    min-height: {{ $thumbMode ? '100%' : '1123px' }};
    background: #ffffff;
}

.cv-sidebar,
.cv-sidebar * {
    min-width: 0;
}

.cv-sidebar :where(h1, h2, h3, h4, p, span, div, li, a) {
    overflow-wrap: anywhere;
    word-break: break-word;
}

.cv-sidebar .cv-placeholder { opacity: .4; font-style: italic; }

/* --- SIDEBAR KIRI (BLACK) --- */
.cv-side {
    background-color: #000000; /* Hitam Pekat */
    color: #ffffff;
    padding: {{ $thumbMode ? '30px 20px' : '50px 30px' }};
    display: flex;
    flex-direction: column;
    align-items: center; /* Center align content */
    text-align: center;
    height: 100%;
}

/* Foto Profile */
.cv-side-photo {
    width: 140px;
    height: 140px;
    border-radius: 50%; /* Lingkaran / Bulat */
    object-fit: cover;
    border: 4px solid #ffffff; /* Border Putih tebal */
    margin-bottom: 25px;
    background-color: #333;
    box-shadow: 0 0 0 1px rgba(255,255,255,0.1); /* Halo effect halus */
}

/* Nama & Judul di Sidebar (Jika ingin nama disamping foto, tapi di model B&W biasanya nama di Kanan atau Bawah Foto) 
   Sesuai request: "BENJAMIN TAYLOR" di sidebar? 
   Biasanya di layout ini, Header ada di Sidebar. */
   
.cv-side-name-container {
    margin-bottom: 40px;
}

.cv-side-name {
    margin: 0;
    font-family: var(--font-head);
    font-size: {{ $thumbMode ? '24px' : '28px' }};
    font-weight: 800; /* Extra Bold */
    line-height: 1.2;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #fff;
}

.cv-side-role {
    margin-top: 8px;
    font-family: var(--font-head);
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 2px;
    color: #aaaaaa; /* Abu-abu muda */
}

/* Contact Section */
.cv-side-title {
    width: 100%;
    text-align: left;
    margin: 0 0 20px 0;
    font-family: var(--font-head);
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #fff;
    padding-bottom: 8px;
    border-bottom: 1px solid #333;
}

.cv-contact-item {
    width: 100%;
    text-align: left;
    margin-bottom: 15px;
    font-size: 13px;
    color: #cccccc;
    overflow-wrap: anywhere;
    word-break: break-word;
}

.cv-contact-item strong {
    color: #fff;
    display: block;
    font-size: 11px;
    text-transform: uppercase;
    margin-bottom: 4px;
    letter-spacing: 0.5px;
}

/* Social Icons (Simple Text Style for B&W) */
.cv-social-link {
    color: #fff;
    text-decoration: none;
    font-size: 13px;
    border-bottom: 1px solid #333;
    padding-bottom: 2px;
    transition: all 0.3s;
}
.cv-social-link:hover {
    color: #fff;
    border-bottom-color: #fff;
}


/* --- MAIN RIGHT (WHITE) --- */
.cv-main {
    background: #ffffff;
    padding: {{ $thumbMode ? '30px 35px' : '60px 50px' }};
    color: #000;
    overflow: visible;
}

/* Main Name (Hidden in Sidebar to keep B&W clean, showing in Main) */
.cv-main-header {
    margin-bottom: 40px;
    border-bottom: 2px solid #000;
    padding-bottom: 20px;
}

.cv-main-name {
    margin: 0;
    font-family: var(--font-head);
    font-size: {{ $thumbMode ? '32px' : '48px' }};
    font-weight: 800;
    text-transform: uppercase;
    line-height: 1.1;
    color: #000;
    max-width: 100%;
    white-space: normal;
    overflow-wrap: normal;
    word-break: normal;
    hyphens: none;
}

.cv-main-title {
    margin-top: 10px;
    font-size: 14px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 3px;
    color: #666;
    white-space: normal;
    overflow-wrap: normal;
    word-break: normal;
}

/* Section Styling */
.cv-main-section {
    margin-bottom: 45px;
}

.cv-section-heading {
    font-family: var(--font-head);
    font-size: 18px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 20px;
    color: #000;
    display: flex;
    align-items: center;
}

.cv-section-heading::after {
    content: '';
    flex: 1;
    height: 2px;
    background: #e5e5e5;
    margin-left: 15px;
}

/* About Me */
.cv-summary {
    font-size: 14px;
    line-height: 1.8;
    color: #333;
    text-align: justify;
}

/* Skills Grid */
.cv-skills-grid {
    display: grid;
    grid-template-columns: repeat({{ $thumbMode ? '2' : '3' }}, 1fr);
    gap: 15px;
    margin-top: 10px;
}

.cv-skill-item {
    font-family: var(--font-head);
    font-size: 13px;
    font-weight: 700;
    color: #000;
    background: #f5f5f5;
    padding: 10px 0;
    text-align: center;
    border: 1px solid #e0e0e0;
    text-transform: uppercase;
}

/* Experience & Education Items */
.cv-entry-item {
    margin-bottom: 25px;
}

.cv-entry-head {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    flex-wrap: wrap;
    row-gap: 6px;
    margin-bottom: 5px;
}

.cv-entry-role {
    font-family: var(--font-head);
    font-size: 15px;
    font-weight: 800;
    color: #000;
    text-transform: uppercase;
    max-width: 100%;
}

.cv-entry-date {
    font-family: var(--font-head);
    font-size: 12px;
    font-weight: 700;
    color: #666;
    max-width: 100%;
    text-align: right;
}

.cv-entry-company {
    font-size: 13px;
    font-weight: 600;
    color: #444;
    margin-bottom: 8px;
    display: block;
}

.cv-entry-desc {
    font-size: 13px;
    color: #555;
    line-height: 1.6;
    text-align: justify;
}

/* Mobile Responsive */
@media screen and (max-width: {{ $thumbMode ? '0px' : '760px' }}) {
    .cv-sidebar {
        grid-template-columns: 1fr;
    }
    .cv-main {
        padding: 40px 25px;
    }
    .cv-skills-grid {
        grid-template-columns: 1fr 1fr;
    }
}
</style>

<div class="cv-sidebar">
    <!-- SIDEBAR KIRI (HITAM) -->
    <aside class="cv-side">
        @if($photo)
            <img src="{{ $photo }}" alt="Photo" class="cv-side-photo">
        @else
            <!-- Placeholder Image Circle -->
            <div class="cv-side-photo d-flex align-items-center justify-content-center text-muted" style="background:#333;">
                <i class="bi bi-person fs-2"></i>
            </div>
        @endif

        <div class="cv-side-name-container">
            <!-- Optional: Tampilkan Nama di Kiri jika diinginkan, tapi layout standar biasanya Nama Besar di Kanan. 
                 Sesuai deskripsi: "BENJAMIN TAYLOR" besar. -->
             <!-- Saya pindahkan Nama Besar ke Kanan untuk balance Layout Sidebar Putih, 
                  TAPI jika request persis gambar (Sidebar Foto + Contact), maka Nama biasanya di Kanan. -->
        </div>

        <h2 class="cv-side-title">Contact</h2>
        
        @if($phone)
        <div class="cv-contact-item">
            <strong>Phone</strong>
            {{ $phone }}
        </div>
        @endif

        @if($email)
        <div class="cv-contact-item">
            <strong>Email</strong>
            <a href="mailto:{{ $email }}" style="color:#cccccc; text-decoration:none;">{{ $email }}</a>
        </div>
        @endif

        @if($location)
        <div class="cv-contact-item">
            <strong>Address</strong>
            {{ $location }}
        </div>
        @endif

        <h2 class="cv-side-title" style="margin-top: 30px;">Social</h2>
        @if($linkedin)
        <div class="cv-contact-item">
            <a href="{{ $linkedin }}" target="_blank" class="cv-social-link">LinkedIn Profile</a>
        </div>
        @endif
        @if($website)
        <div class="cv-contact-item">
            <a href="{{ $website }}" target="_blank" class="cv-social-link">Portfolio Website</a>
        </div>
        @endif

    </aside>

    <!-- MAIN KANAN (PUTIH) -->
    <main class="cv-main">
        
        <!-- Header Besar di Kanan -->
        <header class="cv-main-header">
            <h1 class="cv-main-name {{ $isPlaceholder('personal_name') ? 'cv-placeholder' : '' }}">
                {{ $thumbMode ? Str::limit($name, 25) : $name }}
            </h1>
            <div class="cv-main-title {{ $isPlaceholder('title') ? 'cv-placeholder' : '' }}">
                {{ $title }}
            </div>
        </header>

        <!-- About Me -->
        @if($summary)
        <section class="cv-main-section">
            <h2 class="cv-section-heading">About Me</h2>
            <p class="cv-summary {{ $isPlaceholder('summary') ? 'cv-placeholder' : '' }}">
                {{ $thumbMode ? Str::limit($summary, 300) : $summary }}
            </p>
        </section>
        @endif

        <!-- Skills (Pindah ke Main Area agar tampilan B&W seimbang) -->
        <section class="cv-main-section">
            <h2 class="cv-section-heading">Skills</h2>
            <div class="cv-skills-grid">
                @foreach($skills as $skill)
                    <div class="cv-skill-item {{ $itemPlaceholder($skill, 'name') ? 'cv-placeholder' : '' }}">
                        {{ data_get($skill, 'name') }}
                    </div>
                @endforeach
                <!-- Placeholder jika data kosong -->
                @if($skills->isEmpty())
                    <div class="cv-skill-item cv-placeholder">Skill One</div>
                    <div class="cv-skill-item cv-placeholder">Skill Two</div>
                    <div class="cv-skill-item cv-placeholder">Skill Three</div>
                    <div class="cv-skill-item cv-placeholder">Skill Four</div>
                    <div class="cv-skill-item cv-placeholder">Skill Five</div>
                @endif
            </div>
        </section>

        <!-- Experience -->
        <section class="cv-main-section">
            <h2 class="cv-section-heading">Experience</h2>
            @foreach(($thumbMode ? $experiences->take(3) : $experiences) as $exp)
                <article class="cv-entry-item">
                    <div class="cv-entry-head">
                        <div class="cv-entry-role {{ $itemPlaceholder($exp, 'position') ? 'cv-placeholder' : '' }}">
                            {{ data_get($exp, 'position') }}
                        </div>
                        <div class="cv-entry-date {{ ($itemPlaceholder($exp, 'start_date') || $itemPlaceholder($exp, 'end_date')) ? 'cv-placeholder' : '' }}">
                            {{ data_get($exp, 'start_date') }} {{ data_get($exp, 'end_date') ? '- ' . data_get($exp, 'end_date') : '- Present' }}
                        </div>
                    </div>
                    <span class="cv-entry-company {{ $itemPlaceholder($exp, 'company') ? 'cv-placeholder' : '' }}">
                        {{ data_get($exp, 'company') }}
                    </span>
                    @if(data_get($exp, 'description'))
                        <div class="cv-entry-desc {{ $itemPlaceholder($exp, 'description') ? 'cv-placeholder' : '' }}">
                            {{ $thumbMode ? Str::limit((string) data_get($exp, 'description'), 200) : data_get($exp, 'description') }}
                        </div>
                    @endif
                </article>
            @endforeach
        </section>

        <!-- Education -->
        <section class="cv-main-section">
            <h2 class="cv-section-heading">Education</h2>
            @foreach($educations as $edu)
                <article class="cv-entry-item">
                    <div class="cv-entry-head">
                        <div class="cv-entry-role {{ $itemPlaceholder($edu, 'school') ? 'cv-placeholder' : '' }}">
                            {{ data_get($edu, 'school') }}
                        </div>
                        <div class="cv-entry-date {{ $itemPlaceholder($edu, 'year') ? 'cv-placeholder' : '' }}">
                            {{ data_get($edu, 'year') }}
                        </div>
                    </div>
                    <span class="cv-entry-company {{ $itemPlaceholder($edu, 'degree') ? 'cv-placeholder' : '' }}">
                        {{ data_get($edu, 'degree') }}
                    </span>
                </article>
            @endforeach
        </section>

    </main>
</div>
@endsection