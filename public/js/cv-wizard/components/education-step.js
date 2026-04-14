export const EducationStep = {
  name: 'EducationStep',
  props: {
    items: { type: Array, required: true },
    errors: { type: Object, default: () => ({}) },
  },
  emits: ['update:items'],
  methods: {
    addItem() {
      this.$emit('update:items', [
        ...this.items,
        { id: null, school: '', degree: '', year: '' },
      ]);
    },
    updateItem(index, field, value) {
      const next = this.items.map((item, i) => (i === index ? { ...item, [field]: value } : item));
      this.$emit('update:items', next);
    },
    removeItem(index) {
      this.$emit('update:items', this.items.filter((_, i) => i !== index));
    },
    fieldError(index, field) {
      return this.errors[`educations.${index}.${field}`] || null;
    },
  },
  template: `
    <section>
      <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
          <h3 class="h6 mb-0">Step 3: Education</h3>
          <p class="text-muted small mb-0">Tambahkan riwayat pendidikan (multiple).</p>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary" @click="addItem">Add Education</button>
      </div>

      <div class="alert alert-light border" v-if="items.length === 0">Belum ada education. Tambahkan minimal 1.</div>

      <div class="card border mb-3" v-for="(item, idx) in items" :key="item.id || idx">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <strong class="small">Education {{ idx + 1 }}</strong>
            <button type="button" class="btn btn-sm btn-outline-danger" @click="removeItem(idx)">Remove</button>
          </div>

          <div class="row g-2">
            <div class="col-md-6">
              <label class="form-label">School</label>
              <input class="form-control" :value="item.school" @input="updateItem(idx, 'school', $event.target.value)" />
              <div class="text-danger small" v-if="fieldError(idx, 'school')">{{ fieldError(idx, 'school') }}</div>
            </div>
            <div class="col-md-4">
              <label class="form-label">Degree</label>
              <input class="form-control" :value="item.degree" @input="updateItem(idx, 'degree', $event.target.value)" />
              <div class="text-danger small" v-if="fieldError(idx, 'degree')">{{ fieldError(idx, 'degree') }}</div>
            </div>
            <div class="col-md-2">
              <label class="form-label">Year</label>
              <input class="form-control" :value="item.year" @input="updateItem(idx, 'year', $event.target.value)" placeholder="2024" />
              <div class="text-danger small" v-if="fieldError(idx, 'year')">{{ fieldError(idx, 'year') }}</div>
            </div>
          </div>
        </div>
      </div>
    </section>
  `,
};
