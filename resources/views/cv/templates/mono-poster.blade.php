@extends('layouts.app')

@section('content')
@php
    $name = $cv->user->name;
    $email = $cv->user->email;
    $initials = collect(explode(' ', $name))->map(fn($w) => strtoupper(mb_substr($w, 0, 1)))->take(2)->join('');

    $summary = $cv->summary;

    $skills = $cv->skills;
    $exps = $cv->experiences;
    $edus = $cv->educations;
@endphp

{{--
Monochrome poster-style CV (inspired by editorial/Dribbble poster CV layouts):
- strong left vertical name block
- large photo placeholder area (uses initials)
- clean two-column content
- generous spacing, minimal palette
--}}

<div class="bg-zinc-200 min-h-[calc(100vh-64px)] py-10">
    <div class="mx-auto max-w-5xl px-4">
        <div class="bg-white shadow-[0_30px_80px_rgba(0,0,0,0.18)] rounded-none overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-12">
                {{-- Left poster rail --}}
                <aside class="lg:col-span-3 bg-zinc-900 text-white p-8 relative">
                    <div class="sticky top-10">
                        <div class="flex items-start gap-3">
                            <div class="h-10 w-10 rounded-lg bg-white/10 border border-white/10 flex items-center justify-center font-semibold">
                                {{ $initials }}
                            </div>
                            <div>
                                <div class="text-[11px] uppercase tracking-[0.24em] text-white/70">Curriculum Vitae</div>
                                <div class="mt-1 text-sm text-white/85">{{ $cv->title }}</div>
                            </div>
                        </div>

                        {{-- Vertical name --}}
                        <div class="mt-10">
                            <div class="leading-none">
                                <div class="text-5xl font-extrabold tracking-tight [writing-mode:vertical-rl] rotate-180">
                                    {{ strtoupper($name) }}
                                </div>
                            </div>
                            <div class="mt-6 text-xs uppercase tracking-[0.22em] text-white/60">{{ $cv->status }}</div>
                        </div>

                        {{-- Contact --}}
                        <div class="mt-12">
                            <div class="text-[11px] uppercase tracking-[0.22em] text-white/60">Contact</div>
                            <div class="mt-3 space-y-2 text-sm">
                                <div class="text-white/85">{{ $name }}</div>
                                <a class="text-white/80 hover:text-white" href="mailto:{{ $email }}">{{ $email }}</a>
                            </div>
                        </div>

                        {{-- Skills --}}
                        <div class="mt-10">
                            <div class="text-[11px] uppercase tracking-[0.22em] text-white/60">Skills</div>
                            <div class="mt-4 space-y-2">
                                @forelse($skills as $skill)
                                    <div class="flex items-center justify-between gap-3 rounded-xl bg-white/5 border border-white/10 px-3 py-2">
                                        <div class="text-sm font-medium text-white/90">{{ $skill->name }}</div>
                                        <div class="text-xs text-white/60">{{ $skill->level ?: '' }}</div>
                                    </div>
                                @empty
                                    <div class="text-sm text-white/60 italic">No skills.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </aside>

                {{-- Main content --}}
                <main class="lg:col-span-9 p-8 lg:p-10">
                    {{-- Top editorial copy + image placeholder --}}
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                        <div class="lg:col-span-7">
                            <div class="text-[11px] uppercase tracking-[0.22em] text-zinc-500">Profile</div>
                            <h1 class="mt-3 text-3xl font-semibold tracking-tight text-zinc-900">{{ $name }}</h1>
                            <p class="mt-4 text-sm leading-7 text-zinc-600">
                                {{ $summary ?: 'Ringkasan/profil belum diisi. Tambahkan summary untuk membuat tampilan lebih kuat dan profesional.' }}
                            </p>

                            {{-- Small editorial lines --}}
                            <div class="mt-6 space-y-2 text-xs text-zinc-500">
                                <div>Soft monochrome palette · generous spacing · print-friendly.</div>
                                <div>Template: <span class="font-medium text-zinc-700">mono-poster</span></div>
                            </div>
                        </div>

                        <div class="lg:col-span-5">
                            <div class="bg-zinc-100 border border-zinc-200 aspect-[4/3] flex items-center justify-center">
                                <div class="text-center">
                                    <div class="text-xs uppercase tracking-[0.22em] text-zinc-500">Photo</div>
                                    <div class="mt-3 h-24 w-24 rounded-full bg-zinc-900 text-white flex items-center justify-center text-2xl font-semibold">
                                        {{ $initials }}
                                    </div>
                                    <div class="mt-3 text-xs text-zinc-500">(placeholder)</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="my-10 h-px bg-zinc-200"></div>

                    {{-- Two-column: Experience / Education --}}
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                        <section class="lg:col-span-7">
                            <div class="flex items-center justify-between">
                                <h2 class="text-[11px] uppercase tracking-[0.22em] text-zinc-500">Experience</h2>
                            </div>

                            <div class="mt-6 space-y-6">
                                @forelse($exps as $exp)
                                    <article>
                                        <div class="text-sm font-semibold text-zinc-900">{{ $exp->position ?: 'Job Title' }}</div>
                                        <div class="mt-1 text-xs text-zinc-500">{{ $exp->company ?: 'Company name' }} · {{ $exp->start_date }} — {{ $exp->end_date ?? 'Present' }}</div>
                                        @if($exp->description)
                                            <p class="mt-3 text-sm leading-7 text-zinc-600">{{ $exp->description }}</p>
                                        @endif
                                    </article>
                                @empty
                                    <div class="text-sm text-zinc-500 italic">No experience.</div>
                                @endforelse
                            </div>
                        </section>

                        <section class="lg:col-span-5">
                            <h2 class="text-[11px] uppercase tracking-[0.22em] text-zinc-500">Education</h2>
                            <div class="mt-6 space-y-5">
                                @forelse($edus as $edu)
                                    <div>
                                        <div class="text-sm font-semibold text-zinc-900">{{ $edu->degree ?: 'Degree / Major' }}</div>
                                        <div class="mt-1 text-xs text-zinc-500">{{ $edu->school ?: 'University name' }} · {{ $edu->year }}</div>
                                    </div>
                                @empty
                                    <div class="text-sm text-zinc-500 italic">No education.</div>
                                @endforelse
                            </div>
                        </section>
                    </div>

                    <div class="mt-12 text-[11px] text-zinc-400">
                        Tip: untuk hasil paling mirip poster, print A4 / export PDF.
                    </div>
                </main>
            </div>
        </div>
    </div>
</div>
@endsection
