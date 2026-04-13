@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6" style="padding-top:2.5rem; padding-bottom:4rem;">

    {{-- ==================== HEADER ==================== --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 style="font-size:1.75rem; font-weight:800; color:#0f172a; letter-spacing:-0.03em; margin:0; line-height:1.2;">
                My CVs
            </h1>
            <p style="margin-top:6px; font-size:0.875rem; color:#94a3b8; line-height:1.5;">
                Kelola semua CV yang telah kamu buat dari satu tempat.
            </p>
        </div>

        <a href="{{ route('cv-builder.templates') }}"
           style="display:inline-flex; align-items:center; gap:8px; background:linear-gradient(135deg,#3b82f6,#6366f1); color:#fff; padding:11px 22px; border-radius:10px; font-size:0.8125rem; font-weight:600; text-decoration:none; box-shadow:0 4px 14px -2px rgba(59,130,246,0.4); transition:all 0.2s; white-space:nowrap;"
           onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 20px -2px rgba(59,130,246,0.5)';"
           onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 14px -2px rgba(59,130,246,0.4)';">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Pilih Template
        </a>
    </div>

    {{-- ==================== FILTER BAR (ADVANCED) ==================== --}}
    <form method="GET" action="{{ route('cvs.index') }}"
          class="mt-6"
          style="background:#fff; border:1px solid #f1f5f9; border-radius:14px; padding:14px; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
        <div class="grid grid-cols-1 md:grid-cols-12" style="gap:10px;">
            <div class="md:col-span-5">
                <label class="sr-only" for="q">Search</label>
                <input id="q" name="q" value="{{ $search ?? request('q') }}"
                       placeholder="Cari title / summary…"
                       class="w-full"
                       style="height:42px; border:1px solid #e2e8f0; border-radius:10px; padding:0 12px; font-size:0.875rem; outline:none;">
            </div>

            <div class="md:col-span-3">
                <label class="sr-only" for="status">Status</label>
                <select id="status" name="status" class="w-full"
                        style="height:42px; border:1px solid #e2e8f0; border-radius:10px; padding:0 10px; font-size:0.875rem; outline:none; background:#fff;">
                    @php($selectedStatus = $status ?? request('status'))
                    <option value="">Semua status</option>
                    <option value="draft" @selected($selectedStatus==='draft')>Draft</option>
                    <option value="published" @selected($selectedStatus==='published')>Published</option>
                </select>
            </div>

            <div class="md:col-span-4">
                <label class="sr-only" for="template">Template</label>
                <select id="template" name="template" class="w-full"
                        style="height:42px; border:1px solid #e2e8f0; border-radius:10px; padding:0 10px; font-size:0.875rem; outline:none; background:#fff;">
                    @php($selectedTemplate = $template ?? request('template'))
                    <option value="">Semua template</option>
                    @foreach(($templates ?? collect()) as $tpl)
                        <option value="{{ $tpl->slug }}" @selected($selectedTemplate===$tpl->slug)>
                            {{ $tpl->name }} ({{ $tpl->slug }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-12 flex items-center justify-between" style="gap:10px;">
                <div class="text-xs" style="color:#94a3b8;">
                    Tip: filter akan mengubah list tanpa menghapus data.
                </div>
                <div class="flex items-center" style="gap:8px;">
                    <a href="{{ route('cvs.index') }}" style="font-size:0.8125rem; color:#64748b; text-decoration:none;">Reset</a>
                    <button type="submit"
                            style="display:inline-flex; align-items:center; gap:8px; background:#0f172a; color:#fff; padding:10px 14px; border-radius:10px; font-size:0.8125rem; font-weight:700; border:none; cursor:pointer;">
                        Apply
                    </button>
                </div>
            </div>
        </div>
    </form>

    {{-- ==================== EMPTY STATE ==================== --}}
    @if($cvs->isEmpty())
        <div style="margin-top:3rem; background:#fff; border:1px solid #f1f5f9; border-radius:16px; padding:4rem 2rem; text-align:center; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
            {{-- Illustration --}}
            <div style="width:80px; height:80px; margin:0 auto 1.5rem; border-radius:20px; background:#f8fafc; border:1px solid #e2e8f0; display:flex; align-items:center; justify-content:center;">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <path d="M14 2v6h6"/>
                    <path d="M12 18v-6"/>
                    <path d="M9 15h6"/>
                </svg>
            </div>
            <h3 style="font-size:1rem; font-weight:700; color:#0f172a; margin:0;">Belum ada CV</h3>
            <p style="font-size:0.875rem; color:#94a3b8; margin:8px 0 0; max-width:280px; margin-left:auto; margin-right:auto; line-height:1.6;">
                Mulai buat CV pertamamu dengan memilih template yang sesuai.
            </p>
            <a href="{{ route('cv-builder.templates') }}"
               style="display:inline-flex; align-items:center; gap:8px; margin-top:1.5rem; background:linear-gradient(135deg,#3b82f6,#6366f1); color:#fff; padding:11px 22px; border-radius:10px; font-size:0.8125rem; font-weight:600; text-decoration:none; box-shadow:0 4px 14px -2px rgba(59,130,246,0.4); transition:all 0.2s;"
               onmouseover="this.style.transform='translateY(-1px)';"
               onmouseout="this.style.transform='translateY(0)';">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Buat CV Pertama
            </a>
        </div>

    {{-- ==================== CV CARDS GRID ==================== --}}
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3" style="margin-top:2rem; gap:16px;">

            @foreach($cvs as $cv)
                @php
                    $isPublished = $cv->status === 'published';
                    $accentColor = $isPublished ? '#10b981' : '#f59e0b';
                    $accentBg = $isPublished ? '#ecfdf5' : '#fffbeb';
                    $accentText = $isPublished ? '#059669' : '#d97706';
                    $accentBorder = $isPublished ? '#a7f3d0' : '#fde68a';

                    // Template thumbnail (optional)
                    $tpl = method_exists($cv, 'template') ? $cv->template : null;
                    $thumb = $tpl?->thumbnail;
                @endphp

                <div style="background:#fff; border:1px solid #f1f5f9; border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.04); transition:all 0.25s cubic-bezier(0.4,0,0.2,1); border-left:3px solid {{ $accentColor }};"
                     onmouseover="this.style.boxShadow='0 8px 25px -5px rgba(0,0,0,0.08)'; this.style.borderColor='#e2e8f0'; this.style.borderLeftColor='{{ $accentColor }}'; this.style.transform='translateY(-2px)';"
                     onmouseout="this.style.boxShadow='0 1px 3px rgba(0,0,0,0.04)'; this.style.borderColor='#f1f5f9'; this.style.borderLeftColor='{{ $accentColor }}'; this.style.transform='translateY(0)';">

                    {{-- Thumbnail strip --}}
                    <div style="height:92px; background:linear-gradient(135deg,#eef2ff,#f8fafc); border-bottom:1px solid #f1f5f9; position:relative;">
                        @if(!empty($thumb))
                            <img src="{{ $thumb }}" alt="{{ $cv->template_slug }}" style="width:100%; height:100%; object-fit:cover; display:block; opacity:0.95;" />
                            <div style="position:absolute; inset:0; background:linear-gradient(180deg, rgba(255,255,255,0) 0%, rgba(255,255,255,0.85) 100%);"></div>
                        @else
                            <div style="position:absolute; inset:0; opacity:0.3; background-image:linear-gradient(#cbd5e1 1px, transparent 1px), linear-gradient(90deg, #cbd5e1 1px, transparent 1px); background-size:26px 26px;"></div>
                        @endif
                        <div style="position:absolute; left:16px; bottom:12px; display:flex; align-items:center; gap:8px;">
                            <span style="display:inline-flex; align-items:center; padding:4px 10px; border-radius:999px; font-size:10px; font-weight:800; letter-spacing:0.06em; text-transform:uppercase; background:{{ $accentBg }}; color:{{ $accentText }}; border:1px solid {{ $accentBorder }};">
                                {{ $cv->status }}
                            </span>
                            <span style="display:inline-flex; align-items:center; padding:4px 10px; border-radius:999px; font-size:10px; font-weight:800; letter-spacing:0.06em; text-transform:uppercase; background:#fff; color:#64748b; border:1px solid #e2e8f0;">
                                {{ $cv->template_slug }}
                            </span>
                        </div>
                    </div>

                    {{-- Card Body --}}
                    <div style="padding:18px 20px 0;">
                        <div class="flex items-start justify-between gap-3">
                            <h3 style="font-size:0.9375rem; font-weight:800; color:#0f172a; margin:0; line-height:1.3; overflow:hidden; text-overflow:ellipsis; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;">
                                {{ $cv->title }}
                            </h3>
                        </div>

                        @if($cv->summary)
                            <p style="margin-top:8px; font-size:0.8125rem; color:#94a3b8; line-height:1.6; overflow:hidden; text-overflow:ellipsis; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;">
                                {{ \Illuminate\Support\Str::limit($cv->summary, 100) }}
                            </p>
                        @endif

                        <div class="flex items-center flex-wrap" style="margin-top:14px; gap:6px 16px; font-size:0.75rem; color:#b0b8c8;">
                            <span class="flex items-center" style="gap:5px;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                {{ $cv->updated_at?->diffForHumans() }}
                            </span>
                        </div>
                    </div>

                    {{-- Card Actions --}}
                    <div style="margin-top:16px; padding:14px 20px; border-top:1px solid #f8fafc; display:flex; align-items:center; gap:6px;">

                        {{-- View --}}
                        <a href="{{ route('cvs.show', $cv) }}"
                           style="flex:1; display:flex; align-items:center; justify-content:center; gap:5px; padding:8px 0; border-radius:8px; font-size:0.75rem; font-weight:600; color:#64748b; background:#f8fafc; text-decoration:none; transition:all 0.15s;"
                           onmouseover="this.style.background='#eff6ff'; this.style.color='#3b82f6';"
                           onmouseout="this.style.background='#f8fafc'; this.style.color='#64748b';">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            View
                        </a>

                        {{-- Preview --}}
                        <a href="{{ route('cvs.render', $cv) }}" target="_blank" rel="noopener"
                           style="flex:1; display:flex; align-items:center; justify-content:center; gap:5px; padding:8px 0; border-radius:8px; font-size:0.75rem; font-weight:600; color:#64748b; background:#f8fafc; text-decoration:none; transition:all 0.15s;"
                           onmouseover="this.style.background='#eef2ff'; this.style.color='#6366f1';"
                           onmouseout="this.style.background='#f8fafc'; this.style.color='#64748b';">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z"/></svg>
                            Preview
                        </a>

                        {{-- Publish Toggle --}}
                        <form method="POST" action="{{ route('cvs.toggle-publish', $cv) }}" style="flex:1; display:flex;">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                style="width:100%; display:flex; align-items:center; justify-content:center; gap:5px; padding:8px 0; border-radius:8px; font-size:0.75rem; font-weight:600; color:#64748b; background:#f8fafc; border:none; cursor:pointer; transition:all 0.15s;"
                                onmouseover="this.style.background='{{ $cv->status === 'published' ? '#fef2f2' : '#ecfdf5' }}'; this.style.color='{{ $cv->status === 'published' ? '#ef4444' : '#059669' }}';"
                                onmouseout="this.style.background='#f8fafc'; this.style.color='#64748b';">
                                @if($cv->status === 'published')
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l1.5-1.5a5 5 0 0 0-7.07-7.07L11.5 4.5"/><path d="M14 11a5 5 0 0 0-7.54-.54l-1.5 1.5a5 5 0 0 0 7.07 7.07l.47-.47"/></svg>
                                    Unpublish
                                @else
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v4"/><path d="M10 14 21 3"/><path d="M21 10v11a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                                    Publish
                                @endif
                            </button>
                        </form>

                        {{-- Copy Public Link (only published) --}}
                        @if($cv->status === 'published')
                            @php($publicUrl = route('cvs.public', $cv))
                            <button type="button"
                                data-copy="{{ $publicUrl }}"
                                style="flex:1; display:flex; align-items:center; justify-content:center; gap:5px; padding:8px 0; border-radius:8px; font-size:0.75rem; font-weight:600; color:#64748b; background:#f8fafc; border:none; cursor:pointer; transition:all 0.15s;"
                                onmouseover="this.style.background='#f0fdf4'; this.style.color='#16a34a';"
                                onmouseout="this.style.background='#f8fafc'; this.style.color='#64748b';">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                Copy
                            </button>
                        @endif

                        {{-- Edit --}}
                        <a href="{{ route('cvs.edit', $cv) }}"
                           style="flex:1; display:flex; align-items:center; justify-content:center; gap:5px; padding:8px 0; border-radius:8px; font-size:0.75rem; font-weight:600; color:#64748b; background:#f8fafc; text-decoration:none; transition:all 0.15s;"
                           onmouseover="this.style.background='#f0fdf4'; this.style.color='#16a34a';"
                           onmouseout="this.style.background='#f8fafc'; this.style.color='#64748b';">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            Edit
                        </a>

                        {{-- Delete --}}
                        <form method="POST" action="{{ route('cvs.destroy', $cv) }}" style="flex:1; display:flex;"
                              onsubmit="return confirm('Yakin ingin menghapus CV \"{{ $cv->title }}\"?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    style="width:100%; display:flex; align-items:center; justify-content:center; gap:5px; padding:8px 0; border-radius:8px; font-size:0.75rem; font-weight:600; color:#64748b; background:#f8fafc; border:none; cursor:pointer; transition:all 0.15s;"
                                    onmouseover="this.style.background='#fef2f2'; this.style.color='#ef4444';"
                                    onmouseout="this.style.background='#f8fafc'; this.style.color='#64748b';">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Footer count --}}
        <div style="margin-top:1.5rem; text-align:center;">
            <span style="font-size:0.75rem; color:#cbd5e1;">
                {{ $cvs->count() }} CV tersimpan
            </span>
        </div>
    @endif

</div>

{{-- Copy to clipboard handler --}}
<script>
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('button[data-copy]');
        if (!btn) return;

        const url = btn.getAttribute('data-copy');
        if (!url) return;

        try {
            await navigator.clipboard.writeText(url);
            const prev = btn.innerHTML;
            btn.innerHTML = 'Copied';
            setTimeout(() => (btn.innerHTML = prev), 900);
        } catch {
            window.prompt('Copy this link:', url);
        }
    });
</script>
@endsection