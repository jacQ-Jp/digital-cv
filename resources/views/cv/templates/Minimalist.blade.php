@extends($layout ?? 'layouts.render')

@section('content')
@php
    // --- DATA EXTRACTION (Sama persis dengan input) ---
    $n = data_get($cv, 'personal_name') ?: (data_get($cv, 'user.name') ?? 'CV');
    $e = data_get($cv, 'personal_email') ?: (data_get($cv, 'user.email') ?? '');
    $title = data_get($cv, 'title');
    $summary = data_get($cv, 'summary');
    $personalPhone = data_get($cv, 'personal_phone');
    $personalLocation = data_get($cv, 'personal_location');
    $personalLinkedin = data_get($cv, 'personal_linkedin');
    $personalWebsite = data_get($cv, 'personal_website');
    $t = ($layout === 'layouts.thumb');
    
    // --- PHOTO LOGIC (PENTING: Diembalikan agar kompatibel dengan layout asli) ---
    $previewPhotoUrl = data_get($cv, 'photo_preview_url');
    $storedPhotoPath = data_get($cv, 'photo_path');
    $ph = $previewPhotoUrl ?: ($storedPhotoPath ? asset('storage/'.$storedPhotoPath) : null);
    
    // --- THEME CONFIGURATION ---
    $accent = strtoupper((string) ($cv->accent_color ?? '#7C3AED'));
    $themes = [
        '#7C3AED' => ['accent' => '#7C3AED', 'accent_dark' => '#5B21B6', 'accent_soft' => '#EDE9FE', 'bg_soft' => '#F8FAFC'],
        '#0EA5A4' => ['accent' => '#0EA5A4', 'accent_dark' => '#0F766E', 'accent_soft' => '#CCFBF1', 'bg_soft' => '#F0FDFA'],
        '#3B82F6' => ['accent' => '#3B82F6', 'accent_dark' => '#1D4ED8', 'accent_soft' => '#DBEAFE', 'bg_soft' => '#EFF6FF'],
        '#EA580C' => ['accent' => '#EA580C', 'accent_dark' => '#C2410C', 'accent_soft' => '#FFEDD5', 'bg_soft' => '#FFF7ED'],
        '#334155' => ['accent' => '#334155', 'accent_dark' => '#1E293B', 'accent_soft' => '#E2E8F0', 'bg_soft' => '#F1F5F9'],
        '#166534' => ['accent' => '#166534', 'accent_dark' => '#14532D', 'accent_soft' => '#DCFCE7', 'bg_soft' => '#F0FDF4'],
        '#BE123C' => ['accent' => '#BE123C', 'accent_dark' => '#9F1239', 'accent_soft' => '#FFE4E6', 'bg_soft' => '#FFF1F2'],
    ];
    $theme = $themes[$accent] ?? $themes['#7C3AED'];
    
    $placeholderFlags = collect($cv->preview_placeholder_flags ?? []);
    $isPlaceholder = fn (string $key): bool => (bool) $placeholderFlags->get($key, false);
    $itemPlaceholder = fn ($item, string $field): bool => (bool) data_get($item, '_placeholder.'.$field, false);
@endphp

