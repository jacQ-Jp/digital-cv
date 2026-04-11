@extends('layouts.app')

@section('content')
@php
    $name = $cv->user->name;
    $email = $cv->user->email;
    $initials = collect(explode(' ', $name))->map(fn($w) => strtoupper(mb_substr($w, 0, 1)))->take(2)->join('');
@endphp

<div class="bg-slate-50 min-h-[calc(100vh-64px)]">
    <div class="mx-auto max-w-6xl px-4 py-10">
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="grid grid-cols-1 lg:grid-cols-12">
                <aside class="lg:col-span-4 bg-gradient-to-b from-sky-500 to-blue-700 text-white p-8">
                    <div class="flex items-start gap-4">
                        <div class="h-16 w-16 rounded-2xl bg-white/15 border border-white/20 flex items-center justify-center font-semibold tracking-wide text-lg">
                            {{ $initials }}
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-[0.2em] text-white/80">Profile</div>
                            <h1 class="mt-2 text-2xl font-semibold tracking-tight">{{ $name }}</h1>
                            <a class="mt-2 inline-block text-sm text-white/85 hover:text-white" href="mailto:{{ $email }}">{{ $email }}</a>
                            <div class="mt-4">
                                <span class="inline-flex items-center rounded-full bg-white/15 border border-white/20 px-3 py-1 text-xs font-semibold">
                                    {{ strtoupper($cv->status) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    @if($cv->summary)
                        <p class="mt-6 text-sm leading-7 text-white/85">{{ $cv->summary }}</p>
                    @endif

                    <div class="mt-10">
                        <div class="text-xs uppercase tracking-[0.18em] text-white/80">Skills</div>
                        <div class="mt-4 space-y-2">
                            @forelse($cv->skills as $skill)
                                <div class="flex items-center justify-between gap-3 rounded-2xl bg-white/10 border border-white/15 px-4 py-3">
                                    <div class="text-sm font-semibold">{{ $skill->name }}</div>
                                    <div class="text-xs text-white/70">{{ $skill->level ?: '' }}</div>
                                </div>
                            @empty
                                <div class="text-sm text-white/80 italic">No skills.</div>
                            @endforelse
                        </div>
                    </div>
                </aside>

                <main class="lg:col-span-8 p-8">
                    <div class="flex items-center justify-between border-b border-slate-200 pb-6">
                        <div>
                            <div class="text-xs uppercase tracking-[0.18em] text-slate-400">{{ $cv->title }}</div>
                            <div class="mt-2 text-sm text-slate-600">Layout profile-sidebar dengan emphasis di sisi kiri.</div>
                        </div>
                        <span class="hidden md:inline-flex items-center rounded-full bg-slate-900 px-3 py-1 text-xs font-semibold text-white">Profile Sidebar</span>
                    </div>

                    <section class="mt-8">
                        <h2 class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-400">Experience</h2>
                        <div class="mt-6 space-y-5">
                            @forelse($cv->experiences as $exp)
                                <div class="rounded-3xl border border-slate-200 bg-white p-6">
                                    <div class="flex flex-wrap items-start justify-between gap-2">
                                        <div>
                                            <div class="text-sm font-semibold text-slate-900">{{ $exp->position }} <span class="text-slate-400 font-normal">—</span> {{ $exp->company }}</div>
                                            <div class="mt-1 text-xs font-medium text-slate-400">{{ $exp->start_date }} — {{ $exp->end_date ?? 'Present' }}</div>
                                        </div>
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
                                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6">
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
