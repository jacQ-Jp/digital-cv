@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <div id="cvWizardRoot" data-cv-id="{{ $cv->id }}" class="wizard-shell">
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
                            <review-step v-if="currentStep === 5" :cv="state.cv" :counts="counts" :status="form.review.status" :busy="isSaving" @update:status="updateReviewStatus" @copy-link="copyPublicLink" @download-pdf="downloadPdf" @finish="finishPublish"></review-step>
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
                            <cv-live-preview :preview-data="previewData"></cv-live-preview>
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
    background: #fff;
    position: relative;
}

.wizard-shell .wizard-live {
    width: 100%;
    height: 100%;
    background: #f3f4f6;
    overflow: auto;
    padding: 12px;
}

.wizard-shell .wizard-paper {
    width: 100%;
    min-height: 100%;
    background: #fff;
    padding: 18px;
    box-shadow: 0 6px 18px rgba(15, 23, 42, .12);
    border-top: 4px solid var(--wizard-accent, #475569);
    color: #111827;
    font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, 'Segoe UI', sans-serif;
    font-size: 12px;
    overflow-x: hidden;
    box-sizing: border-box;
}

.wizard-shell .wizard-paper * {
    box-sizing: border-box;
}

.wizard-shell .wizard-paper-header {
    border-bottom: 1px solid #e5e7eb;
    padding-bottom: 10px;
}

.wizard-shell .wizard-paper-name {
    margin: 0;
    font-size: 18px;
    font-weight: 800;
    letter-spacing: -.02em;
}

.wizard-shell .wizard-paper-role {
    margin-top: 2px;
    color: #374151;
    font-size: 12px;
}

.wizard-shell .wizard-paper-email {
    display: inline-block;
    margin-top: 5px;
    color: #4b5563;
    font-size: 11px;
    text-decoration: none;
}

.wizard-shell .wizard-paper-summary {
    margin-top: 8px;
    color: #374151;
    line-height: 1.45;
}

.wizard-shell .wizard-paper-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 14px;
    margin-top: 12px;
}

.wizard-shell .wizard-paper-grid > * {
    min-width: 0;
}

.wizard-shell .wizard-paper-section {
    margin-top: 14px;
    border-bottom: 1px solid #e5e7eb;
    padding-bottom: 10px;
}

.wizard-shell .wizard-paper-right {
    background: var(--wizard-side-bg, #f8fafc);
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 10px;
    min-width: 0;
}

.wizard-shell .wizard-paper-section:first-child {
    margin-top: 0;
}

.wizard-shell .wizard-paper-section:last-child {
    border-bottom: 0;
    padding-bottom: 0;
}

.wizard-shell .wizard-paper-title {
    margin: 0 0 8px;
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: .13em;
    color: #6b7280;
    font-weight: 700;
}

.wizard-shell .wizard-item {
    margin-bottom: 8px;
}

.wizard-shell .wizard-item:last-child {
    margin-bottom: 0;
}

.wizard-shell .wizard-item-head {
    display: flex;
    justify-content: space-between;
    gap: 8px;
    align-items: baseline;
    flex-wrap: wrap;
}

.wizard-shell .wizard-item-main {
    margin: 0;
    font-weight: 700;
    font-size: 12px;
}

.wizard-shell .wizard-item-sub {
    margin: 1px 0 0;
    color: #4b5563;
    font-size: 11px;
}

.wizard-shell .wizard-item-date {
    color: #6b7280;
    font-size: 10px;
    white-space: normal;
    text-align: right;
}

.wizard-shell .wizard-item-desc {
    margin: 4px 0 0;
    color: #374151;
    line-height: 1.4;
}

.wizard-shell .wizard-paper-name,
.wizard-shell .wizard-paper-role,
.wizard-shell .wizard-paper-email,
.wizard-shell .wizard-paper-summary,
.wizard-shell .wizard-item-main,
.wizard-shell .wizard-item-sub,
.wizard-shell .wizard-item-desc,
.wizard-shell .wizard-skill-tag,
.wizard-shell .wizard-fallback {
    overflow-wrap: anywhere;
    word-break: break-word;
}

.wizard-shell .wizard-skill-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.wizard-shell .wizard-skill-tag {
    border: 1px solid #d1d5db;
    border-radius: 999px;
    padding: 3px 8px;
    font-size: 10px;
    color: #374151;
    background: #fff;
}

.wizard-shell .wizard-fallback {
    margin: 0;
    color: #6b7280;
    font-style: italic;
    font-size: 11px;
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
