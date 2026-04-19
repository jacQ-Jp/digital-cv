@extends('layouts.app')

@section('content')
@php
    $statusValue = old('status', $cv->status ?: 'draft');
    $renderUrl = route('cvs.render', $cv);
    $publicUrl = route('cvs.public', ['token' => $cv->public_uuid ?: $cv->id]);
@endphp

<style>
    .finalize-preview-frame {
        aspect-ratio: 210 / 297;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        overflow: hidden;
        background: #fff;
    }

    .finalize-preview-frame iframe {
        width: 100%;
        height: 100%;
        border: 0;
        display: block;
    }

    .finalize-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 8px;
        border-radius: 999px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #334155;
        font-size: .72rem;
        font-weight: 600;
    }

    .finalize-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: .5rem;
    }

    .copy-toast {
        position: fixed;
        right: 1rem;
        bottom: 1rem;
        background: #0f172a;
        color: #fff;
        border-radius: 10px;
        padding: 0.6rem 0.9rem;
        font-size: 0.85rem;
        font-weight: 600;
        opacity: 0;
        transform: translateY(12px);
        pointer-events: none;
        transition: opacity 0.2s ease, transform 0.2s ease;
        z-index: 1100;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.25);
    }

    .copy-toast.show {
        opacity: 1;
        transform: translateY(0);
    }
</style>

