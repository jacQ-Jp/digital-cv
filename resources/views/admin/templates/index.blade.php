@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Templates</h1>
        <a class="btn btn-primary" href="{{ route('admin.templates.create') }}">Add Template</a>
    </div>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Active</th>
                    <th>Default</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($templates as $tpl)
                    <tr>
                        <td>{{ $tpl->name }}</td>
                        <td><code>{{ $tpl->slug }}</code></td>
                        <td>{{ $tpl->is_active ? 'yes' : 'no' }}</td>
                        <td>{{ $tpl->is_default ? 'yes' : 'no' }}</td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.templates.edit', $tpl) }}">Edit</a>
                            <form class="d-inline" method="POST" action="{{ route('admin.templates.destroy', $tpl) }}" onsubmit="return confirm('Delete this template?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
