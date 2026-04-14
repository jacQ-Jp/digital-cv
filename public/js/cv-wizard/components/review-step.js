export const ReviewStep = {
  name: 'ReviewStep',
  props: {
    cv: { type: Object, required: true },
    counts: { type: Object, required: true },
    status: { type: String, required: true },
    busy: { type: Boolean, default: false },
  },
  emits: ['update:status', 'copy-link', 'download-pdf', 'finish'],
  template: `
    <section>
      <h3 class="h6">Step 5: Review & Publish</h3>
      <p class="text-muted small">Status publish hanya muncul di step terakhir.</p>

      <div class="card border mb-3">
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <div class="small text-muted">Template</div>
              <div class="fw-semibold">{{ cv.template_name || '-' }}</div>
            </div>
            <div class="col-md-6">
              <div class="small text-muted">Headline</div>
              <div class="fw-semibold">{{ cv.title || '-' }}</div>
            </div>
            <div class="col-md-4"><span class="badge text-bg-light">Experience: {{ counts.experiences }}</span></div>
            <div class="col-md-4"><span class="badge text-bg-light">Education: {{ counts.educations }}</span></div>
            <div class="col-md-4"><span class="badge text-bg-light">Skills: {{ counts.skills }}</span></div>
          </div>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Publish Status</label>
        <select class="form-select" :value="status" @change="$emit('update:status', $event.target.value)">
          <option value="draft">Draft (default)</option>
          <option value="published">Publish</option>
        </select>
      </div>

      <div class="d-flex flex-wrap gap-2">
        <button type="button" class="btn btn-outline-success" @click="$emit('copy-link')" :disabled="busy">Copy Link</button>
        <button type="button" class="btn btn-outline-dark" @click="$emit('download-pdf')" :disabled="busy">Download PDF</button>
        <button type="button" class="btn btn-primary" @click="$emit('finish')" :disabled="busy">Finish</button>
      </div>
    </section>
  `,
};
