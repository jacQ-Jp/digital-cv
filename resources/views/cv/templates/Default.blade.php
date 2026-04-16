@extends($layout ?? 'layouts.render')

@section('content')
@php
    // --- DATA EXTRACTION ---
    $n = data_get($cv, 'personal_name') ?: (data_get($cv, 'user.name') ?? 'CV');
    $e = data_get($cv, 'personal_email') ?: (data_get($cv, 'user.email') ?? '');
    $title = data_get($cv, 'title');
    $summary = data_get($cv, 'summary');
    $personalPhone = data_get($cv, 'personal_phone');
    $personalLocation = data_get($cv, 'personal_location');
    $personalLinkedin = data_get($cv, 'personal_linkedin');
    $personalWebsite = data_get($cv, 'personal_website');
    $t = ($layout === 'layouts.thumb');

    // --- PHOTO LOGIC ---
    $previewPhotoUrl = data_get($cv, 'photo_preview_url');
    $storedPhotoPath = data_get($cv, 'photo_path');
    $ph = $previewPhotoUrl ?: ($storedPhotoPath ? asset('storage/'.$storedPhotoPath) : null);

    // --- THEME (Neutral/Default) ---
    $theme = [
        'text' => '#334155',
        'heading' => '#1e293b',
        'accent' => '#64748b', // Abu-abu netral
        'border' => '#e2e8f0',
        'bg' => '#ffffff',
        'bg_alt' => '#f8fafc'
    ];
    
    $placeholderFlags = collect($cv->preview_placeholder_flags ?? []);
    $isPlaceholder = fn (string $key): bool => (bool) $placeholderFlags->get($key, false);
    $itemPlaceholder = fn ($item, string $field): bool => (bool) data_get($item, '_placeholder.'.$field, false);
@endphp

