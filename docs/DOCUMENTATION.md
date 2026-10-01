# Estatein: Project Documentation

**Developer:** Vince Ramirez
**Design:** "Real Estate Business Website UI Template – Dark Theme" by Produce UI (Figma Community)
**Deliverables:** (1) a static HTML/CSS/JS site in `static/`; (2) a custom WordPress theme in `wordpress/estatein/`

---

## 1. Development process

**1. Reading the design precisely.** I didn't eyeball the Figma file. I worked from its real data. I decoded the `.fig` file and read the exact values for every frame:
- auto-layout padding and gaps;
- font sizes, line heights and colours (resolved from the colour styles);
- image crops.

The icons and background patterns were exported directly from the file as SVG, so none of them are redrawn or substituted.

**2. One source, two outputs.** All the markup patterns, CSS and JavaScript live in `src/`. A small Node build script (`tools/build.js`):
- renders the static pages from partials (shared header, footer and sections);
- bundles and minifies the CSS and JS with esbuild;
- builds an SVG icon sprite;
- copies the same assets into the WordPress theme.

The WordPress templates output the same HTML structure as the static pages. As a result, the theme looks and behaves exactly like the static site.

**3. Responsive system.** The design has three frames: Desktop 1920, Laptop 1440 and Mobile 390.
- **Between 1440 and 1920:** sizes are design tokens written as `fluid(laptop, desktop)`. The build turns each one into a CSS `clamp()` that is exact at 1440 and 1920 and scales smoothly in between.
- **Below 1440:** dedicated tablet (≤1199, ≤1023) and mobile (≤767) layouts.
- **Copy differences:** where the design shortens text on mobile, small variant classes switch the copy, so the design's wording is used everywhere.

**4. Verifying pixel fidelity.** I built screenshot tooling with Puppeteer:
- full-page captures, overlaid on reference renders of the Figma frames to compare section heights;
- a static-vs-WordPress pixel diff for every page.

Desktop pages are within 0–2 px of Figma. The WordPress pages have the same heights as the static pages at 1920, 1440 and 390. The remaining pixel difference is under 1%, and it comes only from image resampling.

**5. WordPress build.** I turned every repeated piece of content into a post type, every page's copy into fields, and every global element into a Customizer setting. I added a demo importer so the theme looks like the design immediately after activation. Finally, I tested the theme locally on WordPress 7.1 with PHP 8.2: forms, filters, menus, the Customizer, and running with and without ACF.

**6. Motion, SEO and performance.** GSAP was added last, as progressive enhancement, so it can never change the final layout. The site gets the same SEO tags and structured data with or without an SEO plugin.

---

## 2. Theme structure and key choices

