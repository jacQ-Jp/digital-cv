import { PersonalStep } from './components/personal-step.js';
import { ExperienceStep } from './components/experience-step.js';
import { EducationStep } from './components/education-step.js';
import { SkillsStep } from './components/skills-step.js';
import { ReviewStep } from './components/review-step.js';

const { createApp } = window.Vue;

const root = document.getElementById('cvWizardRoot');

if (root) {
  const cvId = root.getAttribute('data-cv-id');
  const baseUrl = `/cvs/${cvId}/wizard`;
  const initialTemplateSlug = root.getAttribute('data-template-slug') || 'default';
  const initialTemplateName = root.getAttribute('data-template-name') || 'Default';

  const getCsrfToken = () => {
    const fromMeta = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (fromMeta) {
      return fromMeta;
    }

    const cookie = document.cookie
      .split('; ')
      .find((row) => row.startsWith('XSRF-TOKEN='));

    return cookie ? decodeURIComponent(cookie.split('=')[1] || '') : '';
  };

  const buildFallbackPreviewHtml = (message, slug) => `<!doctype html><html><body style="font-family:Inter,Arial,sans-serif;padding:16px;color:#334155;">${message} Template: <strong>${slug || initialTemplateSlug}</strong></body></html>`;

  const normalizeResponseData = async (response) => {
    const contentType = response.headers.get('content-type') || '';

    if (contentType.includes('application/json')) {
      return response.json();
    }

    const text = await response.text();

    if (!text) {
      return null;
    }

    try {
      return JSON.parse(text);
    } catch {
      return text;
    }
  };

  const requestWithFetch = async (method, url, data = null, config = {}) => {
    const headers = {
      'X-Requested-With': 'XMLHttpRequest',
      ...(config.headers || {}),
    };

    const csrfToken = getCsrfToken();
    if (csrfToken) {
      headers['X-CSRF-TOKEN'] = csrfToken;
    }

    const requestInit = {
      method,
      credentials: 'same-origin',
      headers,
    };

    if (data instanceof FormData) {
      requestInit.body = data;
      delete requestInit.headers['Content-Type'];
    } else if (data !== null && data !== undefined) {
      if (!requestInit.headers['Content-Type']) {
        requestInit.headers['Content-Type'] = 'application/json';
      }
      requestInit.body = JSON.stringify(data);
    }

    const response = await fetch(url, requestInit);
    const responseData = await normalizeResponseData(response);

    if (!response.ok) {
      const error = new Error(`Request failed with status ${response.status}`);
      error.response = {
        status: response.status,
        data: responseData,
      };

      throw error;
    }

    return {
      status: response.status,
      data: responseData,
    };
  };

  const httpClient = {
    get(url, config = {}) {
      if (window.axios) {
        return window.axios.get(url, config);
      }

      return requestWithFetch('GET', url, null, config);
    },
    post(url, data = null, config = {}) {
      if (window.axios) {
        return window.axios.post(url, data, config);
      }

      return requestWithFetch('POST', url, data, config);
    },
    put(url, data = null, config = {}) {
      if (window.axios) {
        return window.axios.put(url, data, config);
      }

      return requestWithFetch('PUT', url, data, config);
    },
    patch(url, data = null, config = {}) {
      if (window.axios) {
        return window.axios.patch(url, data, config);
      }

      return requestWithFetch('PATCH', url, data, config);
    },
  };

  createApp({
    components: {
      PersonalStep,
      ExperienceStep,
      EducationStep,
      SkillsStep,
      ReviewStep,
    },
    data() {
      return {
        steps: [
          { key: 'personal', label: 'Personal' },
          { key: 'experiences', label: 'Experience' },
          { key: 'educations', label: 'Education' },
          { key: 'skills', label: 'Skills' },
          { key: 'review', label: 'Review' },
        ],
        currentStep: 1,
        isSaving: false,
        errors: {},
        toast: '',
        autosaveTimer: null,
        photoObjectUrl: null,
        lastSavedAt: '',
        previewHtml: buildFallbackPreviewHtml('Menyiapkan preview template.', initialTemplateSlug),
        previewResolvedSlug: initialTemplateSlug,
        previewLoading: false,
        previewLoadingTimer: null,
        previewHasRendered: false,
        previewRenderTimer: null,
        previewRequestId: 0,
        saveRequestId: 0,
        form: {
          personal: {
            title: '',
            personal_name: '',
            personal_email: '',
            summary: '',
            accent_color: '#7C3AED',
            photo_url: null,
            photoFile: null,
            remove_photo: false,
          },
          experiences: [],
          educations: [],
          skills: [],
          review: {
            status: 'draft',
          },
        },
        state: {
          cv: {
            id: Number(cvId) || null,
            title: '',
            template_slug: initialTemplateSlug,
            template_name: initialTemplateName,
            status: 'draft',
            public_url: '',
          },
        },
        showTemplateModal: false,
        availableTemplates: [],
        currentTemplatSlug: initialTemplateSlug,
      };
    },
    computed: {
      previewData() {
        return {
          id: this.state.cv.id,
          templateSlug: this.state.cv.template_slug || 'default',
          templateName: this.state.cv.template_name || '-',
          status: this.form.review.status,
          title: this.form.personal.title,
          personal_name: this.form.personal.personal_name,
          personal_email: this.form.personal.personal_email,
          summary: this.form.personal.summary,
          accent_color: this.form.personal.accent_color || '#7C3AED',
          photo_url: this.form.personal.photo_url,
          experiences: this.form.experiences,
          educations: this.form.educations,
          skills: this.form.skills,
        };
      },
      counts() {
        return {
          experiences: this.form.experiences.length,
          educations: this.form.educations.length,
          skills: this.form.skills.length,
        };
      },
      templateSupportsPhoto() {
        return this.state.cv.template_supports_photo || false;
      },
      templateSupportsAccent() {
        return this.state.cv.template_supports_accent || false;
      },
    },
    mounted() {
      this.schedulePreviewRender();
      this.fetchState();
      this.fetchTemplates();
    },
    methods: {
      normalizeStep(stepNumber) {
        const maxStep = this.steps.length;
        const nextStep = Number(stepNumber) || 1;
        return Math.min(Math.max(nextStep, 1), maxStep);
      },
      async persistCurrentStepBeforeNavigate() {
        if (this.currentStep >= 5 || this.isSaving) {
          return;
        }

        const ok = await this.saveStep(this.currentStep, true);

        if (!ok) {
          this.showToast('Step moved, but some fields are not saved yet.');
        }
      },
      async fetchState() {
        try {
          const response = await httpClient.get(`${baseUrl}/state`);
          this.applyState(response.data);
        } catch (error) {
          const message = error.response?.data?.message || 'Gagal memuat data CV. Preview tetap bisa digunakan.';
          this.showToast(message);
          this.schedulePreviewRender();
        }
      },
      async fetchTemplates() {
        try {
          const response = await httpClient.get(`${baseUrl}/templates`);
          this.availableTemplates = response.data?.templates || [];
        } catch (error) {
          console.warn('Failed to fetch templates:', error);
          this.availableTemplates = [];
        }
      },
      async selectNewTemplate(templateSlug) {
        if (this.isSaving || templateSlug === this.currentTemplatSlug) {
          return;
        }

        this.isSaving = true;

        try {
          const response = await httpClient.patch(`${baseUrl}/switch-template`, {
            template_slug: templateSlug,
          });

          if (response?.data) {
            this.applyState(response.data, {
              triggerPreviewRender: true,
              preservePersonal: false,
            });
            this.currentTemplatSlug = templateSlug;
            this.showTemplateModal = false;
            this.showToast('Template changed successfully');
          }
        } catch (error) {
          const backendMessage = error.response?.data?.message || '';
          this.showToast(backendMessage || 'Failed to switch template');
        } finally {
          this.isSaving = false;
        }
      },
      buildPreviewPayload() {
        return {
          title: this.form.personal.title || '',
          personal_name: this.form.personal.personal_name || '',
          personal_email: this.form.personal.personal_email || '',
          summary: this.form.personal.summary || '',
          accent_color: this.form.personal.accent_color || '#7C3AED',
          photo_url: this.form.personal.photo_url || null,
          experiences: this.form.experiences,
          educations: this.form.educations,
          skills: this.form.skills,
        };
      },
      schedulePreviewRender() {
        clearTimeout(this.previewRenderTimer);
        this.previewRenderTimer = setTimeout(() => {
          this.renderTemplatePreview();
        }, 220);
      },
      async renderTemplatePreview() {
        const requestId = ++this.previewRequestId;

        clearTimeout(this.previewLoadingTimer);
        const spinnerDelay = this.previewHasRendered ? 260 : 0;
        this.previewLoadingTimer = setTimeout(() => {
          if (requestId === this.previewRequestId) {
            this.previewLoading = true;
          }
        }, spinnerDelay);

        try {
          const response = await httpClient.post(`${baseUrl}/preview/live`, this.buildPreviewPayload());

          if (requestId !== this.previewRequestId) {
            return;
          }

          const nextHtml = response?.data?.html || '';
          if (nextHtml && nextHtml !== this.previewHtml) {
            this.previewHtml = nextHtml;
          }

          this.previewResolvedSlug = response?.data?.resolved_slug || response?.data?.template_slug || this.previewData.templateSlug || 'default';

          if (!this.previewHtml) {
            this.previewHtml = buildFallbackPreviewHtml('Preview kosong.', this.previewResolvedSlug);
          }

          this.previewHasRendered = true;
        } catch {
          if (requestId !== this.previewRequestId) {
            return;
          }

          this.previewResolvedSlug = this.previewData.templateSlug || this.state.cv.template_slug || 'default';
          this.previewHtml = buildFallbackPreviewHtml('Gagal memuat preview template.', this.previewResolvedSlug);
        } finally {
          if (requestId === this.previewRequestId) {
            clearTimeout(this.previewLoadingTimer);
            this.previewLoading = false;
          }
        }
      },
      applyState(payload, options = {}) {
        const triggerPreviewRender = options.triggerPreviewRender !== false;
        const preservePersonal = options.preservePersonal === true;
        const cvPayload = payload?.cv || {};

        this.state.cv = {
          ...this.state.cv,
          ...cvPayload,
        };

        // Update current template slug from state
        if (cvPayload.template_slug) {
          this.currentTemplatSlug = cvPayload.template_slug;
        }

        if (this.photoObjectUrl && !preservePersonal) {
          URL.revokeObjectURL(this.photoObjectUrl);
          this.photoObjectUrl = null;
        }

        if (!preservePersonal) {
          this.form.personal = {
            title: cvPayload.title || '',
            personal_name: cvPayload.personal_name || '',
            personal_email: cvPayload.personal_email || '',
            summary: cvPayload.summary || '',
            accent_color: cvPayload.accent_color || '#7C3AED',
            photo_url: cvPayload.photo_url || null,
            photoFile: null,
            remove_photo: false,
          };
        }

        this.form.experiences = Array.isArray(payload?.experiences) ? payload.experiences : [];
        this.form.educations = Array.isArray(payload?.educations) ? payload.educations : [];
        this.form.skills = Array.isArray(payload?.skills) ? payload.skills : [];
        this.form.review.status = cvPayload.status || 'draft';

        if (triggerPreviewRender) {
          this.schedulePreviewRender();
        }
      },
      stepClass(stepNumber) {
        if (stepNumber < this.currentStep) return 'is-done';
        if (stepNumber === this.currentStep) return 'is-active';
        return '';
      },
      async goToStep(stepNumber) {
        const targetStep = this.normalizeStep(stepNumber);

        if (targetStep === this.currentStep) {
          return;
        }

        await this.persistCurrentStepBeforeNavigate();
        this.currentStep = targetStep;
      },
      async nextStep() {
        if (this.currentStep < 5) {
          await this.persistCurrentStepBeforeNavigate();
          this.currentStep += 1;
        }
      },
      prevStep() {
        if (this.currentStep > 1) {
          this.currentStep -= 1;
        }
      },
      validateStep(step) {
        const nextErrors = {};

        if (step === 1) {
          if (!this.form.personal.title.trim()) nextErrors.title = 'Headline is required.';
          if (!this.form.personal.personal_name.trim()) nextErrors.personal_name = 'Name is required.';
          if (!this.form.personal.personal_email.trim()) nextErrors.personal_email = 'Email is required.';
        }

        if (step === 2) {
          this.form.experiences.forEach((item, index) => {
            if (!item.position?.trim()) nextErrors[`experiences.${index}.position`] = 'Position is required.';
            if (!item.company?.trim()) nextErrors[`experiences.${index}.company`] = 'Company is required.';
            if (!item.start_date) nextErrors[`experiences.${index}.start_date`] = 'Start date is required.';
          });
        }

        if (step === 3) {
          this.form.educations.forEach((item, index) => {
            if (!item.school?.trim()) nextErrors[`educations.${index}.school`] = 'School is required.';
            if (!item.degree?.trim()) nextErrors[`educations.${index}.degree`] = 'Degree is required.';
            if (!item.year?.trim()) nextErrors[`educations.${index}.year`] = 'Year is required.';
          });
        }

        if (step === 4) {
          this.form.skills.forEach((item, index) => {
            if (!item.name?.trim()) nextErrors[`skills.${index}.name`] = 'Skill is required.';
          });
        }

        this.errors = nextErrors;

        return Object.keys(nextErrors).length === 0;
      },
      scheduleAutosave() {
        if (this.currentStep >= 5) return;

        clearTimeout(this.autosaveTimer);
        this.autosaveTimer = setTimeout(() => {
          if (this.isSaving) {
            this.scheduleAutosave();
            return;
          }

          this.saveStep(this.currentStep, true);
        }, 700);
      },
      async saveStep(step, silent = false) {
        const requestId = ++this.saveRequestId;
        this.isSaving = true;
        this.errors = {};

        try {
          let response;

          if (step === 1) {
            const fd = new FormData();
            fd.append('title', this.form.personal.title || 'Untitled CV');
            fd.append('personal_name', this.form.personal.personal_name || '');
            fd.append('personal_email', this.form.personal.personal_email || '');
            fd.append('summary', this.form.personal.summary || '');
            fd.append('accent_color', this.form.personal.accent_color || '#7C3AED');
            fd.append('remove_photo', this.form.personal.remove_photo ? '1' : '0');
            if (this.form.personal.photoFile) {
              fd.append('photo', this.form.personal.photoFile);
            }

            response = await httpClient.post(`${baseUrl}/personal?_method=PUT`, fd, {
              headers: { 'Content-Type': 'multipart/form-data' },
            });
          }

          if (step === 2) {
            response = await httpClient.put(`${baseUrl}/experiences`, {
              experiences: this.form.experiences,
            });
          }

          if (step === 3) {
            response = await httpClient.put(`${baseUrl}/educations`, {
              educations: this.form.educations,
            });
          }

          if (step === 4) {
            response = await httpClient.put(`${baseUrl}/skills`, {
              skills: this.form.skills,
            });
          }

          if (step === 5) {
            response = await httpClient.put(`${baseUrl}/review`, {
              status: this.form.review.status,
            });
          }

          if (response?.data && requestId === this.saveRequestId) {
            this.applyState(response.data, {
              triggerPreviewRender: false,
              preservePersonal: step === 1,
            });
          }

          if (requestId === this.saveRequestId) {
            this.lastSavedAt = new Date().toLocaleTimeString();
          }

          if (!silent && requestId === this.saveRequestId) {
            this.showToast('Saved');
          }

          return true;
        } catch (error) {
          if (error.response?.status === 422) {
            const backendErrors = error.response.data.errors || {};
            const flatten = {};
            Object.keys(backendErrors).forEach((key) => {
              flatten[key] = Array.isArray(backendErrors[key]) ? backendErrors[key][0] : backendErrors[key];
            });
            this.errors = flatten;
          }

          if (!silent) {
            const backendMessage = error.response?.data?.message || '';
            const firstFieldError = Object.values(this.errors || {})[0] || '';
            this.showToast(backendMessage || firstFieldError || 'Failed to save. Check form fields.');
          }

          return false;
        } finally {
          if (requestId === this.saveRequestId) {
            this.isSaving = false;
          }
        }
      },
      updatePersonal(payload) {
        const next = { ...payload };

        if (next.photoFile instanceof File) {
          if (this.photoObjectUrl) {
            URL.revokeObjectURL(this.photoObjectUrl);
          }

          this.photoObjectUrl = URL.createObjectURL(next.photoFile);
          next.photo_url = this.photoObjectUrl;
          next.remove_photo = false;
        }

        this.form.personal = next;
        this.schedulePreviewRender();
        this.scheduleAutosave();
      },
      removePhoto() {
        if (this.photoObjectUrl) {
          URL.revokeObjectURL(this.photoObjectUrl);
          this.photoObjectUrl = null;
        }

        this.form.personal.photo_url = null;
        this.form.personal.photoFile = null;
        this.form.personal.remove_photo = true;
        this.schedulePreviewRender();
        this.scheduleAutosave();
      },
      updateExperiences(items) {
        this.form.experiences = items;
        this.schedulePreviewRender();
        this.scheduleAutosave();
      },
      updateEducations(items) {
        this.form.educations = items;
        this.schedulePreviewRender();
        this.scheduleAutosave();
      },
      updateSkills(items) {
        this.form.skills = items;
        this.schedulePreviewRender();
        this.scheduleAutosave();
      },
      redirectToCvListWithNotice(noticeKey) {
        const url = new URL('/cvs', window.location.origin);
        url.searchParams.set('wizard_notice', noticeKey);
        window.location.assign(url.toString());
      },
      async autoCopyText(text) {
        if (!text) return false;

        if (navigator.clipboard?.writeText) {
          try {
            await navigator.clipboard.writeText(text);
            return true;
          } catch {
            // Continue with legacy fallback.
          }
        }

        try {
          const input = document.createElement('textarea');
          input.value = text;
          input.setAttribute('readonly', '');
          input.style.position = 'fixed';
          input.style.left = '-9999px';
          document.body.appendChild(input);
          input.select();
          input.setSelectionRange(0, input.value.length);
          const copied = document.execCommand('copy');
          document.body.removeChild(input);
          return copied;
        } catch {
          return false;
        }
      },
      async saveDraft() {
        this.form.review.status = 'draft';
        const ok = await this.saveStep(5, true);
        if (!ok) {
          const firstFieldError = Object.values(this.errors || {})[0] || 'Gagal menyimpan draft.';
          this.showToast(firstFieldError);
          return;
        }

        this.showToast('Draft berhasil disimpan');
        window.setTimeout(() => {
          this.redirectToCvListWithNotice('draft_saved');
        }, 350);
      },
      async publishCv() {
        this.form.review.status = 'published';
        const ok = await this.saveStep(5, true);
        if (!ok) {
          const firstFieldError = Object.values(this.errors || {})[0] || 'Gagal publish CV.';
          this.showToast(firstFieldError);
          return;
        }

        const url = this.state.cv.public_url;
        let copied = false;
        
        if (url) {
          copied = await this.autoCopyText(url);
        }
        
        this.showToast(copied ? 'link copied!' : 'CV berhasil dipublish');

        window.setTimeout(() => {
          this.redirectToCvListWithNotice(copied ? 'link_copied' : 'published');
        }, 350);
      },
      downloadPdf() {
        window.open(`${baseUrl}/pdf`, '_blank');
      },
      showToast(message) {
        this.toast = message;
        window.setTimeout(() => {
          this.toast = '';
        }, 1800);
      },
    },
  }).mount('#cvWizardRoot');
}
