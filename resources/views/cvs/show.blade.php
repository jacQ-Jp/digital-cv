@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h1 class="h3 mb-1">{{ $cv->title }}</h1>
            <div class="text-muted small">Status: {{ $cv->status }} | Template: {{ $cv->template?->name ?? $cv->template_slug }}</div>
            <div class="mt-2 d-flex flex-wrap gap-2">
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('cvs.render', $cv) }}" target="_blank" rel="noopener">Preview Render</a>
                <button
                    type="button"
                    id="savePdfBtn"
                    class="btn btn-sm btn-outline-dark"
                    data-render-url="{{ route('cvs.render', $cv) }}"
                >
                    Save PDF
                </button>
                @if($cv->status === 'published')
                    <button
                        type="button"
                        id="copyPublicBtn"
                        class="btn btn-sm btn-outline-success"
                        data-public-url="{{ route('cvs.public', ['token' => $cv->public_uuid ?: $cv->id]) }}"
                    >
                        Copy Public Link
                    </button>
                @endif
            </div>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-primary" href="{{ route('cvs.edit', $cv) }}">Edit</a>
            <form method="POST" action="{{ route('cvs.destroy', $cv) }}" onsubmit="return confirm('Delete this CV?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">Delete</button>
            </form>
        </div>
    </div>

    @if($cv->summary)
        <div class="card mb-3">
            <div class="card-body">
                <h2 class="h6">Summary</h2>
                <p class="mb-0">{{ $cv->summary }}</p>
            </div>
        </div>
    @endif

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h2 class="h6 mb-0">Experiences</h2>
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('cvs.experiences.index', $cv) }}">Manage</a>
                    </div>
                    @forelse($cv->experiences as $exp)
                        <div class="border-bottom pb-2 mb-2">
                            <div class="fw-semibold">{{ $exp->position }} - {{ $exp->company }}</div>
                            <div class="text-muted small">{{ $exp->start_date }} - {{ $exp->end_date ?? 'Present' }}</div>
                            @if($exp->description)
                                <div class="small">{{ $exp->description }}</div>
                            @endif
                        </div>
                    @empty
                        <div class="text-muted">No experiences yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h2 class="h6 mb-0">Educations</h2>
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('cvs.educations.index', $cv) }}">Manage</a>
                    </div>
                    @forelse($cv->educations as $edu)
                        <div class="border-bottom pb-2 mb-2">
                            <div class="fw-semibold">{{ $edu->school }}</div>
                            <div class="text-muted small">{{ $edu->degree }} ({{ $edu->year }})</div>
                        </div>
                    @empty
                        <div class="text-muted">No educations yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-0">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h2 class="h6 mb-0">Skills</h2>
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('cvs.skills.index', $cv) }}">Manage</a>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        @forelse($cv->skills as $skill)
                            <span class="badge text-bg-secondary">{{ $skill->name }}@if($skill->level) ({{ $skill->level }})@endif</span>
                        @empty
                            <span class="text-muted">No skills yet.</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <a class="btn btn-outline-secondary" href="{{ route('cvs.index') }}">Back</a>
    </div>
</div>

<script>
(() => {
    const toastId = 'copyLinkToast';

    function showToast(message) {
        let toast = document.getElementById(toastId);
        if (!toast) {
            toast = document.createElement('div');
            toast.id = toastId;
            toast.style.position = 'fixed';
            toast.style.right = '16px';
            toast.style.bottom = '16px';
            toast.style.background = '#0f172a';
            toast.style.color = '#fff';
            toast.style.padding = '10px 14px';
            toast.style.borderRadius = '10px';
            toast.style.fontSize = '14px';
            toast.style.fontWeight = '600';
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(10px)';
            toast.style.transition = 'opacity 0.2s ease, transform 0.2s ease';
            toast.style.pointerEvents = 'none';
            toast.style.zIndex = '2000';
            document.body.appendChild(toast);
        }

        toast.textContent = message;
        toast.style.opacity = '1';
        toast.style.transform = 'translateY(0)';

        if (toast._hideTimer) {
            clearTimeout(toast._hideTimer);
        }

        toast._hideTimer = setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(10px)';
        }, 1500);
    }

    async function copyText(text) {
        if (!text) return false;

        if (navigator.clipboard && window.isSecureContext) {
            try {
                await navigator.clipboard.writeText(text);
                return true;
            } catch (error) {
                // Continue to fallback.
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
        } catch (error) {
            copied = false;
        }

        document.body.removeChild(textarea);
        return copied;
    }

    const copyBtn = document.getElementById('copyPublicBtn');
    if (copyBtn) {
        copyBtn.addEventListener('click', async () => {
            const url = copyBtn.getAttribute('data-public-url');
            if (!url) return;

            const copied = await copyText(url);
            if (copied) {
                const prev = copyBtn.textContent;
                copyBtn.textContent = 'Copied!';
                showToast('Link copied!');
                setTimeout(() => {
                    copyBtn.textContent = prev;
                }, 1200);
            } else {
                window.prompt('Copy this link:', url);
            }
        });
    }

    const pdfBtn = document.getElementById('savePdfBtn');
    if (pdfBtn) {
        pdfBtn.addEventListener('click', () => {
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
                    // keep polling while cross-window is still initializing
                }
            }, 350);
        });
    }
})();
</script>
@endsection
