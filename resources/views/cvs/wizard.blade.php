@extends('layouts.app')

@section('content')
<style>
    :root {
        --wiz-surface: rgba(11, 15, 26, 0.72);
        --wiz-border: rgba(148, 163, 184, 0.26);
        --wiz-text: #e6eefc;
        --wiz-muted: #9fb0cb;
        --wiz-accent: #19b3c6;
        --wiz-accent-soft: rgba(25, 179, 198, 0.14);
        --wiz-warm: #ff8a65;
        --wiz-scroll-height: clamp(420px, 60vh, 620px);
    }

    .wizard-shell .card {
        background: var(--wiz-surface);
        border: 1px solid var(--wiz-border);
        border-radius: 18px;
        box-shadow: 0 24px 30px -28px rgba(30, 48, 75, 0.62);
        transition: border-color 0.22s ease, box-shadow 0.22s ease;
        animation: wizFadeUp 0.42s ease both;
    }

    .wizard-shell .wizard-header h1 {
        font-size: 1.22rem;
        font-weight: 800;
        letter-spacing: -0.01em;
        color: var(--wiz-text);
        text-transform: uppercase;
    }

    .wizard-shell .wizard-header p {
        color: var(--wiz-muted) !important;
    }

    .wizard-shell .wizard-steps {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 10px;
        margin-bottom: 1.2rem;
    }

    .wizard-shell .wizard-step {
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(148, 163, 184, 0.3);
        border-radius: 12px;
        padding: 10px;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        gap: 8px;
        min-width: 0;
        transition: all 0.22s ease;
    }

    .wizard-shell .wizard-step:hover {
        background: rgba(25, 179, 198, 0.12);
        border-color: rgba(25, 179, 198, 0.45);
    }

    .wizard-shell .wizard-step-index {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: rgba(148, 163, 184, 0.2);
        color: #d2def2;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.88rem;
        transition: all 0.22s ease;
        flex: 0 0 auto;
    }

    .wizard-shell .wizard-step-label {
        display: block;
        min-width: 0;
        font-size: 0.74rem;
        font-weight: 700;
        color: #b7c6de;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        white-space: normal;
        overflow-wrap: anywhere;
        word-break: break-word;
    }

    .wizard-shell .wizard-step.is-active {
        background: rgba(25, 179, 198, 0.16);
        border-color: rgba(25, 179, 198, 0.65);
        box-shadow: 0 0 0 0.12rem rgba(25, 179, 198, 0.15);
    }

    .wizard-shell .wizard-step.is-active .wizard-step-index {
        background: var(--wiz-accent);
        color: #fff;
    }

    .wizard-shell .wizard-step.is-active .wizard-step-label {
        color: #7de5f3;
    }

    .wizard-shell .wizard-step.is-done {
        border-color: rgba(16, 169, 122, 0.52);
        background: rgba(16, 169, 122, 0.14);
    }

    .wizard-shell .wizard-step.is-done .wizard-step-index {
        background: #10a97a;
        color: #fff;
    }

    .wizard-shell .wizard-body {
        padding: 0;
    }

    .wizard-shell .form-label,
    .wizard-shell label {
        display: block;
        color: #b0c0d8;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.5rem;
        margin-top: 1.1rem;
    }

    .wizard-shell .form-label:first-child {
        margin-top: 0;
    }

    .wizard-shell .form-control,
    .wizard-shell input,
    .wizard-shell textarea,
    .wizard-shell select {
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(148, 163, 184, 0.34);
        color: var(--wiz-text);
        border-radius: 10px;
        padding: 11px 14px;
        font-size: 0.94rem;
        transition: all 0.2s ease;
        width: 100%;
    }

    .wizard-shell .form-control:focus,
    .wizard-shell input:focus,
    .wizard-shell textarea:focus,
    .wizard-shell select:focus {
        border-color: rgba(25, 179, 198, 0.66);
        box-shadow: 0 0 0 3px rgba(25, 179, 198, 0.16);
        outline: none;
    }

    .wizard-shell input::placeholder,
    .wizard-shell textarea::placeholder {
        color: #91a5c5;
    }

    .wizard-shell .btn {
        border-radius: 999px;
        padding: 10px 18px;
        font-weight: 700;
        letter-spacing: 0.03em;
        transition: transform 0.22s ease, box-shadow 0.22s ease, filter 0.22s ease;
        font-size: 0.87rem;
    }

    .wizard-shell .btn-outline-secondary {
        border-color: rgba(148, 163, 184, 0.45);
        color: #d2ddf0;
        background: rgba(255, 255, 255, 0.04);
    }

    .wizard-shell .btn-outline-secondary:hover {
        border-color: rgba(25, 179, 198, 0.52);
        color: #f4fdff;
        background: rgba(25, 179, 198, 0.12);
    }

    .wizard-shell .btn-outline-dark {
        border-color: rgba(148, 163, 184, 0.5);
        color: #dbe7ff;
        background: rgba(255, 255, 255, 0.03);
    }

    .wizard-shell .btn-outline-dark:hover:not(:disabled) {
        border-color: rgba(25, 179, 198, 0.56);
        color: #f4fdff;
        background: rgba(25, 179, 198, 0.12);
    }

    .wizard-shell .btn-outline-dark:disabled {
        color: rgba(203, 213, 225, 0.88);
        border-color: rgba(148, 163, 184, 0.36);
        background: rgba(15, 23, 42, 0.48);
        opacity: 0.85;
    }

    .wizard-shell .btn-primary {
        background: linear-gradient(95deg, var(--wiz-accent), #11a1b5);
        border: 1px solid rgba(12, 156, 178, 0.66);
        color: #fff;
        box-shadow: 0 18px 24px -22px rgba(12, 156, 178, 0.76);
    }

    .wizard-shell .btn-primary:hover:not(:disabled) {
        transform: translateY(-1px);
        filter: brightness(1.05);
    }

    .wizard-shell .wizard-preview-panel {
        border-color: rgba(25, 179, 198, 0.34);
    }

    .wizard-shell .wizard-preview-wrap {
        aspect-ratio: 210 / 297;
        background: rgba(255, 255, 255, 0.06);
        border-radius: 12px;
        overflow: hidden;
        position: relative;
        box-shadow: inset 0 0 0 1px rgba(148, 163, 184, 0.35);
        border: 1px solid rgba(148, 163, 184, 0.35);
        width: min(100%, 430px);
        margin-inline: auto;
    }

    .wizard-shell .wizard-body,
    .wizard-shell .wizard-preview-scroll {
        scroll-behavior: smooth;
        overscroll-behavior: contain;
        scrollbar-width: thin;
        scrollbar-color: rgba(97, 122, 151, 0.45) transparent;
        scrollbar-gutter: stable both-edges;
    }

    .wizard-shell .wizard-body::-webkit-scrollbar,
    .wizard-shell .wizard-preview-scroll::-webkit-scrollbar {
        width: 9px;
    }

    .wizard-shell .wizard-body::-webkit-scrollbar-thumb,
    .wizard-shell .wizard-preview-scroll::-webkit-scrollbar-thumb {
        background: rgba(97, 122, 151, 0.35);
        border-radius: 999px;
        border: 2px solid transparent;
        background-clip: content-box;
    }

    .wizard-shell .wizard-body::-webkit-scrollbar-thumb:hover,
    .wizard-shell .wizard-preview-scroll::-webkit-scrollbar-thumb:hover {
        background: rgba(97, 122, 151, 0.55);
        background-clip: content-box;
    }

    .wizard-shell .wizard-preview-loading {
        background: rgba(8, 12, 22, 0.74);
        color: #bdefff;
        font-weight: 700;
    }

    .wizard-shell .text-muted {
        color: var(--wiz-muted) !important;
    }

    .wizard-shell .text-danger.small {
        display: none !important;
    }

    .wizard-shell .bg-light-subtle {
        background: rgba(148, 163, 184, 0.18) !important;
        border-color: rgba(161, 182, 211, 0.46) !important;
    }

    .wizard-shell .bg-light-subtle .text-muted {
        color: #d7e4f9 !important;
        font-weight: 700;
    }

    .wizard-shell .bg-light-subtle .fw-bold {
        color: #ffffff;
    }

    .wizard-shell .badge {
        font-weight: 700;
        border-radius: 999px;
        padding: 0.4rem 0.8rem;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 0.05em;
    }

    .wizard-shell .text-bg-success {
        background-color: rgba(16, 169, 122, 0.16) !important;
        color: #8ef0cb !important;
        border: 1px solid rgba(16, 169, 122, 0.42);
    }

    .wizard-shell .text-bg-secondary {
        background-color: rgba(255, 138, 101, 0.16) !important;
        color: #ffd2c4 !important;
        border: 1px solid rgba(255, 138, 101, 0.38);
    }

    .wizard-shell .wizard-footer {
        border-color: rgba(161, 182, 211, 0.5) !important;
    }

    @keyframes wizFadeUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (min-width: 1200px) {
        .wizard-shell .wizard-main-row {
            align-items: stretch;
        }

        .wizard-shell .wizard-editor-panel .card-body,
        .wizard-shell .wizard-preview-panel .card-body {
            height: 100%;
            min-height: 0;
            display: flex;
            flex-direction: column;
        }

        .wizard-shell .wizard-editor-panel .wizard-body,
        .wizard-shell .wizard-preview-scroll {
            flex: 0 0 auto;
            height: var(--wiz-scroll-height);
            overflow-y: auto;
            padding-right: 8px;
            padding-bottom: 10px;
        }
    }

    @media (max-width: 991.98px) {
        .wizard-shell .wizard-steps {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .wizard-shell .wizard-panel {
            height: auto;
            margin-bottom: 1.5rem;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .wizard-shell .card,
        .wizard-shell .wizard-step,
        .wizard-shell .btn {
            animation: none !important;
            transition: none !important;
        }
    }

    /* Modal Styles */
    .modal.show {
        z-index: 9999 !important;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow-y: auto;
        display: flex !important;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        border: none;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    }

    .modal-header {
        background: #fff;
        border-bottom: 1px solid #e9ecef;
        padding: 20px 30px;
    }

    .modal-header .modal-title {
        font-weight: 600;
        font-size: 1.1rem;
        color: #1a1a1a;
    }

    .modal.d-block {
        display: flex !important;
        align-items: center;
        justify-content: center;
    }

    .modal-dialog {
        position: relative;
        margin: auto;
        max-width: 1000px;
        width: 98%;
        margin-top: 15px !important;
        margin-bottom: 15px !important;
        max-height: calc(100vh - 30px);
    }

    .modal-body {
        max-height: calc(100vh - 150px);
        overflow-y: auto;
        padding: 40px 50px;
    }

    /* Template Grid */
    .template-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 40px;
        padding: 20px 0;
    }

    .template-card-wrapper {
        display: flex;
        flex-direction: column;
        align-items: stretch;
    }

    .template-card-frame {
        background-color: #f5f3f8;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .template-card-frame .card {
        border: none;
        background: #ffffff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        border-radius: 4px;
        overflow: hidden;
        position: relative;
    }

    .template-card-frame .card-img-top {
        width: 100%;
        height: auto;
        object-fit: contain;
        display: block;
        background: #ffffff;
    }

    .template-current-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        z-index: 2;
    }

    .template-card-info {
        margin-top: 12px;
        text-align: center;
        min-height: 50px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .template-card-info h6 {
        font-size: 0.95rem;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 4px;
    }

    .template-card-info small {
        font-size: 0.8rem;
        color: #999;
        line-height: 1.3;
    }

    @media (max-width: 991.98px) {
        .template-grid {
            grid-template-columns: 1fr;
            gap: 24px;
        }

        .modal-body {
            padding: 24px;
        }
    }
</style>

<div class="container-fluid py-4 page-animate">
    <div
        id="cvWizardRoot"
        data-cv-id="{{ $cv->id }}"
        data-template-slug="{{ $cv->template_slug ?? optional($cv->template)->slug ?? 'default' }}"
        data-template-name="{{ optional($cv->template)->name ?? ($cv->template_slug ?? 'Default') }}"
        class="wizard-shell"
    >
        <!-- Header -->
        <div class="wizard-header card border-0 shadow-sm mb-4">
            <div class="card-body d-flex flex-wrap gap-3 justify-content-between align-items-center p-4">
                <div>
                    <h1>CV Builder Wizard</h1>
                    <p class="text-muted mb-0 mt-1">Create your professional CV step-by-step.</p>
                </div>
                <a href="{{ route('cvs.index') }}" class="btn btn-outline-secondary">
                    &larr; Back
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="row g-4 wizard-main-row">
            <!-- Editor Side -->
            <div class="col-12 col-xl-6">
                <div class="card border-0 shadow-sm wizard-panel wizard-editor-panel h-100">
                    <div class="card-body p-4 d-flex flex-column">
                        
                        <!-- Steps Nav -->
                        <nav class="wizard-steps mb-4" aria-label="CV Builder Steps">
                            <button type="button" class="wizard-step" v-for="(step, idx) in steps" :key="step.key" :class="stepClass(idx + 1)" @click="goToStep(idx + 1)">
                                <span class="wizard-step-index">@{{ idx + 1 }}</span>
                                <span class="wizard-step-label">@{{ step.label }}</span>
                            </button>
                        </nav>

                        <!-- Form Body -->
                        <div class="wizard-body flex-grow-1 overflow-auto">
                            <!-- Components Injected Here -->
                            <personal-step v-if="currentStep === 1" :model-value="form.personal" :errors="errors" :saving="isSaving" :photo-enabled="templateSupportsPhoto" :accent-enabled="templateSupportsAccent" @update:model-value="updatePersonal" @remove-photo="removePhoto"></personal-step>
                            <experience-step v-if="currentStep === 2" :items="form.experiences" :errors="errors" @update:items="updateExperiences"></experience-step>
                            <education-step v-if="currentStep === 3" :items="form.educations" :errors="errors" @update:items="updateEducations"></education-step>
                            <skills-step v-if="currentStep === 4" :items="form.skills" :errors="errors" @update:items="updateSkills"></skills-step>
                            <review-step v-if="currentStep === 5" :cv="state.cv" :counts="counts" :status="form.review.status" :public-url="state.cv.public_url || ''" :busy="isSaving" @save-draft="saveDraft" @publish="publishCv" @download-pdf="downloadPdf"></review-step>
                        </div>

                        <!-- Footer Actions -->
                        <div class="wizard-footer mt-4 pt-3 border-top d-flex justify-content-between align-items-center" style="border-color: var(--y2k-border) !important;">
                            <button type="button" class="btn btn-outline-secondary" @click="prevStep" :disabled="currentStep === 1 || isSaving">
                                Back
                            </button>

                            <div class="d-flex align-items-center gap-3">
                                <span class="small text-muted" v-if="lastSavedAt">
                                    <span class="spinner-border spinner-border-sm me-2" v-if="isSaving" style="width:1rem; height:1rem;"></span>
                                    Autosaved @{{ lastSavedAt }}
                                </span>
                                <button v-if="currentStep < 5" type="button" class="btn btn-primary" @click="nextStep" :disabled="isSaving">
                                    Next &rarr;
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Preview Side -->
            <div class="col-12 col-xl-6">
                <div class="card border-0 shadow-sm position-sticky wizard-panel wizard-preview-panel" style="top: 1rem;">
                    <div class="card-body p-4 d-flex flex-column">
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2 class="h6 mb-0">Live Preview</h2>
                            <span class="badge" :class="form.review.status === 'published' ? 'text-bg-success' : 'text-bg-secondary'">
                                @{{ form.review.status === 'published' ? 'Published' : 'Draft' }}
                            </span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <p class="text-muted small mb-0">
                                    Template: <strong style="color: var(--wiz-text);">@{{ previewData.templateName || '-' }}</strong>
                                </p>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary" @click="showTemplateModal = true" :disabled="isSaving">
                                Change
                            </button>
                        </div>

                        <div class="wizard-preview-scroll flex-grow-1">
                            <div class="wizard-preview-wrap">
                                <div v-if="previewLoading" class="wizard-preview-loading position-absolute w-100 h-100 d-flex align-items-center justify-content-center">
                                    <span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>
                                    <span>Rendering...</span>
                                </div>
                                <iframe
                                    class="wizard-template-frame w-100 h-100 border-0"
                                    :srcdoc="previewHtml"
                                    title="CV Template Preview"
                                ></iframe>
                            </div>

                            <div class="toast-holder mt-3" aria-live="polite">
                                <div v-if="toast" class="alert alert-success py-2 mb-0 small d-flex align-items-center gap-2" style="background: rgba(16, 169, 122, 0.12); border-color: rgba(16, 169, 122, 0.44); color: #0e7d59; border-radius: 10px;">
                                    <span style="font-size: 1.2em;">✓</span> @{{ toast }}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Template Switch Modal -->
        <div v-if="showTemplateModal" class="modal show d-block" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-2">
                        <h5 class="modal-title">Select Template</h5>
                        <button type="button" class="btn-close" @click="showTemplateModal = false" :disabled="isSaving"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small mb-4">
                            Choose a template. Your CV data will be preserved.
                        </p>
                        <div class="template-grid">
                            <div v-for="template in availableTemplates" :key="template.slug" class="template-card-wrapper">
                                <div class="template-card-frame">
                                    <div 
                                        class="card cursor-pointer transition-all h-100"
                                        :class="{ 'border-primary border-2': template.slug === currentTemplatSlug }"
                                        @click="selectNewTemplate(template.slug)"
                                        style="cursor: pointer; position: relative;"
                                    >
                                        <img 
                                            v-if="template.thumbnail_url" 
                                            :src="template.thumbnail_url" 
                                            class="card-img-top" 
                                            :alt="template.name"
                                        >
                                        <div v-else class="card-img-top d-flex align-items-center justify-content-center" style="background: #f0f0f0; color: #999;">
                                            <span>No Image</span>
                                        </div>
                                        <div v-if="template.slug === currentTemplatSlug" class="template-current-badge">
                                            <span class="badge text-bg-primary">Current</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="template-card-info">
                                    <h6 class="mb-1">@{{ template.name }}</h6>
                                    <small class="text-muted d-block">@{{ template.description }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-outline-secondary" @click="showTemplateModal = false" :disabled="isSaving">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
<script type="module" src="{{ asset('js/cv-wizard/app.js') }}"></script>
@endsection