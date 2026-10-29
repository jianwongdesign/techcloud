# TechCloud — premium custom-theme preview

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

Five services follow the existing public website: managed IT, end-user support, IT staffing, project management, installation/infrastructure. Logo is the existing asset. The latest visual direction uses charcoal and warm-white surfaces, self-hosted Manrope with 720–750 heading weights and 500 body weight, an infrastructure video hero, a compact service grid, a workplace feature, larger case-study layout, native process blocks, and FAQ disclosures. Following user feedback, photos and videos retain their full colours. The charcoal/warm-white layout and stronger typography remain. Following feedback that the rows were too long, Home and Services now use a compact grid: five service tiles plus a contact tile, arranged in three columns/two rows on desktop, two columns on tablet and one column on mobile. Each service shows a small 16:9 colour thumbnail, title, excerpt and arrow. Capability lists and duplicate View service labels were removed from the overview; full capabilities remain on the detail pages. The section spacing is also reduced. Service featured images override the existing illustrative fallback assets.

The original three Higgsfield Kling 3.0 Turbo clips cost 22.5 credits. A stronger hero replacement adds 9 credits, bringing the charged total to **31.5 of the authorized 40 credits**, with **8.5 remaining**; no paid retries. The hero now uses `hero-motion.mp4`: a visible camera move through server infrastructure, silent 1280×720 H.264, 5.54 seconds, 1.64 MB, with a half-second loop dissolve and matching WebP poster. Only this video plays; service thumbnails remain colour stills. The hero loads when its video area is visible and respects reduced motion/data saver; manual pause persists across scrolling. The three original clips remain in the repository. See `docs/higgsfield-budget.json`, `docs/content-sources.md`, and `scripts/prepare-hero-video.py`.

`rich.css` extends the brand styles and also loads in the native editor. `scripts/refine-v2.php` is local-only and idempotent; the previous content is saved in `_tc_content_before_v2`. It has already run locally. `local-up.sh` applies both seed and refinement for fresh installations.

One case study is explicitly labelled **illustrative, not a client result**. Replace or remove it before production. Contact uses email and telephone links, not an unconnected submission form.

## Verification completed

- Compact services revision: desktop, 820px tablet, 390px mobile and 320px narrow mobile were reviewed in Chrome. All five services and the contact link are present, thumbnail sizes match, and the long infrastructure title fits. Local PHP, export and layout checks pass. Final spacing confirmation/public deployment pending. No generation credits spent.

- Services redesign: reviewed in Chrome at desktop, 820px tablet and 390px mobile, including the long infrastructure title. All five services retain their detail links, descriptions and three capabilities. The layout detector returned no findings; PHP syntax, export checks and diff whitespace checks pass. No media was generated and the credit ledger is unchanged.
- Previous preview revision `88690da` is published: https://github.com/jianwongdesign/techcloud/actions/runs/40902934549 succeeded. All 11 public pages and the revised stylesheet returned HTTP 200 and matched the committed export. The published homepage service rows were visually confirmed, and clicking Managed IT opened its correct detail page. Production remains untouched.

- Stronger-hero and card-proportion revision: local PHP syntax, all 12 exported documents, motion behavior checks and `git diff --check` pass. The final layout detector returned no findings. The rendered hero plays with visible camera movement and readable text. Card layouts were reviewed at desktop, 820px tablet and mobile; the final tablet confirmation shows matching photo proportions without the infrastructure card's previous stretched image/blank space.
- Revision `62832fe` is published: https://github.com/jianwongdesign/techcloud/actions/runs/40900342471 succeeded. All 11 public pages, the revised CSS, new hero MP4 and poster returned HTTP 200 and byte-matched the committed export. Chrome confirmed playback on the published hero and the centred infrastructure card with a consistent image crop on desktop. This was the preceding review preview; production remains untouched.
- Current colour-media correction: PHP syntax, motion behavior checks and all 12 exported documents pass. Export checks now enforce exactly one video on the homepage and none on the other pages. Desktop, 820px tablet and 390px mobile hero placement verified in Chrome, along with hero playback, pause/resume and full-colour service stills.
- Colour-media commit `f4a7d9c` is published: https://github.com/jianwongdesign/techcloud/actions/runs/40896755325 succeeded. All 11 public pages, the revised CSS/JS and hero MP4 returned HTTP 200 and byte-matched the export. Published Chrome state confirms hero playback and still images for all three illustrated services. The user explicitly confirmed video only in the hero and still service images.

- Type detector returned no findings. Checked text/surface colour pairs exceed 4.5:1; the focus-ring colour against the main surface exceeds 3:1.
- Monochrome typography reviewed in Chrome at desktop, 820px tablet and 390px mobile, including the long infrastructure service title. Bold headings, 16px section body copy, warmer panels and monochrome media render without observed overlap or clipping.
- Monochrome edition commit `1f0b1ca` is published: https://github.com/jianwongdesign/techcloud/actions/runs/40895407895 completed successfully. All 11 public pages and the revised stylesheet returned HTTP 200 and byte-matched the export after deployment; the published desktop hero was also verified in Chrome.
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
