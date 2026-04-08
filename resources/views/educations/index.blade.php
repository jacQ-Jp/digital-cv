@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 mb-0">Educations</h1>
            <div class="text-muted small">CV: <a href="{{ route('cvs.show', $cv) }}">{{ $cv->title }}</a></div>
        </div>
        <a class="btn btn-primary" href="{{ route('cvs.educations.create', $cv) }}">Add Education</a>
    </div>

    @if($educations->isEmpty())
        <div class="alert alert-info">No educations yet.</div>
    @else
        <div class="list-group">
            @foreach($educations as $edu)
                <a class="list-group-item list-group-item-action" href="{{ route('cvs.educations.show', [$cv, $edu]) }}">
                    <div class="d-flex justify-content-between">
                        <div class="fw-semibold">{{ $edu->school }}</div>
                        <small class="text-muted">{{ $edu->year }}</small>
                    </div>
                    <div class="text-muted small">{{ $edu->degree }}</div>
                </a>
            @endforeach
        </div>
    @endif

    <div class="mt-3">
        <a class="btn btn-outline-secondary" href="{{ route('cvs.show', $cv) }}">Back to CV</a>
    </div>
</div>
@endsection
