@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="h3 mb-3">Edit Experience</h1>
    <div class="text-muted small mb-3">CV: <a href="{{ route('cvs.show', $cv) }}">{{ $cv->title }}</a></div>

    <form method="POST" action="{{ route('cvs.experiences.update', [$cv, $experience]) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Company</label>
            <input name="company" class="form-control" value="{{ old('company', $experience->company) }}" required />
            @error('company')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Position</label>
            <input name="position" class="form-control" value="{{ old('position', $experience->position) }}" required />
            @error('position')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="row g-2">
            <div class="col-md-6 mb-3">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $experience->start_date) }}" required />
                @error('start_date')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $experience->end_date) }}" />
                @error('end_date')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4">{{ old('description', $experience->description) }}</textarea>
            @error('description')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-primary" type="submit">Update</button>
            <a class="btn btn-outline-secondary" href="{{ route('cvs.experiences.show', [$cv, $experience]) }}">Cancel</a>
        </div>
    </form>
</div>
@endsection
