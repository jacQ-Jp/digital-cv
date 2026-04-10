@extends('layouts.app')

@section('content')
@php
    $name = $cv->user->name;
    $email = $cv->user->email;
@endphp
<div class="container py-5" style="max-width: 900px">
    <div class="mb-4">
        <h1 class="display-6 mb-1">{{ $name }}</h1>
        <div class="text-muted">{{ $email }}</div>
        @if($cv->summary)
            <div class="mt-3 border-start ps-3 text-muted">{{ $cv->summary }}</div>
        @endif
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <h2 class="h6 text-uppercase text-muted">Experience</h2>
            <div class="mt-2">
                @forelse($cv->experiences as $exp)
                    <div class="py-2 border-bottom">
                        <div class="fw-semibold">{{ $exp->position }}</div>
                        <div class="small text-muted">{{ $exp->company }} · {{ $exp->start_date }} — {{ $exp->end_date ?? 'Present' }}</div>
                        @if($exp->description)
                            <div class="small mt-1">{{ $exp->description }}</div>
                        @endif
                    </div>
                @empty
                    <div class="text-muted">No experience.</div>
                @endforelse
            </div>

            <h2 class="h6 text-uppercase text-muted mt-4">Education</h2>
            <div class="mt-2">
                @forelse($cv->educations as $edu)
                    <div class="py-2 border-bottom">
                        <div class="fw-semibold">{{ $edu->school }}</div>
                        <div class="small text-muted">{{ $edu->degree }} · {{ $edu->year }}</div>
                    </div>
                @empty
                    <div class="text-muted">No education.</div>
                @endforelse
            </div>
        </div>

        <div class="col-md-4">
            <h2 class="h6 text-uppercase text-muted">Skills</h2>
            <div class="d-flex flex-wrap gap-2 mt-2">
                @forelse($cv->skills as $skill)
                    <span class="px-2 py-1 border rounded small">{{ $skill->name }}@if($skill->level) <span class="text-muted">({{ $skill->level }})</span>@endif</span>
                @empty
                    <span class="text-muted">No skills.</span>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
