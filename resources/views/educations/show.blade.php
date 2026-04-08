@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h1 class="h3 mb-1">{{ $education->school }}</h1>
            <div class="text-muted small">{{ $education->degree }} ({{ $education->year }})</div>
            <div class="text-muted small">CV: <a href="{{ route('cvs.show', $cv) }}">{{ $cv->title }}</a></div>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-primary" href="{{ route('cvs.educations.edit', [$cv, $education]) }}">Edit</a>
            <form method="POST" action="{{ route('cvs.educations.destroy', [$cv, $education]) }}" onsubmit="return confirm('Delete this education?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">Delete</button>
            </form>
        </div>
    </div>

    <div class="mt-3 d-flex gap-2">
        <a class="btn btn-outline-secondary" href="{{ route('cvs.educations.index', $cv) }}">Back</a>
        <a class="btn btn-outline-secondary" href="{{ route('cvs.show', $cv) }}">Back to CV</a>
    </div>
</div>
@endsection