<style>
    /* --- MODERN RESET & VARIABLES --- */
    .cv-paper {
        --ac: {{ $theme['accent'] }};
        --ac-dark: {{ $theme['accent_dark'] }};
        --ac-soft: {{ $theme['accent_soft'] }};
        --bg-soft: {{ $theme['bg_soft'] }};
        
        background-color: #f1f5f9; /* Latar belakang halaman abu-abu muda */
        color: #334155;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }

    .cv-placeholder { opacity: 0.5; font-style: italic; }

    /* --- MAIN CONTAINER --- */
    .sheet {
        max-width: 210mm;
        margin: 40px auto;
        background: #fff;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1); /* Shadow modern yang halus */
        border-radius: 16px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    /* --- HEADER AREA --- */
    .head {
        padding: 48px 48px 32px 48px;
        border-bottom: 1px solid #e2e8f0;
        background: #fff;
    }

    .name {
        font-size: 40px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
        letter-spacing: -0.03em;
        margin: 0 0 8px 0;
    }

    .role {
        font-size: 16px;
        font-weight: 600;
        color: var(--ac);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 20px;
    }

    /* Kontak di header dibuat minimalis */
    .meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0 24px;
        font-size: 13px;
        color: #64748b;
    }
    .meta span {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    /* Ikon simpel menggunakan CSS pseudo-elements */
    .meta span::before {
        content: '';
        display: block;
        width: 6px;
        height: 6px;
        background-color: var(--ac);
        border-radius: 50%;
    }

    /* --- SUMMARY BOX --- */
    .summary-box {
        margin-top: 24px;
        padding: 16px;
        background-color: var(--bg-soft);
        border-left: 4px solid var(--ac);
        border-radius: 0 8px 8px 0;
        font-size: 13px;
        line-height: 1.6;
        color: #475569;
    }

    /* --- LAYOUT GRID --- */
    .layout {
        display: grid;
        grid-template-columns: 1.6fr 1fr; /* Kiri lebih lebar */
    }

    /* --- MAIN COLUMN (Experience & Edu) --- */
    .main-col {
        padding: 40px;
        border-right: 1px solid #f1f5f9;
    }

    .section { margin-bottom: 36px; }
    
    .section-title {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #94a3b8;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
    }
    .section-title::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #e2e8f0;
        margin-left: 16px;
    }

    /* Modern Card Style for Items */
    .item-card {
        position: relative;
        padding-left: 24px;
        border-left: 2px solid #f1f5f9;
        padding-bottom: 24px;
    }
    .item-card:last-child { border-left: 2px solid transparent; }

    /* Dot on the timeline */
    .item-card::before {
        content: '';
        position: absolute;
        left: -6px;
        top: 5px;
        width: 10px;
        height: 10px;
        background: #fff;
        border: 2px solid var(--ac);
        border-radius: 50%;
        z-index: 2;
    }

    .header-row {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        margin-bottom: 4px;
    }

    .position { font-size: 15px; font-weight: 700; color: #1e293b; }
    .company { font-size: 13px; color: #64748b; font-weight: 500; }
    
    .date-badge {
        font-size: 11px;
        font-weight: 600;
        color: var(--ac-dark);
        background: var(--ac-soft);
        padding: 2px 8px;
        border-radius: 4px;
        white-space: nowrap;
    }

    .desc {
        margin-top: 8px;
        font-size: 13px;
        line-height: 1.6;
        color: #475569;
    }

    /* --- SIDEBAR COLUMN (Skills & Contact) --- */
    .side-col {
        padding: 40px;
        background-color: #fcfcfc; /* Sedikit beda tone */
    }

    /* Skill Tags */
    .skill-group { display: flex; flex-wrap: wrap; gap: 8px; }
    
    .skill-tag {
        font-size: 11px;
        font-weight: 600;
        color: var(--ac-dark);
        background: #fff;
        border: 1px solid #e2e8f0;
        padding: 6px 12px;
        border-radius: 6px;
        transition: all 0.2s;
    }
    /* Sedikit trik visual modern */
    .skill-tag:hover {
        border-color: var(--ac);
        background: var(--ac-soft);
    }

    /* Contact Box in Sidebar */
    .contact-box {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 20px;
    }
    
    .contact-row {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
        font-size: 12.5px;
        color: #475569;
    }
    .contact-row:last-child { margin-bottom: 0; }
    
    .contact-icon {
        width: 32px;
        height: 32px;
        background: var(--bg-soft);
        color: var(--ac);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        font-weight: 700;
        font-size: 12px;
    }

    /* Education Grid in Sidebar */
    .edu-item {
        margin-bottom: 16px;
        padding-bottom: 16px;
        border-bottom: 1px dashed #e2e8f0;
    }
    .edu-item:last-child { border: none; margin: 0; padding: 0; }
    .edu-school { font-weight: 700; color: #1e293b; font-size: 13px; }
    .edu-degree { color: #64748b; font-size: 12px; margin-top: 2px; }
    .edu-year { font-size: 11px; color: var(--ac); font-weight: 600; margin-top: 4px; }

    @media (max-width: 768px) {
        .sheet { margin: 0; border-radius: 0; height: auto; }
        .head { padding: 30px 20px; }
        .layout { grid-template-columns: 1fr; }
        .main-col, .side-col { padding: 30px 20px; }
        .main-col { border-right: none; border-bottom: 1px solid #f1f5f9; }
    }
</style>
<div class="sheet">
    <!-- HEADER -->
    <header class="head">
        <h1 class="name {{ $isPlaceholder('personal_name') ? 'cv-placeholder' : '' }}">{{ $t ? Str::limit($n, 30) : $n }}</h1>
        @if($title)<div class="role {{ $isPlaceholder('title') ? 'cv-placeholder' : '' }}">{{ $title }}</div>@endif
        
        <div class="meta">
            @if($e)<span>{{ $e }}</span>@endif
            @if($personalPhone)<span>{{ $personalPhone }}</span>@endif
            @if($personalLocation)<span>{{ $personalLocation }}</span>@endif
            @if($personalLinkedin)<span>{{ $personalLinkedin }}</span>@endif
        </div>

        @if($summary)
            <div class="summary-box {{ $isPlaceholder('summary') ? 'cv-placeholder' : '' }}">
                {{ $t ? Str::limit($summary, 180) : $summary }}
            </div>
        @endif
    </header>

    <div class="layout">
        <!-- MAIN COLUMN -->
        <div class="main-col">
            <section class="section">
                <div class="section-title">Work Experience</div>
                <!-- Menggunakan '?? []' untuk mencegah error jika data kosong -->
                @foreach(($t ? ($cv->experiences ?? [])->take(3) : ($cv->experiences ?? [])) as $x)
                    <article class="item-card">
                        <div class="header-row">
                            <div>
                                <div class="position {{ $itemPlaceholder($x, 'position') ? 'cv-placeholder' : '' }}">{{ $x->position }}</div>
                                <div class="company {{ $itemPlaceholder($x, 'company') ? 'cv-placeholder' : '' }}">{{ $x->company }}</div>
                            </div>
                            <span class="date-badge {{ ($itemPlaceholder($x, 'start_date') || $itemPlaceholder($x, 'end_date')) ? 'cv-placeholder' : '' }}">
                                {{ $x->start_date }}{{ $x->end_date ? ' - '.$x->end_date : ' - Present' }}
                            </span>
                        </div>
                        @if($x->description)
                            <div class="desc {{ $itemPlaceholder($x, 'description') ? 'cv-placeholder' : '' }}">
                                {{ $t ? Str::limit($x->description, 150) : $x->description }}
                            </div>
                        @endif
                    </article>
                @endforeach
            </section>
        </div>

        <!-- SIDEBAR COLUMN -->
        <aside class="side-col">
            
            <!-- SKILLS SECTION -->
            <section class="section">
                <div class="section-title">Skills</div>
                <div class="skill-group">
                    @foreach(($cv->skills ?? []) as $x)
                        <span class="skill-tag {{ $itemPlaceholder($x, 'name') ? 'cv-placeholder' : '' }}">{{ $x->name }}</span>
                    @endforeach
                </div>
            </section>

            <!-- EDUCATION SECTION (Moved to Sidebar for balance) -->
            <section class="section">
                <div class="section-title">Education</div>
                @foreach(($cv->educations ?? []) as $x)
                    <article class="edu-item">
                        <div class="edu-school {{ $itemPlaceholder($x, 'school') ? 'cv-placeholder' : '' }}">{{ $x->school }}</div>
                        <div class="edu-degree {{ $itemPlaceholder($x, 'degree') ? 'cv-placeholder' : '' }}">{{ $x->degree }}</div>
                        <div class="edu-year {{ $itemPlaceholder($x, 'year') ? 'cv-placeholder' : '' }}">{{ $x->year }}</div>
                    </article>
                @endforeach
            </section>

            <!-- DETAILED CONTACT (Optional, styled as list) -->
            <section class="section">
                <div class="section-title">Contact</div>
                <div class="contact-box">
                    @if($e)
                    <div class="contact-row {{ $isPlaceholder('personal_email') ? 'cv-placeholder' : '' }}">
                        <div class="contact-icon">@</div>
                        <div>{{ $e }}</div>
                    </div>
                    @endif
                    @if($personalPhone)
                    <div class="contact-row">
                        <div class="contact-icon">P</div>
                        <div>{{ $personalPhone }}</div>
                    </div>
                    @endif
                    @if($personalWebsite)
                    <div class="contact-row">
                        <div class="contact-icon">W</div>
                        <div>{{ $personalWebsite }}</div>
                    </div>
                    @endif
                </div>
            </section>

        </aside>
    </div>
</div>
@endsection