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
                    <th>Used</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($templates as $tpl)
                    @php
                        $usedCount = (int) ($tpl->cvs_count ?? 0);
                        $isUsed = $usedCount > 0;
                        $isDefault = (bool) $tpl->is_default;
                        $isActive = (bool) $tpl->is_active;

                        $canDelete = ! $isDefault && ! $isUsed;
                        $canToggleActive = ! $isDefault && ! ($isActive && $isUsed);
                    @endphp
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $tpl->name }}</div>
                            <div class="small text-muted">{{ $tpl->description }}</div>
                        </td>
                        <td><code>{{ $tpl->slug }}</code></td>
                        <td>
                            <span class="fw-semibold">{{ $usedCount }}</span>
                            @if($isUsed)
                                <span class="badge text-bg-warning">Dipakai</span>
                            @else
                                <span class="badge text-bg-secondary">Belum dipakai</span>
                            @endif
                        </td>
                        <td>
                            @if($isActive)
                                <span class="badge text-bg-success">Active</span>
                            @else
                                <span class="badge text-bg-secondary">Inactive</span>
                            @endif

                            @if($isDefault)
                                <span class="badge text-bg-primary">Default</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.templates.edit', $tpl) }}">Edit</a>

                            <form class="d-inline" method="POST" action="{{ route('admin.templates.toggle-active', $tpl) }}">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-sm btn-outline-secondary" type="submit" @disabled(!$canToggleActive)
                                    title="{{ !$canToggleActive ? 'Tidak bisa dinonaktifkan jika sudah dipakai / default tidak bisa dinonaktifkan' : '' }}">
                                    {{ $isActive ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>

                            <form class="d-inline" method="POST" action="{{ route('admin.templates.destroy', $tpl) }}" onsubmit="return confirm('Delete this template?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit" @disabled(!$canDelete)
                                    title="{{ !$canDelete ? ($isDefault ? 'Default template tidak bisa dihapus' : 'Template sudah dipakai, tidak bisa dihapus') : '' }}">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
