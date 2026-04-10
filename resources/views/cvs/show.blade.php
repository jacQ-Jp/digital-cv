@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h1 class="h3 mb-1">{{ $cv->title }}</h1>
            <div class="text-muted small">Status: {{ $cv->status }} | Template: {{ $cv->template?->name ?? $cv->template_slug }}</div>
            <div class="mt-2 d-flex flex-wrap gap-2">
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('cvs.render', $cv) }}">Preview Render</a>
                @if($cv->status === 'published')
                    <a class="btn btn-sm btn-outline-success" href="{{ route('cvs.public', $cv) }}" target="_blank" rel="noopener">Public Link</a>
                @endif
            </div>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-primary" href="{{ route('cvs.edit', $cv) }}">Edit</a>
            <form method="POST" action="{{ route('cvs.destroy', $cv) }}" onsubmit="return confirm('Delete this CV?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">Delete</button>
            </form>
        </div>
    </div>

    @if($cv->summary)
        <div class="card mb-3">
            <div class="card-body">
                <h2 class="h6">Summary</h2>
                <p class="mb-0">{{ $cv->summary }}</p>
            </div>
        </div>
    @endif

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h2 class="h6 mb-0">Experiences</h2>
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('cvs.experiences.index', $cv) }}">Manage</a>
                    </div>
                    @forelse($cv->experiences as $exp)
                        <div class="border-bottom pb-2 mb-2">
                            <div class="fw-semibold">{{ $exp->position }} - {{ $exp->company }}</div>
                            <div class="text-muted small">{{ $exp->start_date }} - {{ $exp->end_date ?? 'Present' }}</div>
                            @if($exp->description)
                                <div class="small">{{ $exp->description }}</div>
                            @endif
                        </div>
                    @empty
                        <div class="text-muted">No experiences yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h2 class="h6 mb-0">Educations</h2>
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('cvs.educations.index', $cv) }}">Manage</a>
                    </div>
                    @forelse($cv->educations as $edu)
                        <div class="border-bottom pb-2 mb-2">
                            <div class="fw-semibold">{{ $edu->school }}</div>
                            <div class="text-muted small">{{ $edu->degree }} ({{ $edu->year }})</div>
                        </div>
                    @empty
                        <div class="text-muted">No educations yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-0">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h2 class="h6 mb-0">Skills</h2>
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('cvs.skills.index', $cv) }}">Manage</a>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        @forelse($cv->skills as $skill)
                            <span class="badge text-bg-secondary">{{ $skill->name }}@if($skill->level) ({{ $skill->level }})@endif</span>
                        @empty
                            <span class="text-muted">No skills yet.</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <a class="btn btn-outline-secondary" href="{{ route('cvs.index') }}">Back</a>
    </div>
</div>
@endsection
