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
```

The export includes a review label, noindex directives, self-hosted assets, and relative links that work under the repository subpath. Shop remains a clear placeholder until configured. Contact links open email/phone apps; no enquiry form backend has been introduced.

## Premium visual edition

The design uses charcoal and warm-white surfaces, bold Manrope headings, medium-weight body text, a photographic infrastructure hero, a compact service grid, workplace imagery, a larger case-study feature and a three-step process. `assets/rich.css` extends the original brand styles; the native editor imports both stylesheets. Photography and video retain their full colours against the neutral surfaces; the original logo keeps its brand colours.

Only the home hero plays a video: a self-hosted, silent infrastructure MP4 with visible camera movement, a WebP poster and pause/play control. It downloads when the video area is visible and stops off-screen or in a hidden tab. Reduced-motion and data-saver preferences show the poster until the visitor explicitly plays it. All five services use compact image-and-text tiles, with a contact tile completing the grid: three columns on desktop, two on tablet and one on mobile. The overview shows the title and excerpt; detailed capabilities remain on each service page. Thumbnails retain a consistent 16:9 crop. Set each service page’s featured image to replace its illustrative fallback. All three original clips remain in the repository for future use.

The revised hero is 1280×720, 24fps, approximately 5.54 seconds and 1.64 MB, with a half-second dissolve across the loop boundary. Rebuild it from a downloaded source with FFmpeg, ffprobe and Python Pillow installed:

```sh
python3 scripts/prepare-hero-video.py path/to/source.mp4
python3 scripts/export-preview.py
```

Higgsfield generation totals **31.5 of the authorized 40 credits**, including the 9-credit hero replacement. No paid retries were made; 8.5 credits remain in the project allowance.

`scripts/refine-v2.php` applies the native-block content refinement once on local installations, retaining each original page in `_tc_content_before_v2`. `local-up.sh` runs it after the initial seed. Subsequent runs preserve CMS edits.

## Production boundary

**No production access or deployment has been performed.** Install `theme/techcloud` as a theme and `plugins/techcloud-content` as a plugin on the agreed WordPress staging environment first. `scripts/seed.php` is deliberately localhost-only and will not overwrite existing seeded content.

Before launch: approve copy and visual direction, confirm business details/shop destination, replace or remove the clearly labelled illustrative case study, migrate content, test editing on the target WordPress instance, and configure SEO/indexing for production. No testimonials, certifications, service levels, client results, or response-time guarantees have been invented.

Reference content and asset provenance: [docs/content-sources.md](docs/content-sources.md).
