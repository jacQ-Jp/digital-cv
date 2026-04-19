@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h1 class="h3 mb-1">{{ $cv->title }}</h1>
            <div class="text-muted small">Status: {{ $cv->status }} | Template: {{ $cv->template?->name ?? $cv->template_slug }}</div>
            <div class="mt-2 d-flex flex-wrap gap-2">
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('cvs.render', $cv) }}" target="_blank" rel="noopener">Preview Render</a>
                <a
                    href="{{ route('cvs.wizard.pdf', $cv) }}"
                    class="btn btn-sm btn-outline-dark"
                    target="_blank"
                    rel="noopener"
                >
                    Save PDF
                </a>
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
                            <span class="badge text-bg-secondary">{{ $skill->name }}</span>
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
            toast.style.top = window.innerWidth <= 768 ? '76px' : '86px';
            toast.style.left = 'auto';
            toast.style.bottom = 'auto';
            toast.style.display = 'inline-flex';
            toast.style.alignItems = 'center';
            toast.style.justifyContent = 'center';
            toast.style.width = 'fit-content';
            toast.style.maxWidth = 'min(90vw, 360px)';
            toast.style.minHeight = '46px';
            toast.style.background = 'rgba(2, 6, 23, 0.92)';
            toast.style.border = '1px solid rgba(139, 92, 246, 0.72)';
            toast.style.color = '#f8fafc';
            toast.style.padding = '0.72rem 1rem';
            toast.style.borderRadius = '12px';
            toast.style.fontSize = '0.92rem';
            toast.style.fontWeight = '700';
            toast.style.letterSpacing = '0.01em';
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-12px) scale(0.98)';
            toast.style.transition = 'opacity 0.22s ease, transform 0.22s ease';
            toast.style.pointerEvents = 'none';
            toast.style.zIndex = '2500';
            toast.style.boxShadow = '0 14px 28px rgba(15, 23, 42, 0.44), 0 0 0 1px rgba(139, 92, 246, 0.2)';
            toast.style.backdropFilter = 'blur(10px)';
            document.body.appendChild(toast);
        }

        toast.textContent = message;
        toast.style.opacity = '1';
        toast.style.transform = 'translateY(0) scale(1)';

        if (toast._hideTimer) {
            clearTimeout(toast._hideTimer);
        }

        toast._hideTimer = setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-12px) scale(0.98)';
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
                showToast('link copied!');
                setTimeout(() => {
                    copyBtn.textContent = prev;
                }, 1200);
            } else {
                window.prompt('Copy this link:', url);
            }
        });
    }

})();
</script>
@endsection
