# TechCloud — custom WordPress website

A bespoke WordPress theme for **TechCloud Singapore**, using the native WordPress editor. No Elementor, other page builders, or paid theme dependencies.

## Pages and editing

- **Home:** edit its title, excerpt, and native blocks in Pages. Sections use theme styling.
- **Services:** overview plus five individual child pages. Edit service title, excerpt, content, featured image, and page order; the Home and Services indexes update automatically.
- **Contact:** editable page copy; email, phone, address, and global contact banner under Appearance → Customize → TechCloud business details.
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

The export includes a review label, noindex directives, self-hosted assets, and relative links that work under the repository subpath. Shop remains a clear placeholder until configured. Contact links open email/phone apps; no enquiry form backend has been introduced.

## Premium visual edition

The design uses warm-white surfaces, bold Manrope headings and a consistent two-column grid. The homepage has a light typographic hero with its video in a separate frame, an open service selector, a compact case-study entry, three practical questions and one contact banner. Repeated benefit/process sections, image overlays and decorative slogans have been removed. `assets/rich.css` extends the base styles and also loads in the native editor. Images, video and the original logo retain their colours.

Only the home hero plays a video: a self-hosted, silent infrastructure MP4 with visible camera movement, a WebP poster and pause/play control. It downloads when visible and stops off-screen or in a hidden tab. Reduced-motion and data-saver preferences show the poster until the visitor explicitly plays it. The five services use a vertical selector beside one colour still, with the description and detail link below the image. Click or use the arrow, Home and End keys to select; Service details opens the individual page. Without JavaScript, the selector links open the service pages directly. Featured images replace the illustrative defaults.

The homepage case study is a smaller image-and-text entry, with its illustrative disclosure visible. The main service image uses the network poster and the example project uses the office still, avoiding a repeat of the hero scene in adjacent sections. No new media was generated. Full capabilities remain on the service pages, and all revised page content remains editable through native WordPress blocks.

Phone layouts preserve 16px body copy, fluid headings, single-column media, generous tap areas and a menu that scrolls inside short landscape viewports. Desktop, 820px tablet, 390px phone and the long infrastructure title at 320px were reviewed in Chrome emulation. Physical device checks remain part of release QA.

The revised hero is 1280×720, 24fps, approximately 5.54 seconds and 1.64 MB, with a half-second dissolve across the loop boundary. Rebuild it from a downloaded source with FFmpeg, ffprobe and Python Pillow installed:

```sh
python3 scripts/prepare-hero-video.py path/to/source.mp4
python3 scripts/export-preview.py
```

Higgsfield generation totals **31.5 of the authorized 40 credits**, including the 9-credit hero replacement. No paid retries were made; 8.5 credits remain in the project allowance.

`scripts/refine-v2.php` and `scripts/refine-v3.php` apply the native-block refinements once on local installations. V2 keeps original content in `_tc_content_before_v2`; V3 saves the prior title, excerpt and content in `_tc_before_v3`. `local-up.sh` runs both after the initial seed. Subsequent runs preserve CMS edits.

## Production boundary

**No production access or deployment has been performed.** Install `theme/techcloud` as a theme and `plugins/techcloud-content` as a plugin on the agreed WordPress staging environment first. `scripts/seed.php` is deliberately localhost-only and will not overwrite existing seeded content.

Before launch: approve copy and visual direction, confirm business details/shop destination, replace or remove the clearly labelled illustrative case study, migrate content, test editing on the target WordPress instance, and configure SEO/indexing for production. No testimonials, certifications, service levels, client results, or response-time guarantees have been invented.

Reference content and asset provenance: [docs/content-sources.md](docs/content-sources.md).
