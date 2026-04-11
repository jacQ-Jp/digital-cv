@extends('layouts.app')

@section('content')
@php
    $name = $cv->user->name;
    $email = $cv->user->email;
    $initials = collect(explode(' ', $name))->map(fn($w) => strtoupper(mb_substr($w, 0, 1)))->take(2)->join('');
@endphp

<div class="bg-slate-100 min-h-[calc(100vh-64px)]">
    <div class="mx-auto max-w-6xl px-4 py-10">
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="grid grid-cols-1 lg:grid-cols-12">
                <aside class="lg:col-span-4 bg-slate-900 text-white p-8">
                    <div class="flex items-start gap-4">
                        <div class="h-14 w-14 rounded-2xl bg-sky-600/20 border border-white/10 flex items-center justify-center font-semibold">
                            {{ $initials }}
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-[0.18em] text-white/60">Sidebar</div>
                            <h1 class="mt-1 text-2xl font-semibold tracking-tight">{{ $name }}</h1>
                            <a class="mt-2 inline-block text-sm text-white/70 hover:text-white" href="mailto:{{ $email }}">{{ $email }}</a>
                        </div>
                    </div>

                    @if($cv->summary)
                        <p class="mt-6 text-sm leading-7 text-white/70">{{ $cv->summary }}</p>
                    @endif

                    <div class="mt-8">
                        <div class="text-xs uppercase tracking-[0.18em] text-white/60">Skills</div>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @forelse($cv->skills as $skill)
                                <span class="inline-flex items-center rounded-xl bg-white/5 border border-white/10 px-3 py-2 text-xs font-medium text-white/90">
                                    {{ $skill->name }}@if($skill->level) <span class="ms-1 text-white/50 font-normal">{{ $skill->level }}</span>@endif
                                </span>
                            @empty
                                <span class="text-sm text-white/60 italic">No skills.</span>
                            @endforelse
                        </div>
                    </div>

                    <div class="mt-8">
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/5 border border-white/10 px-3 py-1 text-xs font-medium text-white/70">
                            <span class="h-2 w-2 rounded-full {{ $cv->status === 'published' ? 'bg-emerald-400' : 'bg-white/30' }}"></span>
                            {{ strtoupper($cv->status) }}
                        </span>
                    </div>
                </aside>

                <main class="lg:col-span-8 p-8">
                    <div class="flex flex-col gap-2 border-b border-slate-200 pb-6">
                        <div class="text-xs uppercase tracking-[0.18em] text-slate-400">{{ $cv->title }}</div>
                        <div class="text-sm text-slate-600">Preview dengan layout sidebar kiri.</div>
                    </div>

                    <section class="mt-8">
                        <h2 class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-400">Experience</h2>
                        <div class="mt-6 space-y-5">
                            @forelse($cv->experiences as $exp)
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                                    <div class="flex flex-wrap items-start justify-between gap-2">
                                        <div>
                                            <div class="text-sm font-semibold text-slate-900">{{ $exp->position }}</div>
                                            <div class="text-sm text-slate-600">{{ $exp->company }}</div>
                                        </div>
                                        <div class="text-xs font-medium text-slate-400">{{ $exp->start_date }} — {{ $exp->end_date ?? 'Present' }}</div>
                                    </div>
                                    @if($exp->description)
                                        <p class="mt-3 text-sm leading-7 text-slate-600">{{ $exp->description }}</p>
                                    @endif
                                </div>
                            @empty
                                <div class="text-sm text-slate-500 italic">No experience.</div>
                            @endforelse
                        </div>
                    </section>

                    <section class="mt-10">
                        <h2 class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-400">Education</h2>
                        <div class="mt-6 space-y-4">
                            @forelse($cv->educations as $edu)
                                <div class="rounded-2xl border border-slate-200 bg-white p-5">
                                    <div class="text-sm font-semibold text-slate-900">{{ $edu->school }}</div>
                                    <div class="mt-1 text-sm text-slate-600">{{ $edu->degree }}</div>
                                    <div class="mt-1 text-xs font-medium text-slate-400">{{ $edu->year }}</div>
                                </div>
                            @empty
                                <div class="text-sm text-slate-500 italic">No education.</div>
                            @endforelse
                        </div>
                    </section>
                </main>
            </div>
        </div>
    </div>
</div>
@endsection
