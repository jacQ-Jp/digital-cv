@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="h3 mb-3">Add Template</h1>

    @if($errors->has('status'))
        <div class="alert alert-danger">{{ $errors->first('status') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.templates.store') }}" enctype="multipart/form-data">
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
            <label class="form-label">Thumbnail (JPG/PNG)</label>
            <input type="file" name="thumbnail" class="form-control" accept="image/jpeg,image/png" required>
            <div class="form-text">Wajib upload gambar preview template (format: .jpg atau .png).</div>
            @error('thumbnail')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
            <label class="form-label d-block">Status</label>

            <div class="mb-3">
                <input type="hidden" name="is_active" value="0">
                <div class="form-check form-switch">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        role="switch"
                        id="is_active"
                        name="is_active"
                        value="1"
                        @checked((string) old('is_active', '1') === '1')
                    >
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
                <div class="form-text">Template aktif bisa dipilih saat membuat CV baru.</div>
                @error('is_active')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>

            <input type="hidden" name="is_default" value="0">
            <div class="form-check form-switch">
                <input
                    class="form-check-input"
                    type="checkbox"
                    role="switch"
                    id="is_default"
                    name="is_default"
                    value="1"
                    @checked((string) old('is_default', '0') === '1')
                >
                <label class="form-check-label" for="is_default">Default</label>
            </div>
            <div class="form-text">Hanya satu template yang boleh default. Jika dipilih default, template otomatis active. Jika sudah ada template default, yang lama otomatis tidak menjadi default.</div>
            @error('is_default')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-primary" type="submit">Save</button>
            <a class="btn btn-outline-secondary" href="{{ route('admin.templates.index') }}">Cancel</a>
        </div>
    </form>
</div>
@endsection
