@extends('layouts.app')

@section('content')
@php
    $name = $cv->user->name;
    $email = $cv->user->email;
@endphp

<div class="bg-slate-50 min-h-[calc(100vh-64px)]">
    <div class="mx-auto max-w-5xl px-4 py-10">
        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="px-8 py-10">
                <div class="flex flex-col gap-2">
                    <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Timeline</div>
                    <h1 class="text-3xl font-semibold tracking-tight text-slate-900">{{ $name }}</h1>
                    <a href="mailto:{{ $email }}" class="text-sm text-slate-500 hover:text-slate-700">{{ $email }}</a>
                    <div class="mt-2 text-sm font-medium text-slate-700">{{ $cv->title }}</div>
                </div>

                @if($cv->summary)
                    <p class="mt-6 max-w-3xl text-sm leading-7 text-slate-600">{{ $cv->summary }}</p>
                @endif

                <div class="mt-10 grid grid-cols-1 gap-10 lg:grid-cols-12">
                    <section class="lg:col-span-7">
                        <div class="flex items-center justify-between">
                            <h2 class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-400">Experience</h2>
                        </div>
                        <div class="mt-6 relative">
                            <div class="absolute left-3 top-0 bottom-0 w-px bg-slate-200"></div>
                            <div class="space-y-6">
                                @forelse($cv->experiences as $exp)
                                    <div class="relative pl-10">
                                        <div class="absolute left-[0.55rem] top-2 h-3 w-3 rounded-full bg-sky-600 ring-4 ring-sky-50"></div>
                                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
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
                                    </div>
                                @empty
                                    <div class="text-sm text-slate-500 italic">No experience.</div>
                                @endforelse
                            </div>
                        </div>
                    </section>

                    <aside class="lg:col-span-5">
                        <div class="rounded-3xl border border-slate-200 bg-white p-6">
                            <h2 class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-400">Education</h2>
                            <div class="mt-5 space-y-4">
                                @forelse($cv->educations as $edu)
                                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
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
                                        <span class="inline-flex items-center rounded-xl bg-slate-50 border border-slate-200 px-3 py-2 text-xs font-medium text-slate-700">
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
</div>
@endsection
