# TechCloud — custom WordPress website

A bespoke WordPress theme for **TechCloud Singapore**, using the native WordPress editor. No Elementor, other page builders, or paid theme dependencies.

## Pages and editing

- **Home:** edit its title, excerpt, and native blocks in Pages. Sections use theme styling.
- **Services:** overview plus five individual child pages. Edit service title, excerpt, content, featured image, and page order; the Home and Services indexes update automatically.
- **Contact:** editable page copy and a native enquiry form; email, phone and address under Appearance → Customize → TechCloud business details.
- **Case studies:** a dedicated post type provided by the small first-party `TechCloud Content` plugin; supports editor content, excerpts, featured images, and revisions. Content stays portable when changing themes.
- **Shop:** configure the destination under Appearance → Customize → TechCloud business details. Empty configuration opens the local Shop placeholder. Supports `/shop/` or a full URL such as `https://shop.example.com`.
- **Navigation:** Appearance → Menus → Primary navigation. A built-in fallback supplies the initial links.

## Local WordPress

Requires Docker and Docker Compose (`docker-compose`). Uses an isolated database and localhost port **8129**.

```sh
./scripts/local-up.sh
./scripts/local-wp.sh post list --post_type=page
```

Site: http://localhost:8129 · CMS: http://localhost:8129/wp-admin/

Local admin username: `techcloud-admin`. A random password is generated into ignored `.env` on first setup. Do not commit that file. Compose database passwords are local-only development values; database ports are not exposed.

## Shareable review preview

`preview/` contains a static export from the actual local WordPress rendering, published separately using GitHub Pages. This is a design preview, not a WordPress CMS and not a replacement for techcloud.sg.

```sh
python3 scripts/export-preview.py
python3 scripts/check-preview.py
node scripts/check-motion.mjs
node scripts/check-services.mjs
```

The export includes a review label, noindex directives, self-hosted assets, and relative links that work under the repository subpath. Shop remains a clear placeholder until configured. The preview form validates fields and demonstrates its response without sending or saving an enquiry. Submission tokens and field names are removed from the export. Email and phone links remain available.

The header stays visible while scrolling. A floating arrow appears after the first screen and returns to the top; reduced-motion settings disable smooth scrolling. The shared export alone includes casual copy, text-selection, image-drag and right-click deterrents (`scripts/preview-guard.js`), with form-field editing exempt. These are browser-side deterrents, not DRM or access control.

## Premium visual edition

The design uses blue-grey neutrals, navy dark surfaces and the logo’s blue for actions, bold Manrope headings, medium-weight body text, a photographic infrastructure hero, a compact dark service selector, an open workplace story, a contrasting case-study feature and a three-step process. `assets/rich.css` extends the original brand styles; the native editor imports both stylesheets. Photography and video retain their full colours against the neutral surfaces; the original logo keeps its brand colours.

Only the home hero plays a video: a self-hosted, silent infrastructure MP4 with visible camera movement, a WebP poster without a visible playback control. It downloads when the video area is visible and stops off-screen or in a hidden tab. Reduced-motion and data-saver preferences show the poster instead of autoplay. All five services appear in a bold vertical selector beside one large colour still and a short summary. On mobile the image follows the selector. Click or use the arrow, Home and End keys to change services; Explore service opens the detail page. Without JavaScript, the selector links open the service pages directly. Detailed capabilities remain on each service page. Set each service page’s featured image to replace its illustrative fallback. All three original clips remain in the repository for future use.

The business story pairs its headline with workplace photography, with three benefits below. Home shows two compact case cards with a link to all four examples. The archive uses an even two-column grid with consistent image proportions, navy text panels and visible illustrative disclosures. It stacks on phones. Each example has its own proposed scenario, scope, handover considerations and related service links. Case order is editable through the native Order field. These sections remain native WordPress blocks or theme-rendered case-study content.

Phone layouts use fluid heading sizes, 16px body text in service summaries, benefits, case studies and process steps, tighter section spacing, and larger text-link tap areas. The mobile menu scrolls within short landscape viewports. The case-study introduction keeps its heading and description together in a left-aligned stack at every width.

The mobile hero places its copy and actions over the video in one shorter section, with a dark overlay. Service changes use a short crossfade with reserved panel height, so the page stays in place. Inactive panels cannot receive focus. Nearby service images preload once unless data saver is enabled. Controls have short colour transitions, and reduced-motion preferences disable animation. Contact details are sticky beside the form only above 1100px and with at least 600px viewport height; smaller screens retain normal scrolling.

All five service detail pages include a colour image, explained capabilities, a preparation checklist, two relevant FAQs and links to related services. The overview adds a practical service guide. `scripts/refine-services-cases.php` installs this content once locally and preserves previous content in `_tc_before_expanded_content`; `local-up.sh` includes it for fresh setups. Later CMS edits are preserved.

