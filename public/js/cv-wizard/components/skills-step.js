export const SkillsStep = {
  name: 'SkillsStep',
  props: {
    items: { type: Array, required: true },
    errors: { type: Object, default: () => ({}) },
  },
  emits: ['update:items'],
  data() {
    return {
      levels: ['Novice', 'Beginner', 'Skillful', 'Experienced', 'Expert'],
    };
  },
  methods: {
    addItem() {
      this.$emit('update:items', [
        ...this.items,
        { id: null, name: '', level: 'Beginner' },
      ]);
    },
    updateItem(index, field, value) {
      const next = this.items.map((item, i) => (i === index ? { ...item, [field]: value } : item));
      this.$emit('update:items', next);
    },
    levelToIndex(level) {
      const normalized = String(level || '').toLowerCase();
      const idx = this.levels.findIndex((name) => name.toLowerCase() === normalized);
      return idx >= 0 ? idx : 1;
    },
    updateLevelByIndex(index, rawValue) {
      const levelIndex = Number(rawValue);
      const safeIndex = Number.isFinite(levelIndex) ? Math.min(Math.max(levelIndex, 0), this.levels.length - 1) : 1;
      this.updateItem(index, 'level', this.levels[safeIndex]);
    },
    levelBtnClass(item, levelName) {
      const active = (item.level || '').toLowerCase() === levelName.toLowerCase();
      return active ? 'btn btn-sm btn-primary' : 'btn btn-sm btn-outline-secondary';
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
            <div class="col-md-7">
              <label class="form-label">Skill</label>
              <input class="form-control" :value="item.name" @input="updateItem(idx, 'name', $event.target.value)" />
              <div class="text-danger small" v-if="fieldError(idx, 'name')">{{ fieldError(idx, 'name') }}</div>
            </div>
            <div class="col-md-5">
              <label class="form-label">Level</label>
              <input
                type="range"
                class="form-range"
                min="0"
                :max="levels.length - 1"
                step="1"
                :value="levelToIndex(item.level)"
                @input="updateLevelByIndex(idx, $event.target.value)"
              />
              <div class="d-flex flex-wrap gap-1 mt-1">
                <button
                  type="button"
                  v-for="(level, levelIdx) in levels"
                  :key="'level-' + idx + '-' + level"
                  :class="levelBtnClass(item, level)"
                  @click="updateLevelByIndex(idx, levelIdx)"
                >
                  {{ level }}
                </button>
              </div>
              <div class="small mt-2 text-muted">Aktif: <strong>{{ item.level || levels[levelToIndex(item.level)] }}</strong></div>
              <div class="text-danger small" v-if="fieldError(idx, 'level')">{{ fieldError(idx, 'level') }}</div>
            </div>
          </div>
        </div>
      </div>
    </section>
  `,
};
