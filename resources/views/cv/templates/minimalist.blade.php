@extends('layouts.app')

@section('content')
@php
    $name = $cv->user->name;
    $email = $cv->user->email;
    $initials = collect(explode(' ', $name))->map(fn($w) => strtoupper(mb_substr($w, 0, 1)))->take(2)->join('');
@endphp

<div class="bg-slate-50 min-h-[calc(100vh-64px)]">
    <div class="mx-auto max-w-5xl px-4 py-10">
        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="px-8 py-10 bg-gradient-to-r from-white via-slate-50 to-sky-50">
                <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                    <div class="flex items-center gap-4">
                        <div class="h-14 w-14 rounded-2xl bg-sky-600 text-white flex items-center justify-center font-semibold tracking-wide">
                            {{ $initials }}
                        </div>
                        <div>
                            <h1 class="text-2xl md:text-3xl font-semibold tracking-tight text-slate-900">{{ $name }}</h1>
                            <a class="text-sm text-slate-500 hover:text-slate-700" href="mailto:{{ $email }}">{{ $email }}</a>
                            <div class="mt-2 text-xs uppercase tracking-[0.18em] text-slate-400">{{ $cv->title }}</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-medium text-slate-600">
                            <span class="h-2 w-2 rounded-full {{ $cv->status === 'published' ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                            {{ strtoupper($cv->status) }}
                        </span>
                        <span class="hidden md:inline-flex items-center gap-2 rounded-full bg-slate-900 px-3 py-1 text-xs font-medium text-white">
                            Minimal
                        </span>
                    </div>
                </div>

                @if($cv->summary)
                    <p class="mt-6 max-w-3xl text-sm leading-7 text-slate-600">
                        {{ $cv->summary }}
                    </p>
                @endif
            </div>

            <div class="px-8 py-10">
                <div class="grid grid-cols-1 gap-10 lg:grid-cols-12">
                    <div class="lg:col-span-7">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-xl bg-sky-50 border border-sky-100 flex items-center justify-center text-sky-700">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 7h20"/><path d="M6 7V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v2"/><path d="M4 7v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7"/><path d="M9 13h6"/></svg>
                            </div>
                            <h2 class="text-sm font-semibold tracking-[0.18em] uppercase text-slate-400">Experience</h2>
                        </div>

                        <div class="mt-6 space-y-6">
                            @forelse($cv->experiences as $exp)
                                <div class="rounded-2xl border border-slate-200 bg-white p-5">
                                    <div class="flex flex-wrap items-start justify-between gap-2">
                                        <div>
                                            <div class="text-sm font-semibold text-slate-900">{{ $exp->position }}</div>
                                            <div class="text-sm text-slate-600">{{ $exp->company }}</div>
                                        </div>
                                        <div class="text-xs font-medium text-slate-400">
                                            {{ $exp->start_date }} — {{ $exp->end_date ?? 'Present' }}
                                        </div>
                                    </div>
                                    @if($exp->description)
                                        <p class="mt-3 text-sm leading-6 text-slate-600">{{ $exp->description }}</p>
                                    @endif
                                </div>
                            @empty
                                <div class="text-sm text-slate-500 italic">No experience.</div>
                            @endforelse
                        </div>
                    </div>

                    <div class="lg:col-span-5">
                        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6">
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-700">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l10 6-10 6L2 8l10-6Z"/><path d="M2 8v8l10 6 10-6V8"/></svg>
                                </div>
                                <h2 class="text-sm font-semibold tracking-[0.18em] uppercase text-slate-400">Education</h2>
                            </div>

                            <div class="mt-5 space-y-4">
                                @forelse($cv->educations as $edu)
                                    <div class="rounded-2xl bg-white border border-slate-200 p-4">
                                        <div class="text-sm font-semibold text-slate-900">{{ $edu->school }}</div>
                                        <div class="mt-1 text-sm text-slate-600">{{ $edu->degree }}</div>
                                        <div class="mt-1 text-xs font-medium text-slate-400">{{ $edu->year }}</div>
                                    </div>
                                @empty
                                    <div class="text-sm text-slate-500 italic">No education.</div>
                                @endforelse
                            </div>

                            <div class="mt-8 flex items-center gap-3">
                                <div class="h-9 w-9 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-700">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="M2 12h20"/></svg>
                                </div>
                                <h2 class="text-sm font-semibold tracking-[0.18em] uppercase text-slate-400">Skills</h2>
                            </div>

                            <div class="mt-4 flex flex-wrap gap-2">
                                @forelse($cv->skills as $skill)
                                    <span class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-700">
                                        {{ $skill->name }}
                                        @if($skill->level)
                                            <span class="text-slate-400 font-normal">{{ $skill->level }}</span>
                                        @endif
                                    </span>
                                @empty
                                    <span class="text-sm text-slate-500 italic">No skills.</span>
                                @endforelse
                            </div>
                        </div>

                        <div class="mt-6 text-xs text-slate-400">
                            Tip: gunakan template lain untuk tampilan sidebar / card / ATS.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection