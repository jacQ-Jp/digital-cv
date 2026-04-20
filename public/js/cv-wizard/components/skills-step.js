export const SkillsStep = {
  name: 'SkillsStep',
  props: {
    items: { type: Array, required: true },
    errors: { type: Object, default: () => ({}) },
  },
  emits: ['update:items'],
  methods: {
    addItem() {
      this.$emit('update:items', [
        ...this.items,
        { id: null, name: '' },
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
      return this.errors[`skills.${index}.${field}`] || null;
    },
  },
  template: `
    <section>
      <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
          <h3 class="h6 mb-0">Step 4: Skills</h3>
          <p class="text-muted small mb-0">Tambahkan daftar skill utama.</p>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary" @click="addItem">Add Skill</button>
      </div>

      <div class="alert alert-light border" v-if="items.length === 0">Belum ada skill. Tambahkan beberapa skill.</div>

      <div class="card border mb-3" v-for="(item, idx) in items" :key="item.id || idx">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <strong class="small">Skill {{ idx + 1 }}</strong>
            <button type="button" class="btn btn-sm btn-outline-danger" @click="removeItem(idx)">Remove</button>
          </div>

          <div class="row g-2">
            <div class="col-12">
              <label class="form-label">Skill</label>
              <input class="form-control" :value="item.name" @input="updateItem(idx, 'name', $event.target.value)" />
              <div class="text-danger small" v-if="fieldError(idx, 'name')">{{ fieldError(idx, 'name') }}</div>
            </div>
          </div>
        </div>
      </div>
    </section>
  `,
};