| Area | Choice and reason |
|---|---|
| Theme type | **Custom classic PHP theme**, not a block theme or page builder. It gives full control over the markup needed for pixel-perfect output, and it is easy for an evaluator to read. |
| Content model | **8 Custom Post Types:** Property, Testimonial, FAQ, Team Member, Client, Service, Office and Enquiry. **4 taxonomies:** Property Category, Property Location, Service Group and Office Type. Everything that repeats in the design is a post, so editors add items instead of editing HTML. |
| Fields | **ACF field groups registered in PHP** (`inc/fields.php`). They version with the code, and nothing needs importing. Every field has a fallback (post meta, then the design's default copy), so the site never breaks if ACF is turned off. The property gallery uses a small native meta box, because ACF's Gallery field is Pro-only. |
| Global content | Banner, header button, CTA, FAQ heading, contact details, social links and footer live in **Customizer → Estatein**, with live preview. |
| Templates | `header.php`/`footer.php` are reused everywhere. The page templates are About Us, Properties, Services and Contact; the front page is Home. There are also `single-property.php`, testimonial and FAQ archives, and blog fallbacks (`index.php`, `single.php`, `page.php`, `404.php`). The shared sections, cards and forms are in `template-parts/`. |
| Loop / queries | Listings use the WordPress Loop and `WP_Query`. The Properties search filters **on the server**, via GET parameters mapped to a tax/meta query, so filtered URLs are shareable and work without JavaScript. |
| Forms | A built-in handler (`inc/forms.php`) instead of a form plugin. It uses a nonce, a honeypot field and server-side validation. Each submission is saved as an **Enquiry** (viewable in the admin) and emailed with `wp_mail`. Forms submit by AJAX when JavaScript is available, and fall back to a normal POST and redirect. |
| Carousels | They use native CSS scroll-snap with arrow buttons. There is no slider library. The counter matches the design ("01 of 10"). |
| Security / standards | All output is escaped (`esc_html`, `esc_url`, `wp_kses`), all input is sanitised, and every string is translation-ready (text domain `estatein`). Theme files exit on direct access. |

---

## 3. Plugins and libraries

| Name | Used for | Required? |
|---|---|---|
| **Advanced Custom Fields (free)** | Editing page copy and post details | Recommended. The theme works without it, using fallbacks. |
| **GSAP 3.13 + ScrollTrigger** | Scroll animations (bundled in the theme, self-hosted) | Bundled |
| SEO plugin (Yoast, Rank Math, …) | Optional | No. The theme outputs its own meta tags, Open Graph tags and JSON-LD, and turns them off if an SEO plugin is detected. |
| SMTP plugin | Optional, only if the host blocks PHP mail | No. Submissions are always saved under Enquiries. |

No page builders and no form or slider plugins are used.

---

## 4. Performance, SEO and accessibility

- **Images:**
  - Every image is converted to WebP in several widths and served with `srcset`/`sizes`, plus explicit width and height to avoid layout shift.
  - The hero (LCP) image is loaded with `fetchpriority="high"`; all others use `loading="lazy"`.
- **CSS/JS:**
  - One minified stylesheet (≈15 KB gzipped) and one app script (≈4 KB gzipped), all `defer`red.
  - On the designed pages, WordPress block-library styles are dequeued.
  - On Vercel, cache-busting hashes plus one-year caching.
- **Fonts:** the Urbanist latin subsets are self-hosted as WOFF2 and preloaded.
- **Icons:** one SVG sprite, cached once.
- **Animations:** the hero is never hidden. Only content below the first screen is animated, and every animation ends on the exact design layout. With motion turned on, I measured 0 layout shift and identical page heights. All motion is disabled for `prefers-reduced-motion`.
- **SEO:**
  - Unique titles and meta descriptions, canonical URLs, and Open Graph/Twitter tags.
  - JSON-LD: `RealEstateAgent`, plus `RealEstateListing` on property pages.
  - `sitemap.xml` and `robots.txt` on the static site; the core WP sitemap on WordPress.
  - A correct heading order with one H1 per page.
- **Accessibility:**
  - A skip link and semantic landmarks.
  - Labelled inputs with inline error messages.
  - `aria-current` in the navigation, `aria-expanded` on the mobile menu, and `aria-live` on carousel counters and form status.
  - Arrow-key tabs and visible focus styles.
  - Alt text on content images; decorative images are hidden from screen readers.

## 5. Testing

**Automated checks** run in Chrome through Puppeteer, at 1920, 1440 and 390 px plus the tablet breakpoints:
- **Layout:** page heights compared with Figma, and pixel diffs between the static and WordPress versions.
- **Motion:** a scroll-through with motion on, checking for console errors and layout shift (CLS = 0) and confirming that no element is left hidden.

**WordPress checks** (local WordPress 7.1, PHP 8.2):
- **Forms:** contact, newsletter, property inquiry and property enquiry. Each submission was saved under Enquiries.
- **Property filters:** keyword and type, plus an empty result.
- **Demo content:** import on a fresh install.
- **Without ACF:** with ACF deactivated, the pages render identically.

All PHP files pass `php -l`.

The CSS targets Chrome, Edge and Firefox 100+ and Safari 15+. It uses standard features only: grid, flex, `clamp()` and scroll-snap. A manual check in Edge, Firefox and Safari (iOS) is part of the release checklist.

## 6. Tools

Figma (`.fig` decoding with `kiwi-schema`), Node.js, esbuild, sharp (image optimisation), Puppeteer (screenshots and diffs), WP-CLI, VS Code and Git/GitHub. Hosting: Vercel (static) and InfinityFree or Wasmer (WordPress).