<style>
    /* --- RESET & GLOBAL STYLES --- */
    .cv-paper {
        background-color: #e2e8f0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; /* Font Standard */
        color: {{ $theme['text'] }};
    }

    .cv-placeholder { opacity: 0.5; font-style: italic; }

    /* --- MAIN CONTAINER --- */
    .sheet {
        max-width: 210mm;
        margin: 40px auto;
        background: #fff;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        display: grid;
        /* Layout 2 Kolom: Utama (Lebar) & Sidebar (Sempit) */
        grid-template-columns: 2fr 1fr; 
        min-height: 297mm; /* Tinggi A4 */
        height: auto;
        overflow: hidden;
    }

    /* --- LEFT COLUMN (MAIN CONTENT) --- */
    .main-col {
        padding: 50px;
        border-right: 1px solid #e2e8f0;
    }

    /* Header Name & Title */
    .name {
        font-size: 32px;
        font-weight: 800;
        color: {{ $theme['heading'] }};
        margin: 0 0 5px 0;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .role {
        font-size: 16px;
        font-weight: 500;
        color: {{ $theme['accent'] }};
        margin-bottom: 25px;
    }

    /* Contact Bar (Top of Main Col) */
    .contact-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 30px;
        font-size: 13px;
        color: #64748b;
        padding-bottom: 20px;
        border-bottom: 1px solid #e2e8f0;
    }
    .contact-item { display: flex; align-items: center; gap: 6px; }
    .icon { font-weight: bold; color: {{ $theme['heading'] }}; }

    /* Summary */
    .summary {
        font-size: 14px;
        line-height: 1.7;
        margin-bottom: 30px;
    }

    /* Sections */
    .section { margin-bottom: 30px; }
    
    .section-title {
        font-size: 14px;
        font-weight: 700;
        text-transform: uppercase;
        color: {{ $theme['heading'] }};
        border-bottom: 2px solid {{ $theme['border'] }};
        padding-bottom: 5px;
        margin-bottom: 20px;
    }

    /* Experience Items */
    .job-item {
        margin-bottom: 20px;
    }
    
    .job-header {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        margin-bottom: 4px;
    }

    .job-title { font-weight: 700; font-size: 15px; color: {{ $theme['heading'] }}; }
    .job-date { font-size: 12px; font-weight: 600; color: {{ $theme['accent'] }}; white-space: nowrap; }
    
    .job-company { font-size: 13px; font-style: italic; color: #64748b; margin-bottom: 8px; }
    
    .job-desc {
        font-size: 13px;
        line-height: 1.6;
        color: #475569;
        margin: 0;
    }

    /* --- RIGHT COLUMN (SIDEBAR) --- */
    .sidebar {
        padding: 50px 30px;
        background-color: {{ $theme['bg_alt'] }};
    }

    .sidebar-title {
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        color: {{ $theme['heading'] }};
        margin-bottom: 15px;
        margin-top: 20px;
    }
    .sidebar-title:first-child { margin-top: 0; }

    /* Skills List */
    .skill-list { list-style: none; padding: 0; margin: 0; }
    .skill-list li {
        margin-bottom: 10px;
        font-size: 13px;
        padding-left: 12px;
        position: relative;
    }
    /* Bullet point */
    .skill-list li::before {
        content: '•';
        position: absolute;
        left: 0;
        color: {{ $theme['accent'] }};
        font-weight: bold;
    }

    /* Education List */
    .edu-item { margin-bottom: 20px; }
    .edu-school { font-weight: 700; font-size: 14px; color: {{ $theme['heading'] }}; }
    .edu-degree { font-size: 13px; color: #475569; margin: 2px 0 4px 0; }
    .edu-year { font-size: 12px; font-weight: 600; color: {{ $theme['accent'] }}; }

    /* Sidebar Contact (Detailed) */
    .side-contact-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 15px;
        font-size: 13px;
    }
    .side-label {
        min-width: 20px;
        font-weight: bold;
        color: {{ $theme['heading'] }};
    }

    @media (max-width: 768px) {
        .sheet { grid-template-columns: 1fr; height: auto; }
        .main-col { border-right: none; padding: 30px; }
        .sidebar { padding: 30px; }
        .name { font-size: 26px; }
    }
</style>

<div class="sheet">
    <!-- KIRI: KONTEN UTAMA -->
    <div class="main-col">
        
        <!-- Header -->
        <header>
            <h1 class="name {{ $isPlaceholder('personal_name') ? 'cv-placeholder' : '' }}">
                {{ $t ? Str::limit($n, 25) : $n }}
            </h1>
            @if($title)
                <div class="role {{ $isPlaceholder('title') ? 'cv-placeholder' : '' }}">
                    {{ $title }}
                </div>
            @endif

            <!-- Contact Info Bar -->
            <div class="contact-bar">
                @if($e)<div class="contact-item {{ $isPlaceholder('personal_email') ? 'cv-placeholder' : '' }}"><span class="icon">E:</span> {{ $e }}</div>@endif
                @if($personalPhone)<div class="contact-item"><span class="icon">P:</span> {{ $personalPhone }}</div>@endif
                @if($personalLocation)<div class="contact-item"><span class="icon">L:</span> {{ $personalLocation }}</div>@endif
            </div>
        </header>

        <!-- Summary -->
        @if($summary)
            <div class="section">
                <div class="section-title">Professional Summary</div>
                <p class="summary {{ $isPlaceholder('summary') ? 'cv-placeholder' : '' }}">
                    {{ $t ? Str::limit($summary, 250) : $summary }}
                </p>
            </div>
        @endif

        <!-- Experience -->
        <div class="section">
            <div class="section-title">Work Experience</div>
            @foreach(($t ? ($cv->experiences ?? [])->take(3) : ($cv->experiences ?? [])) as $x)
                <div class="job-item">
                    <div class="job-header">
                        <div class="job-title {{ $itemPlaceholder($x, 'position') ? 'cv-placeholder' : '' }}">{{ $x->position }}</div>
                        <div class="job-date {{ ($itemPlaceholder($x, 'start_date') || $itemPlaceholder($x, 'end_date')) ? 'cv-placeholder' : '' }}">
                            {{ $x->start_date }}{{ $x->end_date ? ' - '.$x->end_date : ' - Present' }}
                        </div>
                    </div>
                    <div class="job-company {{ $itemPlaceholder($x, 'company') ? 'cv-placeholder' : '' }}">{{ $x->company }}</div>
                    @if($x->description)
                        <p class="job-desc {{ $itemPlaceholder($x, 'description') ? 'cv-placeholder' : '' }}">
                            {{ $t ? Str::limit($x->description, 150) : $x->description }}
                        </p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- KANAN: SIDEBAR -->
    <aside class="sidebar">
        
        <!-- Contact Details -->
        <div class="sidebar-title">Contact</div>
        @if($e)
            <div class="side-contact-item {{ $isPlaceholder('personal_email') ? 'cv-placeholder' : '' }}">
                <span class="side-label">@</span>
                <div>{{ $e }}</div>
            </div>
        @endif
        @if($personalLinkedin)
            <div class="side-contact-item">
                <span class="side-label">in</span>
                <div>{{ $personalLinkedin }}</div>
            </div>
        @endif
        @if($personalWebsite)
            <div class="side-contact-item">
                <span class="side-label">W</span>
                <div>{{ $personalWebsite }}</div>
            </div>
        @endif

        <!-- Skills -->
        @if($cv->skills && count($cv->skills) > 0)
            <div class="sidebar-title">Skills</div>
            <ul class="skill-list">
                @foreach($cv->skills as $x)
                    <li class="{{ $itemPlaceholder($x, 'name') ? 'cv-placeholder' : '' }}">{{ $x->name }}</li>
                @endforeach
            </ul>
        @endif

        <!-- Education -->
        @if($cv->educations && count($cv->educations) > 0)
            <div class="sidebar-title">Education</div>
            @foreach($cv->educations as $x)
                <div class="edu-item">
                    <div class="edu-school {{ $itemPlaceholder($x, 'school') ? 'cv-placeholder' : '' }}">{{ $x->school }}</div>
                    <div class="edu-degree {{ $itemPlaceholder($x, 'degree') ? 'cv-placeholder' : '' }}">{{ $x->degree }}</div>
                    <div class="edu-year {{ $itemPlaceholder($x, 'year') ? 'cv-placeholder' : '' }}">{{ $x->year }}</div>
                </div>
            @endforeach
        @endif

    </aside>
</div>
@endsection