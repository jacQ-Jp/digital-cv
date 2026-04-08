@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 mb-0">Experiences</h1>
            <div class="text-muted small">CV: <a href="{{ route('cvs.show', $cv) }}">{{ $cv->title }}</a></div>
        </div>
        <a class="btn btn-primary" href="{{ route('cvs.experiences.create', $cv) }}">Add Experience</a>
    </div>

    @if($experiences->isEmpty())
        <div class="alert alert-info">No experiences yet.</div>
    @else
        <div class="list-group">
            @foreach($experiences as $exp)
                <a class="list-group-item list-group-item-action" href="{{ route('cvs.experiences.show', [$cv, $exp]) }}">
                    <div class="d-flex justify-content-between">
                        <div class="fw-semibold">{{ $exp->position }} - {{ $exp->company }}</div>
                        <small class="text-muted">{{ $exp->start_date }} - {{ $exp->end_date ?? 'Present' }}</small>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

    <div class="mt-3">
        <a class="btn btn-outline-secondary" href="{{ route('cvs.show', $cv) }}">Back to CV</a>
    </div>
</div>
@endsection
