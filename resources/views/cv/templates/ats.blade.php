@extends('layouts.app')

@section('content')
@php
    $name = $cv->user->name;
    $email = $cv->user->email;
@endphp

{{--
 ATS-friendly template:
 - simple structure
 - minimal styling
 - high contrast
 - easy to export/print
--}}
<div class="bg-white">
    <div class="mx-auto max-w-4xl px-6 py-10">
        <header class="border-b border-slate-200 pb-6">
            <h1 class="text-3xl font-semibold tracking-tight text-slate-900">{{ $name }}</h1>
            <div class="mt-2 text-sm text-slate-600">{{ $email }}</div>
            <div class="mt-2 text-sm font-medium text-slate-800">{{ $cv->title }}</div>
            @if($cv->summary)
                <p class="mt-4 text-sm leading-7 text-slate-700">{{ $cv->summary }}</p>
            @endif
        </header>

        <main class="mt-8 space-y-10">
            <section>
                <h2 class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-500">Experience</h2>
                <div class="mt-4 space-y-5">
                    @forelse($cv->experiences as $exp)
                        <article>
                            <div class="flex flex-wrap items-baseline justify-between gap-2">
                                <div class="font-semibold text-slate-900">{{ $exp->position }} — {{ $exp->company }}</div>
                                <div class="text-xs text-slate-500">{{ $exp->start_date }} — {{ $exp->end_date ?? 'Present' }}</div>
                            </div>
                            @if($exp->description)
                                <p class="mt-2 text-sm leading-7 text-slate-700">{{ $exp->description }}</p>
                            @endif
                        </article>
                    @empty
                        <div class="text-sm text-slate-600 italic">No experience.</div>
                    @endforelse
                </div>
            </section>

            <section>
                <h2 class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-500">Education</h2>
                <div class="mt-4 space-y-4">
                    @forelse($cv->educations as $edu)
                        <div>
                            <div class="font-semibold text-slate-900">{{ $edu->school }}</div>
                            <div class="text-sm text-slate-700">{{ $edu->degree }} · {{ $edu->year }}</div>
                        </div>
                    @empty
                        <div class="text-sm text-slate-600 italic">No education.</div>
                    @endforelse
                </div>
            </section>

            <section>
                <h2 class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-500">Skills</h2>
                <div class="mt-4">
                    @if($cv->skills->isEmpty())
                        <div class="text-sm text-slate-600 italic">No skills.</div>
                    @else
                        <p class="text-sm leading-7 text-slate-700">
                            {{ $cv->skills->map(fn($s) => trim($s->name . ($s->level ? " ({$s->level})" : '')))->implode(', ') }}
                        </p>
                    @endif
                </div>
            </section>
        </main>

        <footer class="mt-12 border-t border-slate-200 pt-6 text-xs text-slate-500">
            ATS-friendly layout (simple typography, minimal decoration).
        </footer>
    </div>
</div>
@endsection
