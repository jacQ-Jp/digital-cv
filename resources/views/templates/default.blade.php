@extends($layout ?? 'layouts.render')

@section('content')
@php
    $n = $cv->personal_name ?: ($cv->user?->name ?? 'CV');
    $e = $cv->personal_email ?: ($cv->user?->email ?? '');
    $t = ($layout === 'layouts.thumb');
@endphp
<style>
.h{border-bottom:2px solid #111827;padding-bottom:24px;margin-bottom:28px}
.n{font-size:34px;font-weight:900;letter-spacing:-.03em;color:#111827;line-height:1.1}
.r{font-size:15px;color:#6b7280;font-weight:500;margin-top:4px}
.c{display:flex;flex-wrap:wrap;gap:6px 20px;margin-top:12px;font-size:12.5px;color:#4b5563}
.s{margin-top:20px;font-size:14px;color:#4b5563;line-height:1.7}
.b{display:grid;grid-template-columns:2fr 1fr;gap:0 40px}
.t{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.14em;color:#9ca3af;border-bottom:1px solid #e5e7eb;padding-bottom:8px;margin:0 0 16px}
.sc{margin-bottom:28px}.i{margin-bottom:18px}.i:last-child{margin-bottom:0}
.p{font-size:15px;font-weight:700;color:#111827;margin:0}
.u{font-size:13.5px;color:#6b7280;margin:2px 0 0}
.d{font-size:12px;color:#9ca3af;font-weight:600}
.x{font-size:13.5px;color:#4b5563;margin:8px 0 0;line-height:1.6}
.g{display:inline-block;font-size:12px;font-weight:600;color:#374151;background:#f9fafb;border:1px solid #e5e7eb;padding:5px 12px;border-radius:6px;margin:0 6px 6px 0}
.w{display:flex;justify-content:space-between;align-items:baseline;gap:12px}
.k{margin-bottom:8px;font-size:13px;color:#4b5563}
</style>
<header class="h">
    <h1 class="n">{{ $t ? Str::limit($n,30) : $n }}</h1>
    @if($cv->title)<div class="r">{{ $cv->title }}</div>@endif
    <div class="c">
        @if($e)<span>{{ $e }}</span>@endif
        @if($cv->personal_phone)<span>{{ $cv->personal_phone }}</span>@endif
        @if($cv->personal_location)<span>{{ $cv->personal_location }}</span>@endif
    </div>
    @if($cv->summary)<p class="s">{{ $t ? Str::limit($cv->summary,120) : $cv->summary }}</p>@endif
</header>
<div class="b">
    <div>
        <section class="sc"><h2 class="t">Experience</h2>
            @foreach(($t ? $cv->experiences->take(3) : $cv->experiences) as $x)
                <div class="i"><div class="w"><div><p class="p">{{ $x->position }}</p><p class="u">{{ $x->company }}</p></div><span class="d">{{ $x->start_date }}{{ $x->end_date ? ' – '.$x->end_date : ' – Present' }}</span></div>
                @if($x->description)<p class="x">{{ $t ? Str::limit($x->description,100) : $x->description }}</p>@endif</div>
            @endforeach
        </section>
        <section class="sc"><h2 class="t">Education</h2>
            @foreach($cv->educations as $x)
                <div class="i"><div class="w"><div><p class="p">{{ $x->school }}</p><p class="u">{{ $x->degree }}</p></div><span class="d">{{ $x->year }}</span></div></div>
            @endforeach
        </section>
    </div>
    <aside>
        <section class="sc"><h2 class="t">Skills</h2>@foreach($cv->skills as $x)<span class="g">{{ $x->name }}</span>@endforeach</section>
        <section class="sc"><h2 class="t">Contact</h2>
            @if($e)<div class="k">{{ $e }}</div>@endif
            @if($cv->personal_phone)<div class="k">{{ $cv->personal_phone }}</div>@endif
            @if($cv->personal_location)<div class="k">{{ $cv->personal_location }}</div>@endif
        </section>
    </aside>
</div>
@endsection