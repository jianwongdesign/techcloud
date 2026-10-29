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

## Premium visual edition

The design uses blue-grey neutrals, navy dark surfaces and the logo’s blue for actions, bold Manrope headings, medium-weight body text, a photographic infrastructure hero, a compact dark service selector, an open workplace story, a contrasting case-study feature and a three-step process. `assets/rich.css` extends the original brand styles; the native editor imports both stylesheets. Photography and video retain their full colours against the neutral surfaces; the original logo keeps its brand colours.

Only the home hero plays a video: a self-hosted, silent infrastructure MP4 with visible camera movement, a WebP poster and pause/play control. It downloads when the video area is visible and stops off-screen or in a hidden tab. Reduced-motion and data-saver preferences show the poster until the visitor explicitly plays it. All five services appear in a bold vertical selector beside one large colour still and a short summary. On mobile the image follows the selector. Click or use the arrow, Home and End keys to change services; Explore service opens the detail page. Without JavaScript, the selector links open the service pages directly. Detailed capabilities remain on each service page. Set each service page’s featured image to replace its illustrative fallback. All three original clips remain in the repository for future use.

The business story pairs its headline with workplace photography, with three benefits below. The case-study feature uses a different network image and a blue-grey text panel. Its illustrative disclosure remains visible. Process steps use stronger headings and individual top rules. These sections remain native WordPress blocks or theme-rendered case-study content.

Phone layouts use fluid heading sizes, 16px body text in service summaries, benefits, case studies and process steps, tighter section spacing, and larger text-link tap areas. The mobile menu scrolls within short landscape viewports. The case-study introduction keeps its heading and description together in a left-aligned stack at every width.

The revised hero is 1280×720, 24fps, approximately 5.54 seconds and 1.64 MB, with a half-second dissolve across the loop boundary. Rebuild it from a downloaded source with FFmpeg, ffprobe and Python Pillow installed:

```sh
python3 scripts/prepare-hero-video.py path/to/source.mp4
python3 scripts/export-preview.py
```

Higgsfield generation totals **31.5 of the authorized 40 credits**, including the 9-credit hero replacement. No paid retries were made; 8.5 credits remain in the project allowance.

`scripts/refine-v2.php` applies the native-block content refinement once on local installations, retaining each original page in `_tc_content_before_v2`. `local-up.sh` runs it after the initial seed. Subsequent runs preserve CMS edits.

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

Before launch: approve copy and visual direction, confirm business details/shop destination, replace or remove the clearly labelled illustrative case study, migrate content, test editing on the target WordPress instance, configure SEO/indexing for production, exclude Contact from full-page caching, and configure/test mail delivery and enquiry retention on the target hosting. No testimonials, certifications, service levels, client results, or response-time guarantees have been invented.

Reference content and asset provenance: [docs/content-sources.md](docs/content-sources.md).
