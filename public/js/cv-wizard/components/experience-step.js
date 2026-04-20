export const ExperienceStep = {
  name: 'ExperienceStep',
  props: {
    items: { type: Array, required: true },
    errors: { type: Object, default: () => ({}) },
  },
  emits: ['update:items'],
  methods: {
    addItem() {
      this.$emit('update:items', [
        ...this.items,
        { id: null, position: '', company: '', start_date: '', end_date: '', description: '' },
      ]);
    },
    updateItem(index, field, value) {
      const next = this.items.map((item, i) => (i === index ? { ...item, [field]: value } : item));
      this.$emit('update:items', next);
    },
    removeItem(index) {
      const next = this.items.filter((_, i) => i !== index);
      this.$emit('update:items', next);
    },
    fieldError(index, field) {
      return this.errors[`experiences.${index}.${field}`] || null;
    },
  },
  template: `
    <section>
      <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
          <h3 class="h6 mb-0">Step 2: Experience</h3>
          <p class="text-muted small mb-0">Tambahkan pengalaman kerja, bisa multiple.</p>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary" @click="addItem">Add Experience</button>
      </div>

      <div class="alert alert-light border" v-if="items.length === 0">Belum ada experience. Tambahkan minimal 1 untuk hasil CV lebih baik.</div>

      <div class="card border mb-3" v-for="(item, idx) in items" :key="item.id || idx">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <strong class="small">Experience {{ idx + 1 }}</strong>
            <button type="button" class="btn btn-sm btn-outline-danger" @click="removeItem(idx)">Remove</button>
          </div>

          <div class="row g-2">
            <div class="col-md-6">
              <label class="form-label">Position</label>
              <input class="form-control" :value="item.position" @input="updateItem(idx, 'position', $event.target.value)" />
              <div class="text-danger small" v-if="fieldError(idx, 'position')">{{ fieldError(idx, 'position') }}</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Company</label>
              <input class="form-control" :value="item.company" @input="updateItem(idx, 'company', $event.target.value)" />
              <div class="text-danger small" v-if="fieldError(idx, 'company')">{{ fieldError(idx, 'company') }}</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Start Date</label>
              <input type="date" class="form-control" :value="item.start_date" @input="updateItem(idx, 'start_date', $event.target.value)" />
              <div class="text-danger small" v-if="fieldError(idx, 'start_date')">{{ fieldError(idx, 'start_date') }}</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">End Date</label>
              <input type="date" class="form-control" :value="item.end_date" @input="updateItem(idx, 'end_date', $event.target.value)" />
              <div class="text-danger small" v-if="fieldError(idx, 'end_date')">{{ fieldError(idx, 'end_date') }}</div>
            </div>
            <div class="col-12">
              <label class="form-label">Description</label>
              <textarea class="form-control" rows="3" :value="item.description" @input="updateItem(idx, 'description', $event.target.value)"></textarea>
            </div>
          </div>
        </div>
      </div>
    </section>
  `,
};
