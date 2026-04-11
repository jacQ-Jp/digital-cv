@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="h4">{{ $skill->name }}</h1>
    <div class="text-muted">{{ $skill->level }}</div>
</div>
@endsection
