# TechCloud — richer custom-theme preview

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

Five services follow the existing public website: managed IT, end-user support, IT staffing, project management, installation/infrastructure. Logo is the existing asset. Indigo/pale-blue visual direction, self-hosted Manrope font, photographic infrastructure hero, detailed service cards, a workplace feature, larger case-study layout, native process blocks, and FAQ disclosures.

The richer edition adds three completed Higgsfield Kling 3.0 Turbo videos: managed IT, end-user support and networking. Total charged: **22.5 of the authorized 40 credits**; no paid retries. Website versions are silent 960×540 H.264 loops, approximately ten seconds, under 1 MB combined, with WebP posters. They load only when visible and respect reduced motion/data saver; manual pause persists across scrolling. See `docs/higgsfield-budget.json` and `docs/content-sources.md`.

`rich.css` extends the brand styles and also loads in the native editor. `scripts/refine-v2.php` is local-only and idempotent; the previous content is saved in `_tc_content_before_v2`. It has already run locally. `local-up.sh` applies both seed and refinement for fresh installations.

One case study is explicitly labelled **illustrative, not a client result**. Replace or remove it before production. Contact uses email and telephone links, not an unconnected submission form.

## Verification completed

- Theme activated and seeded successfully on local WordPress 7.0.2 / PHP 8.3.
- PHP syntax checks passed for theme, plugin, and seed.
- All 11 published review URLs returned HTTP 200.
- Export checker passed all 12 HTML documents including the 404: internal links, assets, anchors, one H1, alt attributes, noindex/review labels, and no leaked localhost/WordPress backend URLs.
- Richer layout reviewed in Chrome at desktop, 820px tablet and 390px mobile. Mobile navigation opens; service detail text wraps correctly. Video playback and pause controls verified in-browser; no console messages observed during review.
- `node scripts/check-motion.mjs` passes lazy loading, pause persistence, off-screen/tab visibility, reduced-motion, data-saver and explicit-play checks. Media inspection confirms silent H.264 video. The design detector reported no deterministic issues in the revised components.
- Server-side checks confirmed native block content and page excerpts, case-study editor support, and both `/shop/` and absolute external Shop URL formats.
- Richer edition commit `6a07cd9` is published: https://github.com/jianwongdesign/techcloud/actions/runs/40857394947 completed successfully. All 11 public pages, all three MP4s, the revised stylesheet and JavaScript returned HTTP 200 and byte-matched the committed export. Published hero and service-video playback also verified in Chrome.
- `.env` is ignored and absent from tracked files.

## Remaining review and release work

Client review of visual direction and copy; approved case-study details; final shop destination and business details; native editor walkthrough on the target CMS; agreed WordPress staging deployment and content migration; final device/accessibility QA, production SEO/indexing, and launch authorization.

Local seed is guarded against non-local execution and repeat overwrites. README documents setup/export. When network sandboxing restricts Python, the exporter accepts `TC_FETCH_DIR` with cached pages/cases JSON and fetched HTML, allowing an offline export after approved curl downloads. No secrets are included in the exported preview.
