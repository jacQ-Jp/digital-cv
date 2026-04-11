{{-- Basic CV list --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">My CVs</h1>
        <a class="btn btn-primary" href="{{ route('cv-builder.templates') }}">Pilih Template</a>
    </div>

    @if($cvs->isEmpty())
        <div class="alert alert-info">No CV yet.</div>
    @else
        <div class="list-group">
            @foreach($cvs as $cv)
                <a class="list-group-item list-group-item-action" href="{{ route('cvs.show', $cv) }}">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1">{{ $cv->title }}</h5>
                        <small class="text-muted">{{ $cv->status }}</small>
                    </div>
                    @if($cv->summary)
                        <p class="mb-1">{{ \Illuminate\Support\Str::limit($cv->summary, 120) }}</p>
                    @endif
                    <small class="text-muted">Updated {{ $cv->updated_at?->diffForHumans() }}</small>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
