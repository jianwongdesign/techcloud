# Blog design — 13 November 2026

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
