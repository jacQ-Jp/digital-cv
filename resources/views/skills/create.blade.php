@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 720px;">
    <h1 class="h4 mb-3">Add Skill — {{ $cv->title }}</h1>

    <form method="POST" action="{{ route('cvs.skills.store', $cv) }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Name</label>
            <input name="name" class="form-control" value="{{ old('name') }}" required>
            @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Level</label>
            <input name="level" class="form-control" value="{{ old('level') }}" placeholder="e.g. beginner / intermediate / expert">
            @error('level')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-primary" type="submit">Save</button>
            <a class="btn btn-outline-secondary" href="{{ route('cvs.skills.index', $cv) }}">Cancel</a>
        </div>
    </form>
</div>
@endsection
