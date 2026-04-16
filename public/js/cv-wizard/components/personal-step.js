export const PersonalStep = {
  name: 'PersonalStep',
  props: {
    modelValue: { type: Object, required: true },
    errors: { type: Object, default: () => ({}) },
    saving: { type: Boolean, default: false },
  },
  emits: ['update:model-value', 'remove-photo'],
  data() {
    return {
      accentPalette: [
        { value: '#7C3AED', label: 'Violet' },
        { value: '#0EA5A4', label: 'Teal' },
        { value: '#3B82F6', label: 'Blue' },
        { value: '#EA580C', label: 'Orange' },
        { value: '#334155', label: 'Slate' },
        { value: '#166534', label: 'Forest' },
        { value: '#BE123C', label: 'Rose' },
      ],
    };
  },
  methods: {
    updateField(field, value) {
      this.$emit('update:model-value', { ...this.modelValue, [field]: value });
    },
    selectAccent(hex) {
      this.updateField('accent_color', hex);
    },
    isAccentActive(hex) {
      const current = String(this.modelValue.accent_color || '#7C3AED').toUpperCase();
      return current === hex;
    },
    onPhotoChange(event) {
      const file = event.target.files?.[0] || null;
      this.updateField('photoFile', file);
    },
  },
  template: `
    <section>
      <h3 class="h6">Step 1: Personal Information</h3>
      <p class="text-muted small">Isi data utama CV. Perubahan otomatis tersimpan.</p>

      <div class="mb-3">
        <label class="form-label">Headline / Role</label>
        <input class="form-control" :value="modelValue.title" @input="updateField('title', $event.target.value)" placeholder="Frontend Engineer" />
        <div class="text-danger small" v-if="errors.title">{{ errors.title }}</div>
      </div>

      <div class="mb-3">
        <label class="form-label">Full Name</label>
        <input class="form-control" :value="modelValue.personal_name" @input="updateField('personal_name', $event.target.value)" placeholder="Your name" />
        <div class="text-danger small" v-if="errors.personal_name">{{ errors.personal_name }}</div>
      </div>

      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" class="form-control" :value="modelValue.personal_email" @input="updateField('personal_email', $event.target.value)" placeholder="you@example.com" />
        <div class="text-danger small" v-if="errors.personal_email">{{ errors.personal_email }}</div>
      </div>

      <div class="mb-3">
        <label class="form-label">Summary</label>
        <textarea class="form-control" rows="4" :value="modelValue.summary" @input="updateField('summary', $event.target.value)" placeholder="Brief professional summary"></textarea>
        <div class="text-danger small" v-if="errors.summary">{{ errors.summary }}</div>
      </div>

      <div class="mb-3">
        <label class="form-label">Template Accent Color</label>
        <div class="d-flex flex-wrap gap-2">
          <button
            type="button"
            class="btn p-0 border-0 bg-transparent"
            v-for="tone in accentPalette"
            :key="tone.value"
            :title="tone.label"
            @click="selectAccent(tone.value)"
          >
            <span
              :style="{
                display: 'inline-block',
                width: '30px',
                height: '30px',
                borderRadius: '999px',
                background: tone.value,
                border: isAccentActive(tone.value) ? '3px solid #0f172a' : '1px solid rgba(15,23,42,0.2)',
                boxShadow: isAccentActive(tone.value) ? '0 0 0 2px #ffffff' : 'none'
              }"
            ></span>
          </button>
        </div>
        <div class="small text-muted mt-2">Warna aktif: <strong>{{ modelValue.accent_color || '#7C3AED' }}</strong>. Berlaku untuk live preview, halaman publik, dan PDF.</div>
      </div>

      <div class="mb-3">
        <label class="form-label">Photo</label>
        <input type="file" class="form-control" accept="image/*" @change="onPhotoChange" />
        <div class="d-flex gap-2 mt-2" v-if="modelValue.photo_url">
          <img :src="modelValue.photo_url" alt="Photo" style="width:56px;height:56px;border-radius:8px;object-fit:cover;border:1px solid #e2e8f0;" />
          <button type="button" class="btn btn-sm btn-outline-danger" @click="$emit('remove-photo')" :disabled="saving">Remove Photo</button>
        </div>
        <div class="text-danger small" v-if="errors.photo">{{ errors.photo }}</div>
      </div>
    </section>
  `,
};
