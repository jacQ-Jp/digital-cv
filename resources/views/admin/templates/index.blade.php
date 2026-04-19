@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Templates</h1>
        <div class="d-flex align-items-center gap-2">
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="btn btn-outline-secondary">Logout</button>
            </form>
            <a class="btn btn-primary" href="{{ route('admin.templates.create') }}">Add Template</a>
        </div>
    </div>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="table-responsive templates-table-wrap" id="templatesTableWrap">
        <table class="table table-striped align-middle templates-table">
            <thead>
                <tr>
                    <th>Thumbnail</th>
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
                        $thumbnailUrl = $tpl->thumbnailPreviewUrl();

                        $canDelete = ! $isDefault && ! $isUsed;
                        $canToggleActive = ! $isDefault && ! ($isActive && $isUsed);
                    @endphp
                    <tr>
                        <td style="width:140px;">
                            @if($thumbnailUrl)
                                <img src="{{ $thumbnailUrl }}" alt="Thumbnail {{ $tpl->name }}" style="width:120px;height:auto;max-height:90px;object-fit:cover;border:1px solid #e2e8f0;border-radius:8px;background:#fff;">
                            @else
                                <span class="small text-muted">No thumbnail</span>
                            @endif
                        </td>
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
                            <!-- Wrapper untuk tombol agar rapi berdampingan -->
                            <div class="d-flex justify-content-end align-items-center gap-1 flex-wrap">
                                
                                <!-- Button Edit -->
                                <a class="btn btn-sm btn-primary" href="{{ route('admin.templates.edit', $tpl) }}">Edit</a>

                                <!-- Toggle Button (Custom CSS) -->
                                <form class="d-inline m-0 p-0" method="POST" action="{{ route('admin.templates.toggle-active', $tpl) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn-custom-toggle {{ $isActive ? 'active' : 'inactive' }}" type="submit" @disabled(!$canToggleActive)
                                        title="{{ !$canToggleActive ? 'Tidak bisa diubah karena dipakai / Default' : 'Toggle Status' }}">
                                        <span class="toggle-track">
                                            <span class="toggle-circle"></span>
                                        </span>
                                    </button>
                                </form>

                                <!-- Button Delete -->
                                <form class="d-inline m-0 p-0" method="POST" action="{{ route('admin.templates.destroy', $tpl) }}" onsubmit="return confirm('Hapus template ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit" @disabled(!$canDelete)
                                        title="{{ !$canDelete ? 'Default / Tidak bisa dihapus' : 'Hapus' }}">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- CUSTOM CSS FOR TOGGLE BUTTON -->
<style>
    .templates-table {
        --bs-table-bg: transparent;
        --bs-table-color: #e2e8f0;
        border-color: rgba(148, 163, 184, 0.28);
    }

    .templates-table thead th {
        background: #1e293b;
        color: #f8fafc;
        border-bottom: 1px solid #334155;
        font-weight: 700;
    }

    .templates-table tbody td {
        color: #e2e8f0;
        border-color: rgba(148, 163, 184, 0.2);
    }

    .templates-table.table-striped > tbody > tr:nth-of-type(odd) > * {
        --bs-table-accent-bg: rgba(148, 163, 184, 0.08);
        color: #e2e8f0;
    }

    .templates-table code {
        color: #c4b5fd;
    }

    /* Container Tombol Toggle */
    .btn-custom-toggle {
        position: relative;
        display: inline-block;
        width: 52px;  /* Lebar track */
        height: 28px; /* Tinggi track */
        background-color: #dee2e6; /* Abu-abu terang (OFF) */
        border: none;
        border-radius: 999px; /* Bentuk pill */
        cursor: pointer;
        margin: 0;
        padding: 0;
        outline: none;
        transition: background-color 0.3s ease;
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
    }

    /* Lingkaran Bulat */
    .toggle-circle {
        content: "";
        position: absolute;
        top: 2px;
        left: 2px;
        width: 24px;
        height: 24px;
        background-color: #fff;
        border-radius: 50%;
        box-shadow: 0 1px 3px rgba(0,0,0,0.3);
        transition: transform 0.3s cubic-bezier(0.4, 0.0, 0.2, 1);
    }

    /* --- STATE: ACTIVE (ON) --- */
    .btn-custom-toggle.active {
        background-color: #198754; /* Hijau Bootstrap Success */
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.2);
    }

    .btn-custom-toggle.active .toggle-circle {
        transform: translateX(24px); /* Geser ke kanan */
    }

    /* --- STATE: INACTIVE (OFF) --- */
    .btn-custom-toggle.inactive {
        background-color: #dee2e6; /* Abu-abu */
    }

    .btn-custom-toggle.inactive .toggle-circle {
        transform: translateX(0); /* Tetap di kiri */
    }

    /* Hover Effect */
    .btn-custom-toggle:not(:disabled):hover {
        filter: brightness(0.95);
    }
    
    /* Disabled State */
    .btn-custom-toggle:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        filter: grayscale(1);
    }
</style>
@endsection