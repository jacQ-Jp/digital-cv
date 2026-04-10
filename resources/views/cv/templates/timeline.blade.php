@extends('layouts.app')

@section('content')
@php
    $name = $cv->user->name;
    $email = $cv->user->email;
@endphp
<style>
    .tl { position: relative; padding-left: 1.25rem; }
    .tl:before { content: ""; position: absolute; left: 0.25rem; top: 0.25rem; bottom: 0.25rem; width: 2px; background: #e9ecef; }
    .tl-item { position: relative; padding-left: 1.25rem; margin-bottom: 1.25rem; }
    .tl-item:before { content: ""; position: absolute; left: -0.02rem; top: 0.25rem; width: 10px; height: 10px; border-radius: 50%; background: #0d6efd; }
</style>

<div class="container py-5" style="max-width: 900px">
    <div class="mb-4">
        <div class="text-uppercase small text-muted">Curriculum Vitae</div>
        <h1 class="h2 mb-1">{{ $name }}</h1>
        <div class="text-muted">{{ $email }}</div>
        @if($cv->summary)
            <p class="mt-3">{{ $cv->summary }}</p>
        @endif
    </div>

    <div class="row g-4">
        <div class="col-md-7">
            <h2 class="h6 text-uppercase text-muted">Experience (Timeline)</h2>
            <div class="tl mt-3">
                @forelse($cv->experiences as $exp)
                    <div class="tl-item">
                        <div class="d-flex justify-content-between flex-wrap gap-2">
                            <div class="fw-semibold">{{ $exp->position }} — {{ $exp->company }}</div>
                            <div class="text-muted small">{{ $exp->start_date }} — {{ $exp->end_date ?? 'Present' }}</div>
                        </div>
                        @if($exp->description)
                            <div class="mt-1">{{ $exp->description }}</div>
                        @endif
                    </div>
                @empty
                    <div class="text-muted">No experience.</div>
                @endforelse
            </div>

            <h2 class="h6 text-uppercase text-muted mt-4">Education</h2>
            @forelse($cv->educations as $edu)
                <div class="mb-3">
                    <div class="fw-semibold">{{ $edu->school }}</div>
                    <div class="text-muted small">{{ $edu->degree }} · {{ $edu->year }}</div>
                </div>
            @empty
                <div class="text-muted">No education.</div>
            @endforelse
        </div>

        <div class="col-md-5">
            <h2 class="h6 text-uppercase text-muted">Skills</h2>
            <div class="d-flex flex-wrap gap-2 mt-2">
                @forelse($cv->skills as $skill)
                    <span class="badge text-bg-secondary">{{ $skill->name }}@if($skill->level) ({{ $skill->level }})@endif</span>
                @empty
                    <span class="text-muted">No skills.</span>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
