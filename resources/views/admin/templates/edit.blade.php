@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="h3 mb-3">Edit Template</h1>

    @if(!empty($isUsed))
        <div class="alert alert-info">
            Template ini sudah dipakai oleh pengguna. Sesuai aturan, template <strong>tidak boleh</strong> diubah menjadi <strong>inactive</strong>.
        </div>
    @endif

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
            <div class="form-text">Harus sama dengan nama view di <code>resources/views/cv/templates/{slug}.blade.php</code></div>
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

        @php
            $oldIsDefault = (bool) old('is_default', $template->is_default);
            $oldIsActive = (bool) old('is_active', $template->is_active);
            $activeLockedByDefault = $oldIsDefault;
            $activeLockedByUsed = !empty($isUsed) && $template->is_active;
            $disableActive = $activeLockedByDefault || $activeLockedByUsed;

            // If default is checked, active must be true.
            $effectiveActiveChecked = $oldIsDefault ? true : $oldIsActive;
        @endphp

        <div class="form-check mb-2">
            <input id="is_active" class="form-check-input" type="checkbox" name="is_active" value="1"
                @checked($effectiveActiveChecked)
                @disabled($disableActive)
            >
            <label for="is_active" class="form-check-label">Active</label>
            @if($activeLockedByDefault)
                <div class="form-text">Karena <strong>Default</strong> dicentang, template akan selalu <strong>Active</strong>.</div>
            @elseif($activeLockedByUsed)
                <div class="form-text">Tidak bisa dinonaktifkan karena template sudah dipakai oleh pengguna.</div>
            @endif
        </div>

        <div class="form-check mb-3">
            <input id="is_default" class="form-check-input" type="checkbox" name="is_default" value="1" @checked($oldIsDefault)>
            <label for="is_default" class="form-check-label">Default (forces active; only one allowed)</label>
            <div class="form-text">Sistem hanya mengizinkan <strong>1</strong> template default. Mengatur template ini sebagai default akan menonaktifkan status default pada template lain.</div>
        </div>

        @if($disableActive && $effectiveActiveChecked)
            <input type="hidden" name="is_active" value="1">
        @endif

        <div class="d-flex gap-2">
            <button class="btn btn-primary" type="submit">Save changes</button>
            <a class="btn btn-outline-secondary" href="{{ route('admin.templates.index') }}">Back</a>
        </div>
    </form>

    <script>
        // Enforce on the client: if Default checked, force Active checked & visually locked.
        // Server-side rules still apply.
        (function () {
            const elDefault = document.getElementById('is_default');
            const elActive = document.getElementById('is_active');
            if (!elDefault || !elActive) return;

            const lockedByUsed = {{ !empty($isUsed) && $template->is_active ? 'true' : 'false' }};

            function sync() {
                if (elDefault.checked) {
                    elActive.checked = true;
                    elActive.disabled = true;
                } else {
                    // If it's locked because used+active, keep disabled.
                    elActive.disabled = lockedByUsed;
                }
            }

            elDefault.addEventListener('change', sync);
            sync();
        })();
    </script>
</div>
@endsection
