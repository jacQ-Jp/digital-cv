@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <div
        id="cvWizardRoot"
        data-cv-id="{{ $cv->id }}"
        data-template-slug="{{ $cv->template_slug ?? optional($cv->template)->slug ?? 'default' }}"
        data-template-name="{{ optional($cv->template)->name ?? ($cv->template_slug ?? 'Default') }}"
        class="wizard-shell"
    >
        <div class="wizard-header card border-0 shadow-sm mb-3">
            <div class="card-body d-flex flex-wrap gap-2 justify-content-between align-items-center">
                <div>
                    <h1 class="h5 mb-1">CV Builder Wizard</h1>
                    <p class="text-muted small mb-0">Step-by-step with autosave and live preview.</p>
                </div>
                <a href="{{ route('cvs.index') }}" class="btn btn-sm btn-outline-secondary">Back to CV List</a>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12 col-xl-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <nav class="wizard-steps mb-3" aria-label="CV Builder Steps">
                            <button type="button" class="wizard-step" v-for="(step, idx) in steps" :key="step.key" :class="stepClass(idx + 1)" @click="goToStep(idx + 1)">
                                <span class="wizard-step-index">@{{ idx + 1 }}</span>
                                <span class="wizard-step-label">@{{ step.label }}</span>
                            </button>
                        </nav>

                        <div class="wizard-body">
                            <personal-step v-if="currentStep === 1" :model-value="form.personal" :errors="errors" :saving="isSaving" @update:model-value="updatePersonal" @remove-photo="removePhoto"></personal-step>
                            <experience-step v-if="currentStep === 2" :items="form.experiences" :errors="errors" @update:items="updateExperiences"></experience-step>
                            <education-step v-if="currentStep === 3" :items="form.educations" :errors="errors" @update:items="updateEducations"></education-step>
                            <skills-step v-if="currentStep === 4" :items="form.skills" :errors="errors" @update:items="updateSkills"></skills-step>
                            <review-step v-if="currentStep === 5" :cv="state.cv" :counts="counts" :status="form.review.status" :public-url="state.cv.public_url || ''" :busy="isSaving" @save-draft="saveDraft" @publish="publishCv" @download-pdf="downloadPdf"></review-step>
                        </div>

                        <div class="wizard-footer mt-3 d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-outline-secondary" @click="prevStep" :disabled="currentStep === 1 || isSaving">Back</button>

                            <div class="d-flex align-items-center gap-2">
                                <span class="small text-muted" v-if="lastSavedAt">Autosaved @{{ lastSavedAt }}</span>
                                <button v-if="currentStep < 5" type="button" class="btn btn-primary" @click="nextStep" :disabled="isSaving">Next</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-6">
                <div class="card border-0 shadow-sm position-sticky" style="top: 1rem;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h2 class="h6 mb-0">Live Preview</h2>
                            <span class="badge" :class="form.review.status === 'published' ? 'text-bg-success' : 'text-bg-secondary'">@{{ form.review.status === 'published' ? 'Published' : 'Draft' }}</span>
                        </div>

                        <p class="text-muted small mb-2">Template: <strong>@{{ previewData.templateName || '-' }}</strong></p>

                        <div class="wizard-preview-wrap">
                            <div v-if="previewLoading" class="wizard-preview-loading">
                                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                                <span>Rendering template...</span>
                            </div>
                            <iframe
                                class="wizard-template-frame"
                                :srcdoc="previewHtml"
                                title="CV Template Preview"
                            ></iframe>
                        </div>

                        <div class="toast-holder mt-2" aria-live="polite">
                            <div v-if="toast" class="alert alert-success py-2 mb-0 small">@{{ toast }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.wizard-shell .wizard-steps {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: .5rem;
}
.wizard-shell .wizard-step {
    border: 1px solid #e2e8f0;
    background: #fff;
    border-radius: 10px;
    padding: .5rem;
    text-align: left;
    display: flex;
    gap: .5rem;
    align-items: center;
}
.wizard-shell .wizard-step-index {
    width: 1.5rem;
    height: 1.5rem;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: .75rem;
    background: #e2e8f0;
    color: #334155;
}
.wizard-shell .wizard-step-label {
    font-size: .78rem;
    line-height: 1.1;
    color: #334155;
}
.wizard-shell .wizard-step.is-active {
    border-color: #3b82f6;
    background: #eff6ff;
}
.wizard-shell .wizard-step.is-active .wizard-step-index {
    background: #3b82f6;
    color: #fff;
}
.wizard-shell .wizard-step.is-done {
    border-color: #86efac;
    background: #f0fdf4;
}
.wizard-shell .wizard-preview-wrap {
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    aspect-ratio: 210 / 297;
    background: #f1f5f9;
    position: relative;
}

.wizard-shell .wizard-template-frame {
    width: 100%;
    height: 100%;
    border: 0;
    display: block;
    background: #fff;
}

.wizard-shell .wizard-preview-loading {
    position: absolute;
    inset: 0;
    z-index: 5;
    background: rgba(241, 245, 249, .88);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
    color: #334155;
    font-size: .85rem;
}
@media (max-width: 900px) {
    .wizard-shell .wizard-steps {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}
</style>

<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
<script type="module" src="{{ asset('js/cv-wizard/app.js') }}"></script>
@endsection
