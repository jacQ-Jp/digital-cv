@extends('layouts.app')

@section('content')
@php
    $name = $cv->user->name;
    $email = $cv->user->email;
    $initials = collect(explode(' ', $name))->map(fn($w) => strtoupper(mb_substr($w, 0, 1)))->take(2)->join('');
@endphp

<div class="bg-slate-100 min-h-[calc(100vh-64px)]">
    <div class="mx-auto max-w-5xl px-4 py-10">
        <div class="grid grid-cols-1 gap-6">
            <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-8">
                <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                    <div class="flex items-center gap-4">
                        <div class="h-16 w-16 rounded-2xl bg-white border border-slate-200 flex items-center justify-center">
                            <div class="h-12 w-12 rounded-xl bg-slate-900 text-white flex items-center justify-center font-semibold">
                                {{ $initials }}
                            </div>
                        </div>
                        <div>
                            <h1 class="text-3xl font-semibold tracking-tight text-slate-900">{{ $name }}</h1>
                            <a href="mailto:{{ $email }}" class="text-sm text-slate-500 hover:text-slate-700">{{ $email }}</a>
                            <div class="mt-2 text-sm font-medium text-slate-700">{{ $cv->title }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center rounded-full bg-sky-600 px-3 py-1 text-xs font-semibold text-white">Card</span>
                        <span class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-medium text-slate-600">
                            {{ strtoupper($cv->status) }}
                        </span>
                    </div>
                </div>

                @if($cv->summary)
                    <p class="mt-6 max-w-3xl text-sm leading-7 text-slate-600">{{ $cv->summary }}</p>
                @endif
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
                <section class="lg:col-span-7">
                    <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-7">
                        <div class="flex items-center justify-between">
                            <h2 class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-400">Experience</h2>
                        </div>
                        <div class="mt-6 space-y-4">
                            @forelse($cv->experiences as $exp)
                                <article class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
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
                                </article>
                            @empty
                                <div class="text-sm text-slate-500 italic">No experience.</div>
                            @endforelse
                        </div>
                    </div>
                </section>

                <aside class="lg:col-span-5">
                    <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-7">
                        <h2 class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-400">Education</h2>
                        <div class="mt-5 space-y-4">
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

                        <div class="mt-8">
                            <h2 class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-400">Skills</h2>
                            <div class="mt-4 flex flex-wrap gap-2">
                                @forelse($cv->skills as $skill)
                                    <span class="inline-flex items-center rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-medium text-slate-700">
                                        {{ $skill->name }}@if($skill->level) <span class="ms-1 text-slate-400 font-normal">{{ $skill->level }}</span>@endif
                                    </span>
                                @empty
                                    <span class="text-sm text-slate-500 italic">No skills.</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</div>
@endsection
