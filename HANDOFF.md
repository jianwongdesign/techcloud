# TechCloud — first custom-theme build

Updated 29 October 2026.

## Current state

- Repository: https://github.com/jianwongdesign/techcloud — public, branch `main`.
- Review preview: https://jianwongdesign.github.io/techcloud/ — GitHub Pages, static export of the local WordPress rendering.
- Local WordPress: http://localhost:8129 — isolated Docker project `techcloud`.
- Local CMS: http://localhost:8129/wp-admin/ — user `techcloud-admin`, random password in ignored `.env`.
- **techcloud.sg is untouched.** No live-site credentials, backups, migrations, or production changes were involved.

## Agreed scope

Custom WordPress theme only; no Elementor or other page builder. Native blocks edit page content. Home; Services overview and five individual child pages; Contact; Case Studies archive/detail; configurable Shop button. User said the Shop may be `/shop/` or a separate shop subdomain, to be supplied later. Empty setting opens a deliberate placeholder page.

## Implementation

Theme: `theme/techcloud`. Tiny first-party content plugin: `plugins/techcloud-content` for portable case studies. Only this plugin is active locally. Core editor, excerpts, featured images and revisions are supported. Business details, banner copy, and Shop destination are in Appearance → Customize → TechCloud business details. Primary navigation is editable through Appearance → Menus.

Five services follow the existing public website: managed IT, end-user support, IT staffing, project management, installation/infrastructure. Logo is the existing asset. Indigo/pale-blue visual direction, self-hosted Manrope font, CSS/SVG technology illustration, responsive service lists, and native FAQ disclosures.

One case study is explicitly labelled **illustrative, not a client result**. Replace or remove it before production. Contact uses email and telephone links, not an unconnected submission form.

## Verification completed

- Theme activated and seeded successfully on local WordPress 7.0.2 / PHP 8.3.
- PHP syntax checks passed for theme, plugin, and seed.
- All 11 published review URLs returned HTTP 200.
- Export checker passed all 12 HTML documents including the 404: internal links, assets, anchors, one H1, alt attributes, noindex/review labels, and no leaked localhost/WordPress backend URLs.
- Browser reviewed the desktop layout, 820px tablet layout, and 390px mobile hero/service layout. Keyboard opened the mobile menu; Escape closes it in the script. No console messages observed in the local preview during review.
- Server-side checks confirmed native block content and page excerpts, case-study editor support, and both `/shop/` and absolute external Shop URL formats.
- Published homepage opened successfully in Chrome. Latest successful deployment at that check: https://github.com/jianwongdesign/techcloud/actions/runs/40851018407.
- `.env` is ignored and absent from tracked files.

## Remaining review and release work

Client review of visual direction and copy; approved case-study details; final shop destination and business details; native editor walkthrough on the target CMS; agreed WordPress staging deployment and content migration; final device/accessibility QA, production SEO/indexing, and launch authorization.

Local seed is guarded against non-local execution and repeat overwrites. README documents setup/export. When network sandboxing restricts Python, the exporter accepts `TC_FETCH_DIR` with cached pages/cases JSON and fetched HTML, allowing an offline export after approved curl downloads. No secrets are included in the exported preview.
