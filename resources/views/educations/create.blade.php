@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="h3 mb-3">Add Education</h1>
    <div class="text-muted small mb-3">CV: <a href="{{ route('cvs.show', $cv) }}">{{ $cv->title }}</a></div>

    <form method="POST" action="{{ route('cvs.educations.store', $cv) }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">School</label>
            <input name="school" class="form-control" value="{{ old('school') }}" required />
            @error('school')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Degree</label>
            <input name="degree" class="form-control" value="{{ old('degree') }}" required />
            @error('degree')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Year</label>
            <input name="year" class="form-control" value="{{ old('year') }}" required />
            @error('year')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-primary" type="submit">Save</button>
            <a class="btn btn-outline-secondary" href="{{ route('cvs.educations.index', $cv) }}">Cancel</a>
        </div>
    </form>
</div>
@endsection
