@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mb-3">
        <h1 class="h3 mb-1">{{ $cv->user->name }}</h1>
        <div class="text-muted">{{ $cv->user->email }}</div>
        @if($cv->summary)
            <p class="mt-3">{{ $cv->summary }}</p>
        @endif
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h2 class="h6">Experience</h2>
                    @forelse($cv->experiences as $exp)
                        <div class="mb-2">
                            <div class="fw-semibold">{{ $exp->position }} — {{ $exp->company }}</div>
                            <div class="text-muted small">{{ $exp->start_date }} - {{ $exp->end_date ?? 'Present' }}</div>
                            @if($exp->description)
                                <div class="small">{{ $exp->description }}</div>
                            @endif
                        </div>
                    @empty
                        <div class="text-muted">No experience.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h2 class="h6">Education</h2>
                    @forelse($cv->educations as $edu)
                        <div class="mb-2">
                            <div class="fw-semibold">{{ $edu->school }}</div>
                            <div class="text-muted small">{{ $edu->degree }} ({{ $edu->year }})</div>
                        </div>
                    @empty
                        <div class="text-muted">No education.</div>
                    @endforelse
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <h2 class="h6">Skills</h2>
                    <div class="d-flex flex-wrap gap-2">
                        @forelse($cv->skills as $skill)
                            <span class="badge text-bg-secondary">{{ $skill->name }}@if($skill->level) ({{ $skill->level }})@endif</span>
                        @empty
                            <span class="text-muted">No skills.</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