<div class="container py-2">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h4 mb-0">Step 5: Finalize CV</h1>
        <span class="badge text-bg-dark">Step 5/5</span>
    </div>

    <div class="row g-4 align-items-start">
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h6 mb-0">Skills</h2>
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('cvs.skills.create', $cv) }}">Add Skill</a>
                    </div>

                    @if($skills->isEmpty())
                        <div class="alert alert-info py-2 mb-0">Belum ada skill. Kamu bisa tambah dulu atau langsung finalize.</div>
                    @else
                        <div class="list-group">
                            @foreach($skills as $skill)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-semibold">{{ $skill->name }}</div>
                                        @if($skill->level)
                                            <div class="text-muted small">Level: {{ $skill->level }}</div>
                                        @endif
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('cvs.skills.edit', [$cv, $skill]) }}">Edit</a>
                                        <form method="POST" action="{{ route('cvs.skills.destroy', [$cv, $skill]) }}" onsubmit="return confirm('Delete this skill?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h6 mb-3">Final Status</h2>

                    <form method="POST" action="{{ route('cvs.update', $cv) }}" id="finalizeForm">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="title" value="{{ old('title', $cv->title) }}">
                        <textarea name="summary" class="d-none">{{ old('summary', $cv->summary) }}</textarea>
                        <input type="hidden" name="template_slug" value="{{ old('template_slug', $cv->template_slug) }}">

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select id="finalStatusInput" name="status" class="form-select" required>
                                <option value="draft" @selected($statusValue === 'draft')>Draft</option>
                                <option value="published" @selected($statusValue === 'published')>Public Link</option>
                            </select>
                            @error('status')<div class="text-danger small">{{ $message }}</div>@enderror
                            @error('title')<div class="text-danger small">{{ $message }}</div>@enderror
                            @error('template_slug')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>

                        <div class="small text-muted mb-3" id="finalStatusHint">
                            {{ $statusValue === 'published'
                                ? 'CV akan dipublikasikan dan link publik bisa langsung disalin dari halaman detail/list.'
                                : 'CV disimpan sebagai Draft dan belum bisa diakses publik.' }}
                        </div>

                        <div class="finalize-toolbar mb-3">
                            <button
                                type="button"
                                id="copyPublicBtn"
                                class="btn btn-outline-success"
                                data-public-url="{{ $publicUrl }}"
                            >
                                Public Link
                            </button>
                            <button
                                type="button"
                                id="savePdfBtn"
                                class="btn btn-outline-dark"
                                data-render-url="{{ $renderUrl }}"
                            >
                                Save PDF
                            </button>
                        </div>

                        <div class="small mb-3" id="publicLinkHint" style="color:#64748b;">
                            Public link bisa di-copy kapan saja, dan akan aktif untuk umum saat status disimpan ke Public Link.
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-primary" type="submit">Finish CV</button>
                            <a class="btn btn-outline-secondary" href="{{ route('cvs.show', $cv) }}">Lihat Detail CV</a>
                            <a class="btn btn-outline-dark" href="{{ route('cvs.educations.create', $cv) }}">Back to Education</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm position-sticky" style="top:1rem;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <h2 class="h6 mb-1">Final Review (Live)</h2>
                            <div class="text-muted small">Panel kanan menampilkan render template asli sesuai data yang sudah kamu isi.</div>
                        </div>
                        <span class="badge rounded-pill {{ $statusValue === 'published' ? 'text-bg-success' : 'text-bg-secondary' }}" id="finalStatusBadge">
                            {{ $statusValue === 'published' ? 'Public Link' : 'Draft' }}
                        </span>
                    </div>

                    <div class="finalize-preview-frame mb-3">
                        <iframe
                            id="finalReviewIframe"
                            src="{{ $renderUrl }}"
                            title="Final CV Review"
                            loading="lazy"
                            referrerpolicy="same-origin"
                        ></iframe>
                    </div>

                    <div class="small text-muted mb-2">Template: {{ $cv->template?->name ?? $cv->template_slug }}</div>
                    <div class="small text-muted mb-3">Judul CV: {{ $cv->title }}</div>

                    <div class="mb-2 text-uppercase small fw-semibold" style="letter-spacing:.08em; color:#64748b;">Quick Stats</div>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="finalize-chip">Experience: {{ $cv->experiences->count() }}</span>
                        <span class="finalize-chip">Education: {{ $cv->educations->count() }}</span>
                        <span class="finalize-chip">Skills: {{ $skills->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="copyToast" class="copy-toast" aria-live="polite">Link copied!</div>

<script>
(() => {
    const statusInput = document.getElementById('finalStatusInput');
    const statusBadge = document.getElementById('finalStatusBadge');
    const statusHint = document.getElementById('finalStatusHint');
    const publicHint = document.getElementById('publicLinkHint');
    const copyBtn = document.getElementById('copyPublicBtn');
    const copyToast = document.getElementById('copyToast');
    const pdfBtn = document.getElementById('savePdfBtn');
    const reviewIframe = document.getElementById('finalReviewIframe');
    let toastTimer = null;

    const showCopyToast = (message) => {
        if (!copyToast) return;

        copyToast.textContent = message;
        copyToast.classList.add('show');

        if (toastTimer) {
            clearTimeout(toastTimer);
        }

        toastTimer = setTimeout(() => {
            copyToast.classList.remove('show');
        }, 1500);
    };

    const copyText = async (text) => {
        if (!text) return false;

        if (navigator.clipboard && window.isSecureContext) {
            try {
                await navigator.clipboard.writeText(text);
                return true;
            } catch {
                // Continue to fallback
            }
        }

        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.setAttribute('readonly', '');
        textarea.style.position = 'fixed';
        textarea.style.top = '-9999px';
        document.body.appendChild(textarea);
        textarea.focus();
        textarea.select();

        let copied = false;
        try {
            copied = document.execCommand('copy');
        } catch {
            copied = false;
        }

        document.body.removeChild(textarea);
        return copied;
    };

    const updateStatus = () => {
        const isPublic = statusInput?.value === 'published';

        statusBadge.className = 'badge rounded-pill ' + (isPublic ? 'text-bg-success' : 'text-bg-secondary');
        statusBadge.textContent = isPublic ? 'Public Link' : 'Draft';

        statusHint.textContent = isPublic
            ? 'CV akan dipublikasikan dan link publik bisa langsung disalin dari halaman detail/list.'
            : 'CV disimpan sebagai Draft dan belum bisa diakses publik.';

        if (publicHint) {
            publicHint.textContent = isPublic
                ? 'Status saat ini: Public Link. Setelah klik Finish CV, link publik langsung aktif.'
                : 'Status saat ini: Draft. Link publik tetap bisa di-copy, tapi akses umum aktif setelah status diubah ke Public Link dan disimpan.';
        }
    };

    copyBtn?.addEventListener('click', async () => {
        const url = copyBtn.getAttribute('data-public-url');
        if (!url) return;

        const copied = await copyText(url);
        if (copied) {
            const prev = copyBtn.textContent;
            copyBtn.textContent = 'Copied!';
            showCopyToast('Link copied!');
            setTimeout(() => {
                copyBtn.textContent = prev;
            }, 1200);
        } else {
            showCopyToast('Gagal copy link');
            window.prompt('Copy this link:', url);
        }
    });

    pdfBtn?.addEventListener('click', () => {
        const renderUrl = pdfBtn.getAttribute('data-render-url');
        if (!renderUrl) return;

        const printWindow = window.open(renderUrl, '_blank');
        if (!printWindow) return;

        const started = Date.now();
        const timer = setInterval(() => {
            if (Date.now() - started > 12000) {
                clearInterval(timer);
                return;
            }

            try {
                if (printWindow.document && printWindow.document.readyState === 'complete') {
                    clearInterval(timer);
                    printWindow.focus();
                    printWindow.print();
                }
            } catch {
                // keep polling while cross-window is initializing
            }
        }, 350);
    });

    // Keep preview in sync with latest saved CV data when user returns to tab.
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden && reviewIframe?.contentWindow) {
            reviewIframe.contentWindow.location.reload();
        }
    });

    statusInput?.addEventListener('change', updateStatus);
    updateStatus();
})();
</script>
@endsection
