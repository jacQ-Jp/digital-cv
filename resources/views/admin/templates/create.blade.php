@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="h3 mb-3">Add Template</h1>

    <form method="POST" action="{{ route('admin.templates.store') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Name</label>
            <input name="name" class="form-control" value="{{ old('name') }}" required>
            @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Slug</label>
            <input name="slug" class="form-control" value="{{ old('slug') }}" required>
            <div class="form-text">Harus sama dengan nama view di <code>resources/views/cv/templates/{slug}.blade.php</code></div>
            @error('slug')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
            @error('description')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Thumbnail URL/Path</label>
            <input name="thumbnail" class="form-control" value="{{ old('thumbnail') }}">
            @error('thumbnail')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="form-check mb-2">
            <input id="is_active" class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', true))>
            <label for="is_active" class="form-check-label">Active</label>
        </div>

        <div class="form-check mb-3">
            <input id="is_default" class="form-check-input" type="checkbox" name="is_default" value="1" @checked(old('is_default'))>
            <label for="is_default" class="form-check-label">Set as default (will force active)</label>
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-primary" type="submit">Save</button>
            <a class="btn btn-outline-secondary" href="{{ route('admin.templates.index') }}">Cancel</a>
        </div>
    </form>
</div>
@endsection
