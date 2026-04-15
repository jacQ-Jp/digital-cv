export const ReviewStep = {
  name: 'ReviewStep',
  props: {
    cv: { type: Object, required: true },
    counts: { type: Object, required: true },
    status: { type: String, required: true },
    publicUrl: { type: String, default: '' },
    busy: { type: Boolean, default: false },
  },
  emits: ['save-draft', 'publish', 'download-pdf'],
  template: `
    <section>
      <h3 class="h6">Step 5: Review & Publish</h3>
      <p class="text-muted small">Final check: review desain CV langsung di panel preview kanan.</p>

      <div class="card border shadow-sm mb-3">
        <div class="card-body">
          <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
            <div>
              <div class="small text-muted">Status</div>
              <div class="fw-semibold">{{ status === 'published' ? 'Published' : 'Draft' }}</div>
            </div>
            <div class="small text-muted text-end">
              Template: <strong>{{ template_name || '-' }}</strong>
            </div>
          </div>

          <div class="row g-2 mb-3">
            <div class="col-12 col-md-4">
              <div class="border rounded-3 p-2 bg-light-subtle">
                <div class="small text-muted">Experience</div>
                <div class="fw-bold fs-5">{{ counts.experiences }}</div>
              </div>
            </div>
            <div class="col-12 col-md-4">
              <div class="border rounded-3 p-2 bg-light-subtle">
                <div class="small text-muted">Education</div>
                <div class="fw-bold fs-5">{{ counts.educations }}</div>
              </div>
            </div>
            <div class="col-12 col-md-4">
              <div class="border rounded-3 p-2 bg-light-subtle">
                <div class="small text-muted">Skills</div>
                <div class="fw-bold fs-5">{{ counts.skills }}</div>
              </div>
            </div>
          </div>

          <div class="border rounded-3 p-3 mb-3">
            <div class="small text-muted mb-1">Checklist</div>
            <div class="small">Pastikan konten, spacing, dan tipografi pada preview kanan sudah final.</div>
            <div class="small text-muted mt-2">Preview kanan menggunakan template Blade yang sama dengan halaman public dan PDF.</div>
          </div>

          <div class="border rounded-3 p-3 mb-3" v-if="publicUrl">
            <div class="small text-muted">Public Link</div>
            <div class="small text-break">{{ publicUrl }}</div>
          </div>

          <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-outline-secondary" @click="$emit('save-draft')" :disabled="busy">Save as Draft</button>
            <button type="button" class="btn btn-primary" @click="$emit('publish')" :disabled="busy">Publish</button>
            <button type="button" class="btn btn-outline-dark" @click="$emit('download-pdf')" :disabled="busy">Download PDF</button>
          </div>
        </div>
      </div>
    </section>
  `,
};
