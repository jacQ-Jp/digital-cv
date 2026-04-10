@extends('layouts.app')

@section('content')
@php
    $name = $cv->user->name;
    $email = $cv->user->email;
@endphp
<div class="container py-4">
    <div class="border-bottom pb-3 mb-4">
        <div class="d-flex align-items-end justify-content-between flex-wrap gap-2">
            <div>
                <h1 class="h2 mb-1">{{ $name }}</h1>
                <div class="text-muted">{{ $email }}</div>
            </div>
            <div class="text-muted small">
                <div class="fw-semibold">{{ $cv->title }}</div>
                <div>{{ ucfirst($cv->status) }}</div>
            </div>
        </div>
        @if($cv->summary)
            <p class="mt-3 mb-0">{{ $cv->summary }}</p>
        @endif
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <h2 class="h5 mb-3">Experience</h2>
            @forelse($cv->experiences as $exp)
                <div class="mb-4">
                    <div class="d-flex justify-content-between flex-wrap gap-2">
                        <div class="fw-semibold">{{ $exp->position }} · {{ $exp->company }}</div>
                        <div class="text-muted small">{{ $exp->start_date }} — {{ $exp->end_date ?? 'Present' }}</div>
                    </div>
                    @if($exp->description)
                        <div class="mt-1">{{ $exp->description }}</div>
                    @endif
                </div>
            @empty
                <div class="text-muted">No experience.</div>
            @endforelse

            <h2 class="h5 mt-4 mb-3">Education</h2>
            @forelse($cv->educations as $edu)
                <div class="mb-3">
                    <div class="fw-semibold">{{ $edu->school }}</div>
                    <div class="text-muted small">{{ $edu->degree }} · {{ $edu->year }}</div>
                </div>
            @empty
                <div class="text-muted">No education.</div>
            @endforelse
        </div>

        <div class="col-lg-4">
            <div class="p-3 bg-light border rounded-3">
                <h2 class="h6 mb-3">Skills</h2>
                <div class="d-flex flex-wrap gap-2">
                    @forelse($cv->skills as $skill)
                        <span class="badge text-bg-dark">{{ $skill->name }}@if($skill->level) ({{ $skill->level }})@endif</span>
                    @empty
                        <span class="text-muted">No skills.</span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
