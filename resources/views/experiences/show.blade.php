@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h1 class="h3 mb-1">{{ $experience->position }} - {{ $experience->company }}</h1>
            <div class="text-muted small">{{ $experience->start_date }} - {{ $experience->end_date ?? 'Present' }}</div>
            <div class="text-muted small">CV: <a href="{{ route('cvs.show', $cv) }}">{{ $cv->title }}</a></div>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-primary" href="{{ route('cvs.experiences.edit', [$cv, $experience]) }}">Edit</a>
            <form method="POST" action="{{ route('cvs.experiences.destroy', [$cv, $experience]) }}" onsubmit="return confirm('Delete this experience?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">Delete</button>
            </form>
        </div>
    </div>

    @if($experience->description)
        <div class="card">
            <div class="card-body">
                <h2 class="h6">Description</h2>
                <p class="mb-0">{{ $experience->description }}</p>
            </div>
        </div>
    @endif

    <div class="mt-3 d-flex gap-2">
        <a class="btn btn-outline-secondary" href="{{ route('cvs.experiences.index', $cv) }}">Back</a>
        <a class="btn btn-outline-secondary" href="{{ route('cvs.show', $cv) }}">Back to CV</a>
    </div>
</div>
@endsection
