@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="h3 mb-3">Step 1 — Select Template</h1>

    @if($templates->isEmpty())
        <div class="alert alert-warning">No active templates available.</div>
    @else
        <form method="POST" action="{{ route('cv-builder.templates.save') }}">
            @csrf

            <div class="row g-3">
                @foreach($templates as $tpl)
                    <div class="col-md-4">
                        <label class="card h-100" style="cursor:pointer;">
                            <div class="card-body">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="template_slug" value="{{ $tpl->slug }}" @checked(old('template_slug', $templates->firstWhere('is_default', true)?->slug) === $tpl->slug)>
                                    <span class="form-check-label fw-semibold">{{ $tpl->name }}</span>
                                    @if($tpl->is_default)
                                        <span class="badge text-bg-primary ms-2">default</span>
                                    @endif
                                </div>
                                @if($tpl->description)
                                    <p class="text-muted small mt-2 mb-0">{{ $tpl->description }}</p>
                                @endif
                            </div>
                        </label>
                    </div>
                @endforeach
            </div>

            @error('template_slug')
                <div class="text-danger small mt-2">{{ $message }}</div>
            @enderror

            <div class="mt-3 d-flex gap-2">
                <button class="btn btn-primary" type="submit">Continue</button>
                <a class="btn btn-outline-secondary" href="{{ route('cvs.index') }}">Cancel</a>
            </div>
        </form>
    @endif
</div>
@endsection
