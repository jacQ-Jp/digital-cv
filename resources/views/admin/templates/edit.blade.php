@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="h3 mb-3">Edit Template</h1>

    @if($errors->has('status'))
        <div class="alert alert-danger">{{ $errors->first('status') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.templates.update', $template) }}" enctype="multipart/form-data">
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
            <div class="form-text">Harus sama dengan nama view di <code>resources/views/cv/templates/{slug}.blade.php</code></div>
            @error('slug')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description', $template->description) }}</textarea>
            @error('description')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Thumbnail (JPG/PNG)</label>
            @if($template->thumbnail)
                <div class="mb-2">
                    <img src="{{ asset('storage/'.$template->thumbnail) }}" alt="Thumbnail {{ $template->name }}" style="width:180px;max-width:100%;height:auto;border:1px solid #e2e8f0;border-radius:8px;">
                </div>
            @endif
            <input type="file" name="thumbnail" class="form-control" accept="image/jpeg,image/png">
            <div class="form-text">Kosongkan jika thumbnail tidak ingin diganti.</div>
            @error('thumbnail')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
            <label class="form-label d-block">Status</label>

            <input type="hidden" name="is_default" value="0">
            <div class="form-check form-switch">
                <input
                    class="form-check-input"
                    type="checkbox"
                    role="switch"
                    id="is_default"
                    name="is_default"
                    value="1"
                    @checked((string) old('is_default', $template->is_default ? '1' : '0') === '1')
                    @disabled($hasAnotherDefault && ! $template->is_default)
                >
                <label class="form-check-label" for="is_default">Default</label>
            </div>
            <div class="form-text">Hanya satu template yang boleh default. Jika dipilih default, template otomatis active.</div>
            @if($hasAnotherDefault && ! $template->is_default)
                <div class="form-text text-warning">Sudah ada template default lain, jadi opsi ini dinonaktifkan.</div>
            @endif
            @error('is_default')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-primary" type="submit">Save changes</button>
            <a class="btn btn-outline-secondary" href="{{ route('admin.templates.index') }}">Back</a>
        </div>
    </form>
</div>
@endsection
