@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="h3 mb-3">Edit Template</h1>

    <form method="POST" action="{{ route('admin.templates.update', $template) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Name</label>
            <input name="name" class="form-control" value="{{ old('name', $template->name) }}" required>
            @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Slug</label>
            <input name="slug" class="form-control" value="{{ old('slug', $template->slug) }}" required>
            @error('slug')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description', $template->description) }}</textarea>
            @error('description')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Thumbnail URL/Path</label>
            <input name="thumbnail" class="form-control" value="{{ old('thumbnail', $template->thumbnail) }}">
            @error('thumbnail')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="form-check mb-2">
            <input id="is_active" class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', $template->is_active))>
            <label for="is_active" class="form-check-label">Active</label>
        </div>

        <div class="form-check mb-3">
            <input id="is_default" class="form-check-input" type="checkbox" name="is_default" value="1" @checked(old('is_default', $template->is_default))>
            <label for="is_default" class="form-check-label">Default (forces active; only one allowed)</label>
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-primary" type="submit">Save changes</button>
            <a class="btn btn-outline-secondary" href="{{ route('admin.templates.index') }}">Back</a>
        </div>
    </form>
</div>
@endsection