The revised hero is 1280×720, 24fps, approximately 5.54 seconds and 1.64 MB, with a half-second dissolve across the loop boundary. Rebuild it from a downloaded source with FFmpeg, ffprobe and Python Pillow installed:

```sh
python3 scripts/prepare-hero-video.py path/to/source.mp4
python3 scripts/export-preview.py
```

Higgsfield generation totals **31.5 of the authorized 40 credits**, including the 9-credit hero replacement. No paid retries were made; 8.5 credits remain in the project allowance.

`scripts/refine-v2.php` applies the native-block content refinement once on local installations, retaining each original page in `_tc_content_before_v2`. `local-up.sh` runs it after the initial seed. Subsequent runs preserve CMS edits.

## Policy pages and footer credit

The footer includes Terms & Conditions, Privacy Policy, Cookies and a linked “Designed by AdCendes” credit. The old footer text link to the top is removed; the floating arrow remains. The three policies are editable native pages created by `scripts/add-policy-pages.php`, included in local setup. They are clearly labelled drafts for review, describe the shared preview accurately, and must be confirmed or replaced for production. Cookies is an information page, not a cookie-consent manager.

## Contact enquiries and footer

The blue footer includes enquiry and call buttons, email, the configured address, a Google map and directions link. The Contact page uses `[techcloud_contact]` and `[techcloud_contact_form]` in native shortcode blocks.

On WordPress, valid enquiries are stored privately under **Enquiries**, accessible only to administrators. The first-party plugin validates fields, checks a nonce and honeypot, limits repeated submissions and prevents duplicate notifications. It attempts an email notification to the business email in the Customizer; a failed notification does not discard the record. Inbox delivery has not been verified on hosting.

Local integration checks intercept mail and remove test records:

```sh
./scripts/local-wp.sh eval-file /scripts/check-contact.php
```

`restore-v3.php` reverses the superseded editorial revision using saved content; `refine-contact.php` adds the form. Both run once locally via `local-up.sh` and retain backups. The earlier homepage design is restored; the new styles are scoped to the footer and Contact form.

## Production boundary

**No production access or deployment has been performed.** Install `theme/techcloud` as a theme and `plugins/techcloud-content` as a plugin on the agreed WordPress staging environment first. `scripts/seed.php` is deliberately localhost-only and will not overwrite existing seeded content.

Before launch: approve copy and visual direction, confirm business details/shop destination, replace or remove the four clearly labelled illustrative case studies, migrate content, test editing on the target WordPress instance, configure SEO/indexing for production, exclude Contact from full-page caching, and configure/test mail delivery and enquiry retention on the target hosting. No testimonials, certifications, service levels, client results, or response-time guarantees have been invented.

Reference content and asset provenance: [docs/content-sources.md](docs/content-sources.md).

## Three-day review expiry

`preview-policy.json` fixes the preview deadline at **2 November 2026, 11:30:14 am Singapore/Malaysia time** (`2026-11-02T03:30:14Z`). It does not reset on visits or exports. The inline guard checks first load, open tabs, focus, page restore and visibility changes. After expiry it replaces the site with an AdCendes contact screen.

Pages builds go through `scripts/build-pages.py`. At and after the deadline, the published artifact contains only expiry pages for every route and the 404, with no original images, video, fonts or scripts. The GitHub schedule checks every 15 minutes and skips deployment before the deadline. Scheduled runs can be delayed by GitHub; the browser guard provides the immediate display limit using the visitor's clock.

This is a review-window restriction, not authentication or DRM. The public repository/history and copies already downloaded remain accessible. Local WordPress is unaffected. To extend, edit the policy, re-export, verify and push. Checks: `python3 scripts/check-expiry.py`, `node scripts/check-expiry.mjs`, `python3 scripts/check-preview.py`.

## Client brief content

The five client drafts are stored as structured website copy in `scripts/client-brief-content.json`. `extract-client-brief.py` can recreate that file from the local source DOCX, which is ignored by Git. `apply-client-brief.php` is a local-only, one-time migration run after earlier migrations by `local-up.sh`; it preserves original titles, excerpts and content in `_tc_before_client_brief`. Do not reset its option after later CMS edits unless deliberately reapplying the client source.

The native page title, excerpt and blocks edit the H1, hero introduction and body. A TechCloud search/service-card meta box edits SEO titles, descriptions and compact selector text. Existing URLs are retained and brief links are mapped to them. Run `python3 scripts/check-client-brief.py` after export to check supplied copy coverage, SEO descriptions, FAQ counts and safe forms. See `docs/content-sources.md` for the exact mapping and missing source modules.
