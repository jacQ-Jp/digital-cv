export const CvLivePreview = {
  name: 'CvLivePreview',
  props: {
    previewData: { type: Object, required: true },
  },
  computed: {
    theme() {
      const slug = this.previewData.templateSlug || 'default';

      const map = {
        default: { accent: '#475569', sideBg: '#f8fafc' },
        classic: { accent: '#374151', sideBg: '#f9fafb' },
        minimalist: { accent: '#52525b', sideBg: '#fafafa' },
        modern: { accent: '#2563eb', sideBg: '#eff6ff' },
        creative: { accent: '#7c3aed', sideBg: '#f5f3ff' },
        timeline: { accent: '#0ea5e9', sideBg: '#ecfeff' },
        sidebar: { accent: '#0369a1', sideBg: '#f0f9ff' },
        'profile-sidebar': { accent: '#0f766e', sideBg: '#ecfdf5' },
        ats: { accent: '#1f2937', sideBg: '#f9fafb' },
        'mono-poster': { accent: '#111827', sideBg: '#f4f4f5' },
      };

      return map[slug] || map.default;
    },
    safeName() {
      return this.previewData.personal_name?.trim() || 'Your Name';
    },
    safeRole() {
      return this.previewData.title?.trim() || 'Professional Profile';
    },
    safeEmail() {
      return this.previewData.personal_email?.trim() || 'you@example.com';
    },
    summary() {
      return this.previewData.summary?.trim() || '';
    },
    experiences() {
      return (this.previewData.experiences || []).filter((item) => (
        item?.position || item?.company || item?.start_date || item?.end_date || item?.description
      ));
    },
    educations() {
      return (this.previewData.educations || []).filter((item) => (
        item?.school || item?.degree || item?.year
      ));
    },
    skills() {
      return (this.previewData.skills || []).filter((item) => item?.name);
    },
  },
  methods: {
    dateRange(item) {
      const start = item?.start_date || '';
      const end = item?.end_date || 'Present';
      if (!start && !item?.end_date) return '';
      return `${start || '-'} - ${end}`;
    },
  },
  template: `
    <div class="wizard-live">
      <article class="wizard-paper" :style="{ '--wizard-accent': theme.accent, '--wizard-side-bg': theme.sideBg }">
        <header class="wizard-paper-header">
          <h3 class="wizard-paper-name">{{ safeName }}</h3>
          <div class="wizard-paper-role">{{ safeRole }}</div>
          <a class="wizard-paper-email" :href="'mailto:' + safeEmail">{{ safeEmail }}</a>
          <p v-if="summary" class="wizard-paper-summary">{{ summary }}</p>
        </header>

        <div class="wizard-paper-grid">
          <div>
            <section class="wizard-paper-section">
              <h4 class="wizard-paper-title">Experience</h4>
              <template v-if="experiences.length">
                <article class="wizard-item" v-for="(item, idx) in experiences" :key="item.id || 'exp-' + idx">
                  <div class="wizard-item-head">
                    <div>
                      <p class="wizard-item-main">{{ item.position || 'Position' }}</p>
                      <p class="wizard-item-sub">{{ item.company || 'Company' }}</p>
                    </div>
                    <div class="wizard-item-date">{{ dateRange(item) }}</div>
                  </div>
                  <p class="wizard-item-desc" v-if="item.description">{{ item.description }}</p>
                </article>
              </template>
              <p v-else class="wizard-fallback">No experience listed</p>
            </section>

            <section class="wizard-paper-section">
              <h4 class="wizard-paper-title">Education</h4>
              <template v-if="educations.length">
                <article class="wizard-item" v-for="(item, idx) in educations" :key="item.id || 'edu-' + idx">
                  <div class="wizard-item-head">
                    <div>
                      <p class="wizard-item-main">{{ item.school || 'School' }}</p>
                      <p class="wizard-item-sub">{{ item.degree || 'Degree' }}</p>
                    </div>
                    <div class="wizard-item-date">{{ item.year || '-' }}</div>
                  </div>
                </article>
              </template>
              <p v-else class="wizard-fallback">No education listed</p>
            </section>
          </div>

          <aside>
            <div class="wizard-paper-right">
              <section class="wizard-paper-section">
                <h4 class="wizard-paper-title">Skills</h4>
                <div v-if="skills.length" class="wizard-skill-tags">
                  <span class="wizard-skill-tag" v-for="(item, idx) in skills" :key="item.id || 'skill-' + idx">
                    {{ item.name }}<template v-if="item.level"> · {{ item.level }}</template>
                  </span>
                </div>
                <p v-else class="wizard-fallback">No skills listed</p>
              </section>

              <section class="wizard-paper-section">
                <h4 class="wizard-paper-title">Template</h4>
                <p class="wizard-item-sub">{{ previewData.templateName || '-' }}</p>
              </section>
            </div>
          </aside>
        </div>
      </article>
    </div>
  `,
};
