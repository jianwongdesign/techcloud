# TechCloud — premium custom-theme preview

Updated 30 October 2026.

## Current state

- Repository: https://github.com/jianwongdesign/techcloud — public, branch `main`.
- Review preview: https://jianwongdesign.github.io/techcloud/ — GitHub Pages, static export of the local WordPress rendering.
- Local WordPress: http://localhost:8129 — isolated Docker project `techcloud`.
- Local CMS: http://localhost:8129/wp-admin/ — user `techcloud-admin`, random password in ignored `.env`.
- **techcloud.sg is untouched.** No live-site credentials, backups, migrations, or production changes were involved.

## Agreed scope

Custom WordPress theme only; no Elementor or other page builder. Native blocks edit page content. Home; Services overview and five individual child pages; Contact; Case Studies archive/detail; configurable Shop button. User said the Shop may be `/shop/` or a separate shop subdomain, to be supplied later. Empty setting opens a deliberate placeholder page.

## Implementation

Theme: `theme/techcloud`. Tiny first-party content plugin: `plugins/techcloud-content` for portable case studies and private enquiries. Only this plugin is active locally. Core editor, excerpts, featured images and revisions are supported. Business details and Shop destination are in Appearance → Customize → TechCloud business details. Primary navigation is editable through Appearance → Menus.

Five services follow the existing public website: managed IT, end-user support, IT staffing, project management, installation/infrastructure. Logo is the existing asset. The visual direction uses blue-grey neutrals, navy surfaces and logo-blue actions, self-hosted Manrope with 720–750 heading weights and 500 body weight, and full-colour imagery.

The latest revision replaces the small service tiles with a compact dark selector beside one large image and summary. Clicking or using arrow/Home/End keys selects a service; Explore service opens its detail page. The selector stacks above the image on mobile. Without JavaScript its real links go directly to the detail pages. Featured images override the existing illustrative fallback assets; full capabilities remain on the detail pages.

The middle of Home is also redesigned following the supplied screenshot: the business story has a large headline beside the workplace photo, with three benefits underneath, without the previous enclosing box. The case-study feature uses a different network image and blue-grey text panel. Process steps have stronger headings and separate top rules. The native content remains editable and the illustrative-case disclosure remains visible.

The original three Higgsfield Kling 3.0 Turbo clips cost 22.5 credits. A stronger hero replacement adds 9 credits, bringing the charged total to **31.5 of the authorized 40 credits**, with **8.5 remaining**; no paid retries. The hero now uses `hero-motion.mp4`: a visible camera move through server infrastructure, silent 1280×720 H.264, 5.54 seconds, 1.64 MB, with a half-second loop dissolve and matching WebP poster. Only this video plays; service imagery remains colour stills. The hero loads when its video area is visible and respects reduced motion/data saver; manual pause persists across scrolling. The three original clips remain in the repository. See `docs/higgsfield-budget.json`, `docs/content-sources.md`, and `scripts/prepare-hero-video.py`.

`rich.css` extends the brand styles and also loads in the native editor. `scripts/refine-v2.php` is local-only and idempotent; the previous content is saved in `_tc_content_before_v2`. It has already run locally. `local-up.sh` applies both seed and refinement for fresh installations.

Four case studies are explicitly labelled **illustrative, not client results**. Replace or remove them before production. Contact includes a first-party enquiry form with name, email, optional company/phone, service selection and message. WordPress saves private administrator-only enquiries and attempts notification to the configured business email. Validation, nonce, honeypot, duplicate handling and rate limiting are included. Email delivery on hosting is not yet verified. The static preview explicitly does not send messages and strips submission fields/tokens.

## Services, cases and motion refinement — 30 October

Five service detail pages now explain their scope, suitable situations, preparation details and FAQs, with colour stills and related service links. The overview retains the selector and adds a practical guide. `scripts/refine-services-cases.php` is local-only and runs once through `local-up.sh`; previous page/case content is retained in `_tc_before_expanded_content`.

Home shows two compact case cards and a view-all link. The archive has four distinct illustrative scenarios in a two-column grid, consistent photo proportions and 24px gaps, stacking on phones. Each detail page includes proposed scope and handover considerations. Native case Order controls their sequence. Existing imagery is reused; no credits spent.

Service selection crossfades in overlapping grid cells that reserve the tallest panel. Inactive panels are inert and hidden from accessibility APIs immediately. Nearby stills preload once unless data saver is enabled. Video playback avoids redundant play calls; manual pause, reduced motion and visibility behavior remain. Button hover no longer moves the control, and mobile navigation has a short opening transition.

The Contact email, phone and address use CSS sticky positioning on desktops above 1100px with viewport height at least 600px. They stop at the form boundary. Phones, tablets and short desktop windows use normal flow. The user also requested a shorter mobile hero: its copy and actions now sit directly over the video with a dark overlay, replacing the separate text and video stack. At 320px the hero is approximately 536px tall, including the pause control.

Local validation: all 15 exported HTML documents pass link, asset, heading and preview-boundary checks; PHP syntax and service/motion behavior checks pass. Desktop sticky contact behavior was confirmed at successive scroll positions. Desktop case rows and service detail content, 820px service scope/planning and case grid, and 320px mobile hero, menu, selector, case cards and Contact flow were inspected. Rapid keyboard selection keeps only one accessible active panel and the longest mobile service title fits without moving the section below. The layout detector reports no findings.

Revision `a34ce05` is published: https://github.com/jianwongdesign/techcloud/actions/runs/40921249723 succeeded. All 14 public pages and the updated CSS/JS returned HTTP 200 and byte-matched the committed preview. Chrome confirmed the published homepage at 390px with the text and buttons over the playing hero video. Production remains untouched.

