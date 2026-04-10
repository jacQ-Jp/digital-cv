@extends('layouts.app')

@section('content')
@php
    $name = $cv->user->name;
    $email = $cv->user->email;
@endphp
<div class="container-fluid p-0">
    <div class="row g-0 min-vh-100">
        <aside class="col-md-4 col-lg-3 bg-dark text-white p-4">
            <div class="mb-4">
                <div class="text-uppercase small opacity-75">Curriculum Vitae</div>
                <h1 class="h3 mb-1">{{ $name }}</h1>
                <div class="opacity-75">{{ $email }}</div>
                @if($cv->summary)
                    <p class="mt-3 opacity-75">{{ $cv->summary }}</p>
                @endif
            </div>

            <div class="mt-4">
                <h2 class="h6 text-uppercase opacity-75">Skills</h2>
                <div class="d-flex flex-wrap gap-2">
                    @forelse($cv->skills as $skill)
                        <span class="badge text-bg-light">{{ $skill->name }}@if($skill->level) ({{ $skill->level }})@endif</span>
                    @empty
                        <span class="opacity-75">No skills.</span>
                    @endforelse
                </div>
            </div>
        </aside>

        <main class="col-md-8 col-lg-9 p-4">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4 border-bottom pb-3">
                <div>
                    <h2 class="h5 mb-1">{{ $cv->title }}</h2>
                    <div class="text-muted small">Status: {{ ucfirst($cv->status) }}</div>
                </div>
            </div>

            <section class="mb-4">
                <h3 class="h6 text-uppercase text-muted">Experience</h3>
                @forelse($cv->experiences as $exp)
                    <div class="mb-3">
                        <div class="fw-semibold">{{ $exp->position }} — {{ $exp->company }}</div>
                        <div class="text-muted small">{{ $exp->start_date }} - {{ $exp->end_date ?? 'Present' }}</div>
                        @if($exp->description)
                            <div class="mt-1">{{ $exp->description }}</div>
                        @endif
                    </div>
                @empty
                    <div class="text-muted">No experience.</div>
                @endforelse
            </section>

            <section>
                <h3 class="h6 text-uppercase text-muted">Education</h3>
                @forelse($cv->educations as $edu)
                    <div class="mb-3">
                        <div class="fw-semibold">{{ $edu->school }}</div>
                        <div class="text-muted small">{{ $edu->degree }} · {{ $edu->year }}</div>
                    </div>
                @empty
                    <div class="text-muted">No education.</div>
                @endforelse
            </section>
        </main>
    </div>
</div>
@endsection
