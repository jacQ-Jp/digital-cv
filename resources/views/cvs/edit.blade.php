@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="h3 mb-3">Edit CV</h1>

    <form method="POST" action="{{ route('cvs.update', $cv) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Title</label>
            <input name="title" class="form-control" value="{{ old('title', $cv->title) }}" required />
            @error('title')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Summary</label>
            <textarea name="summary" class="form-control" rows="4">{{ old('summary', $cv->summary) }}</textarea>
            @error('summary')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Template</label>
            <select name="template_slug" class="form-select" required>
                @foreach($templates as $tpl)
                    <option value="{{ $tpl->slug }}" @selected(old('template_slug', $cv->template_slug)===$tpl->slug)>
                        {{ $tpl->name }}
                    </option>
                @endforeach
            </select>
            @error('template_slug')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select" required>
                <option value="draft" @selected(old('status', $cv->status)==='draft')>Draft</option>
                <option value="published" @selected(old('status', $cv->status)==='published')>Public Link</option>
            </select>
            @error('status')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-primary" type="submit">Update</button>
            <a class="btn btn-outline-secondary" href="{{ route('cvs.show', $cv) }}">Cancel</a>
        </div>
    </form>
</div>
@endsection