## Repeated contact banner removal — 30 October

Removed the “Let’s make IT one less thing to worry about” banner from all pages, as requested from the screenshot. The footer enquiry/call buttons, email and map remain. The legacy `techcloud_cta` shortcode returns empty so saved native blocks do not display a raw shortcode; its unused Customizer controls are removed. Every exported HTML document matches the previous revision after removing only that banner. PHP syntax, export and whitespace checks pass. Revision `ded08ce` is published: https://github.com/jianwongdesign/techcloud/actions/runs/40917777861 succeeded. All 11 public pages returned HTTP 200 and byte-matched the export, with no contact-banner markup. Chrome confirms the homepage FAQ leads directly to the footer.

## Colour alignment — 30 October

Replaced the green/olive tint in surfaces, overlays, secondary text, button states, focus rings and Contact form with blue-grey neutrals. The original logo blue `#33398d` now carries actions; the contact banner uses the same `#253763` navy as the footer. Native editor palette and static review notice also match. Photography retains full colour.

This is colour-only: all layout/type declarations and every exported main section were checked against the preceding revision and are unchanged. Desktop and 320px phone views were inspected. Body/action contrast exceeds 4.5:1 and field/focus contrast exceeds 3:1 for checked pairs. Export and whitespace checks pass. Revision `c2269b1` is published: https://github.com/jianwongdesign/techcloud/actions/runs/40916363590 succeeded. All 11 public pages and the updated CSS returned HTTP 200 and byte-matched the committed export. Production remains untouched.

## Footer and Contact revision — 30 October

The broad editorial redesign from `d037796` was reverted at the user's request. The hero, service selector, business story, case-study feature, process, FAQ and page content are restored to `318b9d2` (the design published as `9c4596e`). New visual changes are confined to a deep-blue footer with enquiry/call/email links, the existing office address and a Google map, plus the Contact form. All 11 non-contact exported main sections and the restored templates byte-match that baseline; the baseline CSS is preserved before scoped footer/form rules.

`restore-v3.php` restores saved `_tc_before_v3` content and retains the discarded revision in `_tc_reverted_v3`. `refine-contact.php` adds the form shortcodes with a `_tc_before_contact_form` backup. Both are local-only and idempotent; `local-up.sh` applies them after V2. The superseded V3 migration is removed.

Local checks pass for form validation, spam controls, duplicate handling, private storage, anonymous-access rejection and retention after mail failure. Tests intercept mail and clean their own test records. Static preview submission was checked in Chrome: required fields validate and the confirmation explicitly says no message was sent. Footer map displays Cititech Industrial Building. Desktop, 820px tablet, 390px and 320px phone layouts were inspected. Export, PHP syntax, service/motion checks and layout detector pass. Revision `a5387b2` is published: https://github.com/jianwongdesign/techcloud/actions/runs/40915288825 succeeded. All 11 public pages and the revised CSS/JS returned HTTP 200 and byte-matched the export. Chrome confirmed the restored dark hero on the published preview.

Before production, exclude Contact and submission responses from full-page caching, configure mail transport and verify inbox delivery on the target hosting. Review enquiry retention and access with the client. No production changes or media credits used.

## Verification completed

- Previous preview revision `9c4596e` was published: https://github.com/jianwongdesign/techcloud/actions/runs/40909598799 succeeded. All 11 public pages and the revised CSS returned HTTP 200 and byte-matched the committed export. The published desktop homepage confirms the case-study heading and description grouped on the left with clean wrapping. Production remains untouched.

- Text arrangement and phone adaptation: the case-study introduction is now one left-aligned stack, with natural heading wrapping and a 65ch paragraph measure. At phone widths, headings scale down, key body text is 16px, section/card padding is reduced, text-link tap areas are at least 44px high, and the navigation scrolls inside short viewports. Reviewed in Chrome responsive emulation at 320px and 390px portrait, plus 667×375 and 667×320 landscape. The mobile menu opens and its Shop link remains reachable by scrolling; service selection opens the correct infrastructure detail page and the long heading fits. Desktop and 820px tablet introduction layout were also inspected. Export checks, layout detector and diff whitespace checks pass. Physical iOS/Android touch testing remains part of release QA.

- Previous preview revision `d5c14db` is published: https://github.com/jianwongdesign/techcloud/actions/runs/40907820382 succeeded. All 11 public pages and the revised CSS/JavaScript returned HTTP 200 and byte-matched the committed export. Chrome confirmed the published service selector, open business story, three-column benefits and dark case-study feature with the distinct network image. Production remains untouched.

- Current selector and middle-section redesign: reviewed in Chrome at desktop, 820px tablet, 390px mobile and 320px narrow mobile. The long infrastructure title fits. Mouse selection and arrow-key selection update the image, summary and detail destination; the selected infrastructure button opens its correct detail page. Business story, benefits, contrasting case study and process stack cleanly on mobile. PHP syntax, export validation, motion checks, service interaction checks and diff whitespace checks pass. The layout detector returned no findings. No media was generated; the credit ledger is unchanged.

- Compact services revision: desktop, 820px tablet, 390px mobile and 320px narrow mobile were reviewed in Chrome. All five services and the contact link are present, thumbnail sizes match, and the long infrastructure title fits. Local PHP, export and layout checks pass. No generation credits spent.
- Previous preview revision `953e345` is published: https://github.com/jianwongdesign/techcloud/actions/runs/40904621416 succeeded. All 11 public pages and the stylesheet returned HTTP 200 and matched the committed export. The published homepage confirms the compact two-row grid and reduced section spacing; its new contact tile opens Contact. The unchanged hero MP4 also byte-matches the local source. Production remains untouched.

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
