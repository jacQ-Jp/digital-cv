@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Templates</h1>
        <div class="d-flex align-items-center gap-2">
            <form id="bulkDeleteTemplatesForm" method="POST" action="{{ route('admin.templates.bulk-destroy') }}" class="m-0 d-none">
                @csrf
                @method('DELETE')
            </form>
            <div class="template-action-wrap" id="templateActionWrap">
                <button type="button" id="templateActionMenuToggle" class="btn btn-outline-light btn-menu-dots" aria-haspopup="true" aria-expanded="false" title="Pilihan Aksi">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <circle cx="12" cy="5" r="1.8"></circle>
                        <circle cx="12" cy="12" r="1.8"></circle>
                        <circle cx="12" cy="19" r="1.8"></circle>
                    </svg>
                </button>

                <div id="templateActionMenu" class="template-action-menu" role="menu">
                    <button type="button" id="toggleTemplateSelectionBtn" class="template-action-item" role="menuitem">Pilih</button>
                    <button type="button" id="bulkDeleteTemplatesBtn" class="template-action-item template-action-item-danger" role="menuitem" disabled>Hapus Terpilih</button>

                    <form id="destroyAllTemplatesForm" method="POST" action="{{ route('admin.templates.destroy-all') }}" class="m-0" onsubmit="return confirm('Hapus semua template yang bisa dihapus?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="template-action-item template-action-item-danger" role="menuitem">Hapus Semua Template</button>
                    </form>
                </div>
            </div>

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
                    <th class="template-select-col text-center">
                        <input
                            type="checkbox"
                            id="selectAllTemplatesCheckbox"
                            class="form-check-input"
                            title="Pilih semua template yang bisa dihapus"
                        >
                    </th>
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
                        // Catatan: Kita hapus logika $canSetDefault untuk UI agar tombol Set Default muncul jika belum default
                        $canSetDefault = ! $isDefault;
                    @endphp
                    <tr>
                        <td class="template-select-col text-center">
                            <input
                                type="checkbox"
                                class="form-check-input js-template-select"
                                name="template_ids[]"
                                value="{{ $tpl->id }}"
                                form="bulkDeleteTemplatesForm"
                                @disabled(!$canDelete)
                                title="{{ !$canDelete ? 'Template default / sudah dipakai tidak bisa dipilih' : 'Pilih template' }}"
                            >
                        </td>
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

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const tableWrap = document.getElementById('templatesTableWrap');
        const actionWrap = document.getElementById('templateActionWrap');
        const menuToggleBtn = document.getElementById('templateActionMenuToggle');
        const actionMenu = document.getElementById('templateActionMenu');
        const toggleBtn = document.getElementById('toggleTemplateSelectionBtn');
        const bulkForm = document.getElementById('bulkDeleteTemplatesForm');
        const bulkDeleteBtn = document.getElementById('bulkDeleteTemplatesBtn');
        const selectAllCheckbox = document.getElementById('selectAllTemplatesCheckbox');
        const checkboxes = Array.from(document.querySelectorAll('.js-template-select'));

        if (!tableWrap || !actionWrap || !menuToggleBtn || !actionMenu || !toggleBtn || !bulkForm || !bulkDeleteBtn || !selectAllCheckbox || checkboxes.length === 0) {
            return;
        }

        const selectableCheckboxes = checkboxes.filter((checkbox) => !checkbox.disabled);

        function setMenuOpen(open) {
            actionWrap.classList.toggle('is-open', open);
            menuToggleBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
        }

        function updateBulkDeleteState() {
            const selectedCount = selectableCheckboxes.filter((checkbox) => checkbox.checked).length;
            bulkDeleteBtn.disabled = selectedCount === 0;
            bulkDeleteBtn.textContent = selectedCount > 0
                ? `Hapus Terpilih (${selectedCount})`
                : 'Hapus Terpilih';

            if (selectableCheckboxes.length === 0) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.disabled = true;
                return;
            }

            selectAllCheckbox.disabled = false;
            selectAllCheckbox.checked = selectedCount === selectableCheckboxes.length;
            selectAllCheckbox.indeterminate = selectedCount > 0 && selectedCount < selectableCheckboxes.length;
        }

        function setSelectionMode(enabled) {
            tableWrap.classList.toggle('selection-mode', enabled);
            toggleBtn.textContent = enabled ? 'Batal Pilih' : 'Pilih';

            if (!enabled) {
                checkboxes.forEach((checkbox) => {
                    checkbox.checked = false;
                });
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            }

            updateBulkDeleteState();
        }

        toggleBtn.addEventListener('click', () => {
            const isEnabled = tableWrap.classList.contains('selection-mode');
            setSelectionMode(!isEnabled);
        });

        bulkDeleteBtn.addEventListener('click', () => {
            if (bulkDeleteBtn.disabled) return;
            if (!confirm('Hapus semua template yang dipilih?')) return;
            bulkForm.requestSubmit();
        });

        menuToggleBtn.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            const willOpen = !actionWrap.classList.contains('is-open');
            setMenuOpen(willOpen);
        });

        document.addEventListener('click', (event) => {
            if (!event.target.closest('#templateActionWrap')) {
                setMenuOpen(false);
            }
        });

        checkboxes.forEach((checkbox) => {
            checkbox.addEventListener('change', updateBulkDeleteState);
        });

        selectAllCheckbox.addEventListener('change', () => {
            const shouldCheck = selectAllCheckbox.checked;
            selectableCheckboxes.forEach((checkbox) => {
                checkbox.checked = shouldCheck;
            });
            updateBulkDeleteState();
        });

        setSelectionMode(false);
        setMenuOpen(false);
    });
</script>

<!-- CUSTOM CSS FOR TOGGLE BUTTON -->
<style>
    .template-action-wrap {
        position: relative;
    }

    .btn-menu-dots {
        width: 40px;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }

    .template-action-menu {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        min-width: 220px;
        background: #0f172a;
        border: 1px solid rgba(148, 163, 184, 0.32);
        border-radius: 10px;
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.38);
        padding: 0.35rem;
        display: none;
        z-index: 30;
    }

    .template-action-wrap.is-open .template-action-menu {
        display: block;
    }

    .template-action-item {
        width: 100%;
        border: none;
        border-radius: 8px;
        background: transparent;
        color: #e2e8f0;
        font-size: 0.88rem;
        font-weight: 600;
        text-align: left;
        padding: 0.55rem 0.65rem;
        cursor: pointer;
    }

    .template-action-item:hover {
        background: rgba(148, 163, 184, 0.18);
    }

    .template-action-item.template-action-item-danger {
        color: #fda4af;
    }

    .template-action-item.template-action-item-danger:hover {
        background: rgba(248, 113, 113, 0.18);
        color: #fee2e2;
    }

    .template-action-item:disabled,
    .template-action-item:disabled:hover {
        opacity: 0.45;
        cursor: not-allowed;
        background: transparent;
    }

    .templates-table-wrap .template-select-col {
        display: none;
        width: 64px;
        vertical-align: middle;
    }

    .templates-table-wrap.selection-mode .template-select-col {
        display: table-cell;
    }

    .templates-table-wrap.selection-mode .js-template-select:disabled {
        opacity: 0.45;
        cursor: not-allowed;
    }

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