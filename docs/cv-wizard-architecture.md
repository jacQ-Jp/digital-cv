# CV Wizard Architecture

## Goals
- Multi-step CV flow (Personal -> Experience -> Education -> Skills -> Review & Publish)
- Split layout: form on the left, live preview on the right
- Autosave + no full page reload navigation
- Public link copy + PDF download
- Final output identical to selected template

## Folder Structure
- app/Http/Controllers/Cv/CvWizardController.php
  - JSON autosave endpoints per step
  - Review publish endpoint
  - Template preview endpoint
  - PDF generation endpoint
- app/Support/CvTemplateRenderer.php
  - Reusable template resolver/renderer
- resources/views/cvs/wizard.blade.php
  - Split-view shell + Vue mount point
- public/js/cv-wizard/app.js
  - Vue root state, autosave, step navigation, live preview refresh
- public/js/cv-wizard/components/personal-step.js
- public/js/cv-wizard/components/experience-step.js
- public/js/cv-wizard/components/education-step.js
- public/js/cv-wizard/components/skills-step.js
- public/js/cv-wizard/components/review-step.js
- resources/views/cv/templates/minimalist.blade.php
- resources/views/cv/templates/modern.blade.php
- resources/views/cv/templates/creative.blade.php

## Data Flow
1. User chooses template at cv-builder/templates.
2. System creates draft CV and redirects to cvs/{cv}/wizard.
3. Frontend fetches wizard state from GET /cvs/{cv}/wizard/state.
4. Every step change triggers debounced autosave to dedicated endpoint.
5. On save success, right preview iframe is refreshed (same template renderer).
6. Review step can set draft/published, copy link, and download PDF.

## Live Preview Binding
- Left form edits update Vue state immediately.
- Autosave posts state to backend.
- Backend persists and returns canonical data.
- Right iframe loads /cvs/{cv}/wizard/preview?ts=<timestamp>.
- Because preview uses CvTemplateRenderer, the preview is the same structure used for final/public.

## Public Link
- Route: /p/{token}
- Token: public_uuid (UUID), fallback to numeric id for backward compatibility
- Copy action uses Clipboard API with fallback prompt.

## PDF Export
- Endpoint: GET /cvs/{cv}/wizard/pdf
- Renderer: Spatie Browsershot (Chromium)
- HTML source: the same resolved Blade template used by preview/public render

## Notes
- Run migration before usage: php artisan migrate
- Ensure storage symlink for photo: php artisan storage:link
- Browsershot requires Node + Chromium runtime available on server
