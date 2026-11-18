# Blog design — 13 November 2026

## Separate Case Studies restored — 16 November 2026

The user requested Case Studies back. Production again serves `/case-studies/` and its four original illustrative detail pages without redirects. Both Case studies and Blog appear in header/footer navigation; the homepage uses its original case-study feed. The eight Blog posts, Microsoft outage article, unique featured images and 6/2 pagination are unchanged. No content was duplicated. The three-file theme restoration passed public HTTP/content checks and desktop, tablet and phone spot checks. See the latest `HANDOFF.md` entry and `artifacts/restore-cases-20261116/RESULT.md`. This supersedes the shared-section decision below; the static preview remains an older snapshot.

## Live publication — 15 November 2026

The user subsequently clarified that Case studies and Blog should be one section. Production now has one **Blog** navigation destination, homepage cards from the same Posts feed, a `/case-studies/` → `/blog/` redirect and four legacy-example redirects to the corresponding blog articles. The old `tc_case` records remain saved; future case studies belong in native Posts alongside other articles. The shared feed still contains eight posts with 6/2 pagination. The three-file theme update and redirects passed public HTTP and desktop/phone/tablet checks. The static review below has not been regenerated to reflect this live change.

Later the same day, the requested Microsoft outage case study was published at https://techcloud.sg/blog/microsoft-365-outage-temporary-email-continuity/. It uses verified incident information and a clearly labelled proposed response. Three new Higgsfield illustrations added its own hero and replaced the two repeated blog images, using 8.25 of the authorized 30 credits. There are now eight posts, eight distinct featured images and 6/2 pagination. Desktop, phone and tablet spot checks and anonymous route/canonical/related-link checks passed. See the newest `HANDOFF.md` entry and `docs/higgsfield-blog-budget-20261115.json`.

The user explicitly approved the seven preview articles as new live blog content. They are now published at https://techcloud.sg/blog/ with the sample disclaimers removed, current publication dates, original slugs, featured images, 6/1 pagination and related articles. This approval supersedes the sample-only restriction below for this completed publication; the local fixture seeder remains localhost-only. The normal theme update and a scoped WXR import preserved existing live users, pages and enquiries. No local database was restored. See `HANDOFF.md` and the ignored `artifacts/blog-live-20261115/RESULT.md` for backup, verification and rollback details.

The notes below describe the original design-review phase.

Built on `feature/blog-design` and merged into `main` for the requested shareable review. No production files, posts, accounts or settings were changed. The GitHub Pages review is available at https://jianwongdesign.github.io/techcloud/blog/ until 16 November 2026, 11:30 am Singapore/Malaysia.

## Review locally

- Listing: http://localhost:8129/blog/
- Pagination: http://localhost:8129/blog/page/2/
- Article: http://localhost:8129/blog/disaster-recovery-planning-5-step-emergency-plan-for-smes/

The local WordPress database contains seven clearly labelled sample articles to demonstrate two listing pages and related articles. These are design fixtures, not recovered legacy articles or approved TechCloud copy. The supplied legacy URL is retained as the first sample's slug only. Existing project imagery is used for the design samples; actual post images can be chosen in WordPress.

## Implemented

- Native WordPress Posts, featured images, excerpts, publication dates and block content.
- `/blog/` posts index with six articles per page and numbered Previous/Next pagination. Three columns on desktop, two on tablet, one on mobile. No search or category filter controls.
- `/blog/{post-slug}/` article URLs. Split image/title/date header, a centred reading column, and up to three Related Blogs, preferring matching categories and filling with recent posts. The current article and unpublished posts are excluded.
- No share/category sidebar, Learn More label, All Resources button or separate promotional banner. The existing site footer remains and now includes Blog.
- A logo fallback for articles without featured images, an empty listing state, native multipage article links and category/date archive support.
- Static export includes article pages, pagination and responsive uploaded image variants, with the existing preview expiry and non-sending form rules preserved.

References: [article composition](https://imscloudservices.com/small-business-disaster-recovery-plan-step-by-step-guide/), [listing composition](https://virtualdcs.co.uk/blog), and the user's annotated screenshots. No third-party article content or imagery was copied.

## Reproduce on a fresh local checkout

Start the documented local WordPress stack first. Then run:

```sh
./scripts/local-wp.sh eval-file /scripts/setup-blog.php
./scripts/local-wp.sh eval-file /scripts/seed-blog-preview.php
python3 scripts/export-preview.py
python3 scripts/check-blog.py
```

Both scripts are repeatable: setup reuses the Blog page; fixtures skip existing slugs. Sample seeding refuses non-local hosts. `check-blog.py` expects the seven-fixture local review database. It is not a production test.

## Later migration

1. Obtain working backend access and back up the current live theme, database, permalink settings and existing posts. Check live changes since this branch was made before deploying a theme diff.
2. Deploy the reviewed theme changes. Do **not** restore the local database or import the sample posts. Do not run `seed-blog-preview.php` on production.
3. Reuse or create a published page named Blog with slug `blog` and select it as the Posts page in Settings → Reading. Retain the existing static homepage.
4. Check the live permalink inventory before setting the post structure to `/blog/%postname%/`. Preserve original post slugs, dates, content and featured media. Existing non-blog article URLs may need redirects. Pages and the `case-studies` custom post type keep their existing routes.
5. WP-CLI alternative after that review: `wp eval-file /path/to/setup-blog.php allow-live`. The script records the old `page_for_posts` and `permalink_structure` in `tc_blog_previous_settings`, refuses a conflicting posts-page assignment, and flushes rewrites. Loading the theme alone does not change those settings.
6. Verify the supplied legacy article URL, all imported article links, pagination, related posts, footer link and mobile layouts. Check any SEO plugin configuration on the live site separately.

Rollback: restore the previous theme files and the recorded posts-page/permalink settings, then save permalinks. Retain any imported real posts. The setup script records whether the Blog page existed before setup; review a newly created page before removing it.

The previously prepared white-background favicon remains included in the local source and export. Applying the live Site Icon still needs access and verification.

## Validation

- PHP syntax checks passed for the theme and the two setup scripts.
- HTTP integration checks passed for seven single-post routes, 6/1 pagination with no overlap, a 404 for an out-of-range page, three distinct related links excluding the current post, footer navigation and exported responsive images.
- All 27 exported pages passed internal links/assets/headings and preview boundary checks. Existing client-copy, navigation and expiry checks passed.
- Native Chrome visual spot checks: desktop listing/article; 390×844 listing, article, related cards and pagination; 820×1180 listing/article. Previous-page navigation returned from page two to page one. No layout overflow was visible in these checks.
- Local pre-change database backup: ignored `artifacts/blog-20261113/before-blog.sql`.
