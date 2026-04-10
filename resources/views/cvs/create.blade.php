@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="h3 mb-3">Create CV</h1>

    <div class="alert alert-info">
        Template dipilih di awal. Kamu masih bisa mengganti template di sini jika diperlukan.
        <a href="{{ route('cv-builder.templates') }}" class="alert-link">Ganti template</a>.
    </div>

    <form method="POST" action="{{ route('cvs.store') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Title</label>
            <input name="title" class="form-control" value="{{ old('title') }}" required />
            @error('title')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Summary</label>
            <textarea name="summary" class="form-control" rows="4">{{ old('summary') }}</textarea>
            @error('summary')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Template</label>
            <select name="template_slug" class="form-select">
                @foreach($templates as $tpl)
                    @php($current = old('template_slug', $selectedTemplateSlug ?? null))
                    <option value="{{ $tpl->slug }}" @selected($current === $tpl->slug)>
                        {{ $tpl->name }}@if($tpl->is_default) (default)@endif
                    </option>
                @endforeach
            </select>
            @error('template_slug')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select" required>
                <option value="draft" @selected(old('status','draft')==='draft')>draft</option>
                <option value="published" @selected(old('status')==='published')>published</option>
            </select>
            @error('status')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-primary" type="submit">Save</button>
            <a class="btn btn-outline-secondary" href="{{ route('cvs.index') }}">Cancel</a>
        </div>
    </form>
</div>
@endsection
