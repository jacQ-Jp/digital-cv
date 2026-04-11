@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Skills — {{ $cv->title }}</h1>
        <a class="btn btn-primary" href="{{ route('cvs.skills.create', $cv) }}">Add Skill</a>
    </div>

    <div class="mb-3">
        <a class="btn btn-outline-secondary btn-sm" href="{{ route('cvs.show', $cv) }}">Back to CV</a>
    </div>

    @if($skills->isEmpty())
        <div class="alert alert-info">No skills yet.</div>
    @else
        <div class="list-group">
            @foreach($skills as $skill)
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-semibold">{{ $skill->name }}</div>
                        @if($skill->level)
                            <div class="text-muted small">Level: {{ $skill->level }}</div>
                        @endif
                    </div>
                    <div class="d-flex gap-2">
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('cvs.skills.edit', [$cv, $skill]) }}">Edit</a>
                        <form method="POST" action="{{ route('cvs.skills.destroy', [$cv, $skill]) }}" onsubmit="return confirm('Delete this skill?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
