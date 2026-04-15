@extends($layout ?? 'layouts.render')

@section('content')
@php
    $n = $cv->personal_name ?: ($cv->user?->name ?? 'CV');
    $e = $cv->personal_email ?: ($cv->user?->email ?? '');
    $t = ($layout === 'layouts.thumb');
    $ph = $cv->photo_path ? asset('storage/'.$cv->photo_path) : null;
    $in = collect(explode(' ', $n))->map(fn($w) => strtoupper(mb_substr($w,0,1)))->take(2)->join('');
@endphp
<style>
.cv-paper{padding:0!important;overflow:hidden}
.wr{display:flex;min-height:100%;font-family:'Inter',sans-serif;color:#1e293b;font-size:14px;line-height:1.6}
.sb{width:240px;background:linear-gradient(180deg,#0f172a,#1e293b);color:#e2e8f0;padding:40px 24px;flex-shrink:0}
.ph{width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid rgba(139,92,246,.4);margin-bottom:14px}
.pf{width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#7c3aed,#3b82f6);display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:800;color:#fff;margin-bottom:14px}
.sn{font-size:18px;font-weight:800;color:#fff;margin:0 0 3px}
.sr{font-size:11px;color:#a78bfa;font-weight:600;text-transform:uppercase;letter-spacing:.08em}
.ss{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.12em;color:#64748b;margin:24px 0 10px;padding-bottom:5px;border-bottom:1px solid rgba(255,255,255,.08)}
.si{font-size:11.5px;color:#94a3b8;margin-bottom:6px;line-height:1.4;word-break:break-all}
.sk{display:inline-block;font-size:10.5px;font-weight:600;color:#c4b5fd;background:rgba(139,92,246,.15);border:1px solid rgba(139,92,246,.2);padding:3px 9px;border-radius:999px;margin:0 4px 5px 0}
.mn{flex:1;padding:40px 32px}
.ms{font-size:13px;color:#64748b;line-height:1.7;margin:0 0 24px}
.mt{font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#7c3aed;margin:0 0 14px;display:flex;align-items:center;gap:8px}
.mt::after{content:'';flex:1;height:1px;background:linear-gradient(90deg,#e2e8f0,transparent)}
.me{margin-bottom:24px}.mi{margin-bottom:16px}
.mp{font-size:14px;font-weight:700;color:#0f172a;margin:0}
.mu{font-size:12.5px;color:#64748b;margin:2px 0 0}
.md{font-size:10.5px;color:#94a3b8;font-weight:600;background:#f1f5f9;padding:2px 7px;border-radius:4px}
.mr{display:flex;justify-content:space-between;align-items:center}
</style>
<div class="wr">
    <div class="sb">
        @if($ph)<img src="{{ $ph }}" class="ph" alt="">@else<div class="pf">{{ $in }}</div>@endif
        <h1 class="sn">{{ $t ? Str::limit($n,20) : $n }}</h1>
        @if($cv->title)<div class="sr">{{ $cv->title }}</div>@endif
        <div class="ss">Contact</div>
        @if($e)<div class="si">{{ $e }}</div>@endif
        @if($cv->personal_phone)<div class="si">{{ $cv->personal_phone }}</div>@endif
        @if($cv->personal_location)<div class="si">{{ $cv->personal_location }}</div>@endif
        <div class="ss">Skills</div>
        <div>@foreach($cv->skills as $x)<span class="sk">{{ $x->name }}</span>@endforeach</div>
    </div>
    <div class="mn">
        @if($cv->summary)<p class="ms">{{ $t ? Str::limit($cv->summary,150) : $cv->summary }}</p>@endif
        <div class="me"><h2 class="mt">Experience</h2>
            @foreach(($t ? $cv->experiences->take(3) : $cv->experiences) as $x)
                <div class="mi"><div class="mr"><div><p class="mp">{{ $x->position }}</p><p class="mu">{{ $x->company }}</p></div><span class="md">{{ $x->start_date }}{{ $x->end_date ? ' – '.$x->end_date : ' – Now' }}</span></div>
                @if($x->description)<p class="ms" style="margin:6px 0 0;font-size:12.5px">{{ $t ? Str::limit($x->description,80) : $x->description }}</p>@endif</div>
            @endforeach
        </div>
        <div class="me"><h2 class="mt">Education</h2>
            @foreach($cv->educations as $x)
                <div class="mi"><div class="mr"><div><p class="mp">{{ $x->school }}</p><p class="mu">{{ $x->degree }}</p></div><span class="md">{{ $x->year }}</span></div></div>
            @endforeach
        </div>
    </div>
</div>
@endsection