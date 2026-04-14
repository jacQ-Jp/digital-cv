import { PersonalStep } from './components/personal-step.js';
import { ExperienceStep } from './components/experience-step.js';
import { EducationStep } from './components/education-step.js';
import { SkillsStep } from './components/skills-step.js';
import { ReviewStep } from './components/review-step.js';
import { CvLivePreview } from './components/cv-live-preview.js';

const { createApp } = window.Vue;

const root = document.getElementById('cvWizardRoot');

if (root) {
  const cvId = root.getAttribute('data-cv-id');
  const baseUrl = `/cvs/${cvId}/wizard`;

  createApp({
    components: {
      PersonalStep,
      ExperienceStep,
      EducationStep,
      SkillsStep,
      ReviewStep,
      CvLivePreview,
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
        form: {
          personal: {
            title: '',
            personal_name: '',
            personal_email: '',
            summary: '',
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
            id: null,
            title: '',
            template_name: '',
            status: 'draft',
            public_url: '',
          },
        },
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
    },
    mounted() {
      this.fetchState();
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
        const response = await window.axios.get(`${baseUrl}/state`);
        this.applyState(response.data);
      },
      applyState(payload) {
        this.state.cv = payload.cv;

        if (this.photoObjectUrl) {
          URL.revokeObjectURL(this.photoObjectUrl);
          this.photoObjectUrl = null;
        }

        this.form.personal = {
          title: payload.cv.title || '',
          personal_name: payload.cv.personal_name || '',
          personal_email: payload.cv.personal_email || '',
          summary: payload.cv.summary || '',
          photo_url: payload.cv.photo_url || null,
          photoFile: null,
          remove_photo: false,
        };

        this.form.experiences = payload.experiences || [];
        this.form.educations = payload.educations || [];
        this.form.skills = payload.skills || [];
        this.form.review.status = payload.cv.status || 'draft';
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
          this.saveStep(this.currentStep, true);
        }, 700);
      },
      async saveStep(step, silent = false) {
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
            fd.append('remove_photo', this.form.personal.remove_photo ? '1' : '0');
            if (this.form.personal.photoFile) {
              fd.append('photo', this.form.personal.photoFile);
            }

            response = await window.axios.post(`${baseUrl}/personal?_method=PUT`, fd, {
              headers: { 'Content-Type': 'multipart/form-data' },
            });
          }

          if (step === 2) {
            response = await window.axios.put(`${baseUrl}/experiences`, {
              experiences: this.form.experiences,
            });
          }

          if (step === 3) {
            response = await window.axios.put(`${baseUrl}/educations`, {
              educations: this.form.educations,
            });
          }

          if (step === 4) {
            response = await window.axios.put(`${baseUrl}/skills`, {
              skills: this.form.skills,
            });
          }

          if (step === 5) {
            response = await window.axios.put(`${baseUrl}/review`, {
              status: this.form.review.status,
            });
          }

          if (response?.data) {
            this.applyState(response.data);
          }

          this.lastSavedAt = new Date().toLocaleTimeString();

          if (!silent) {
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
            this.showToast('Failed to save. Check form fields.');
          }

          return false;
        } finally {
          this.isSaving = false;
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
        this.scheduleAutosave();
      },
      updateExperiences(items) {
        this.form.experiences = items;
        this.scheduleAutosave();
      },
      updateEducations(items) {
        this.form.educations = items;
        this.scheduleAutosave();
      },
      updateSkills(items) {
        this.form.skills = items;
        this.scheduleAutosave();
      },
      updateReviewStatus(status) {
        this.form.review.status = status;
      },
      async finishPublish() {
        const ok = await this.saveStep(5);
        if (!ok) return;

        this.showToast(this.form.review.status === 'published' ? 'CV published.' : 'Draft saved.');
      },
      async copyPublicLink() {
        const url = this.state.cv.public_url;
        if (!url) return;

        try {
          await navigator.clipboard.writeText(url);
          this.showToast('Link copied');
        } catch {
          window.prompt('Copy this link:', url);
        }
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
