# Estatein: Real Estate Website (Static Site + WordPress Theme)

This is a pixel-perfect build of the Figma design **"Real Estate Business Website UI Template – Dark Theme"** by Produce UI (Figma Community). It matches the Desktop (1920), Laptop (1440) and Mobile (390) frames, and it is fully responsive between them.

> **This repository contains two separate deliverables of the same design.**
> Use the folder that matches what you want to run.

| | Folder | What it is | Where it runs |
|---|---|---|---|
| 🌐 **Static website** | [`static/`](static/) | Plain HTML, CSS and JavaScript. Ready to open or deploy. | Vercel (any static host) |
| 🧩 **WordPress theme** | [`wordpress/estatein/`](wordpress/estatein/) | Custom classic theme (PHP) with Custom Post Types, ACF fields, a working enquiry system and one-click demo content | Any WordPress 6.5+ host (InfinityFree, Wasmer, …) |

Both are generated from **one shared source** ([`src/`](src/)). The CSS, JavaScript, icons, fonts and images are identical in both, so the WordPress theme looks exactly like the static site.

📄 Project write-up (process, choices, plugins): [`docs/DOCUMENTATION.md`](docs/DOCUMENTATION.md)

---

## Live demo

| Version | URL |
|---|---|
| Static site (Vercel) | _coming soon_ |
| WordPress theme | _coming soon_ |

---

## Repository structure

```
estatein/
├── static/                 ← 🌐 STATIC SITE (built output, deploy this folder)
│   ├── index.html          Home
│   ├── about.html          About Us
│   ├── properties.html     Properties (search + filters)
│   ├── property-details.html
│   ├── services.html
│   ├── contact.html
│   ├── testimonials.html   "View All Testimonials"
│   ├── faqs.html           "View All FAQ's"
│   ├── terms.html
│   ├── sitemap.xml, robots.txt
│   └── assets/             css · js (+ vendor/GSAP) · fonts · icons · images (WebP)
│
├── wordpress/
│   └── estatein/           ← 🧩 WORDPRESS THEME (zip this folder and upload it)
│       ├── style.css       Theme header
│       ├── functions.php   Loads everything in inc/
│       ├── inc/            Post types, ACF fields, Customizer, forms, SEO, demo importer…
│       ├── page-templates/ About Us · Properties · Services · Contact
│       ├── template-parts/ Reusable sections, cards and forms
│       ├── front-page.php, single-property.php, archive-*.php, page.php, index.php, 404.php…
│       └── assets/         Same CSS/JS/images as the static site (copied by the build)
│
├── src/                    ← Shared source for both builds
│   ├── pages/              Static page templates
│   ├── partials/           Header, footer, head and repeated sections
│   ├── css/                Design tokens + component styles
│   ├── js/                 Navigation, carousels, gallery, tabs, forms, search, GSAP animations
│   ├── icons/              SVG icons exported from the Figma file (built into one sprite)
│   └── vendor/             GSAP 3.13 + ScrollTrigger (self-hosted)
│
├── tools/                  Build script, image optimiser, screenshot and compare tools
├── docs/                   Project documentation
└── vercel.json             Vercel config (serves static/, cache and security headers)
```

> ✏️ **Edit files in `src/`, not in `static/`.** `static/` and the theme's `assets/` folder are build output.

---

## 🌐 Static site

### Run locally
No build is needed to view it. Open `static/index.html`, or serve the folder:

```bash
cd tools
npm install
node serve.js ../static 5173     # → http://localhost:5173
```

### Deploy to Vercel
1. Import this repository in Vercel.
2. Leave the settings as they are. [`vercel.json`](vercel.json) already sets the output directory to `static/`, with no build step.
3. Deploy.

If the site's URL is not `https://estatein-vinceramirez.vercel.app`, rebuild with your URL so the canonical, Open Graph and sitemap links are correct (see [Building from source](#building-from-source)).

> The static site has no backend. Its forms validate and show a confirmation message, but they don't send anything. Real form handling (saving and email) is done by the WordPress theme.

---

## 🧩 WordPress theme

### Install
1. Zip the folder `wordpress/estatein/` so the zip contains `estatein/style.css`.
2. In WordPress, go to **Appearance → Themes → Add New → Upload Theme**, choose the zip, then **Activate**.
3. Recommended: install the free **Advanced Custom Fields** plugin. The dashboard shows a notice with an install link.

### Demo content (automatic)
Activating the theme imports the design's content once:
- the Home, About Us, Properties, Services, Contact, Terms & Conditions and Blog pages, with their page templates;
- properties with images and gallery, testimonials, FAQs, team members, clients, services and offices;
- the Primary and Footer menus, the front page setting, and `/%postname%/` permalinks.

To run it again, go to **Tools → Estatein Demo**, or use WP-CLI: `wp estatein import-demo`. Content that already exists is left untouched.

### Where to edit content

| Content | Where in WP Admin |
|---|---|
| Page text (hero, sections, buttons) | Edit the page. The ACF fields appear in tabs per section. |
| Properties (price, beds, baths, area, pricing details, gallery) | **Properties** (with Categories and Locations) |
| Testimonials, FAQs, Team, Clients, Services, Offices | Their own menu items |
| Banner, header button, CTA, FAQ heading, contact details, social links, footer | **Appearance → Customize → Estatein** |
| Menus | **Appearance → Menus** (Primary, Footer) |
| Form submissions | **Enquiries**. Every form is also emailed to the address set in the Customizer. |

### What's inside
- **Custom Post Types:** Property, Testimonial, FAQ, Team Member, Client, Service, Office and Enquiry. **Taxonomies:** Property Category, Property Location, Service Group and Office Type.
- **ACF field groups** are registered in code (`inc/fields.php`), so nothing needs importing. Without ACF, the theme falls back to post meta and the design's default copy, so pages never break.
- **WordPress Loop / `WP_Query`** on every listing. The property search filters on the server (keyword, location, type, price, size, build year).
- **Reusable header and footer** (`header.php`, `footer.php`) and template parts for sections, cards and forms.
- **Forms** (contact, property inquiry, property enquiry, newsletter). They use a nonce and a honeypot, validate on the server, save to *Enquiries* and send with `wp_mail`. They work with or without JavaScript (AJAX when it is available).
- **SEO:** meta descriptions, Open Graph/Twitter tags and JSON-LD (`RealEstateAgent`, plus `RealEstateListing` on property pages), with the theme's post types in the core XML sitemap. These step aside automatically if Yoast, Rank Math, SEOPress or AIOSEO is active.
- **Tested** on WordPress 7.1 with PHP 8.2. It requires WordPress 6.5+ and PHP 7.4+.

> Note: some free hosts limit PHP `mail()`. Submissions are always saved under **Enquiries**. If email delivery matters on that host, add an SMTP plugin.

---

## Building from source

```bash
cd tools
npm install
npm run build                     # src/ → static/ + wordpress/estatein/assets/
SITE_URL=https://your-domain.com npm run build   # set the public URL used in canonical/OG/sitemap
npm run images                    # re-generate the responsive WebP images (only if images change)
```

The build:
- renders the pages from `src/pages` and `src/partials`;
- concatenates and minifies the CSS and JS with esbuild;
- builds the SVG icon sprite;
- adds cache-busting hashes;
- writes `sitemap.xml` and `robots.txt`;
- copies all assets into the WordPress theme.

---

## Highlights

- **Pixel-perfect.** Spacing, type and colours come from the Figma file. Every page was checked with screenshot overlays at 1920, 1440 and 390. The WordPress pages match the static pages at all three widths.
- **Responsive.** Values scale smoothly from 1440 to 1920 (CSS `clamp()`). There are dedicated tablet and mobile layouts.
- **Animations (GSAP + ScrollTrigger).** Sections fade up on scroll, cards stagger in, stats count up and the hero badge rotates. Nothing above the fold is hidden, the layout never shifts, and all motion is turned off for `prefers-reduced-motion`.
- **Performance:**
  - Responsive WebP images with `srcset`; the hero image uses `fetchpriority="high"` and everything else is lazy-loaded.
  - One CSS file (≈15 KB gzipped) and one app script (≈4 KB gzipped), all deferred.
  - Self-hosted, preloaded Urbanist font subsets.
  - A single SVG sprite for icons, and long-term caching on Vercel.
- **Accessibility:**
  - Semantic landmarks and a skip link.
  - Labelled form fields with inline errors.
  - `aria-current`, `aria-expanded` and `aria-live` where needed.
  - Keyboard-friendly carousels and tabs, visible focus styles and alt text.

## Credits
- Design: *Real Estate Business Website UI Template – Dark Theme* by **Produce UI** (Figma Community). Images come from the template.
- Font: **Urbanist** (SIL Open Font License).
- Animation: **GSAP** by GreenSock (free "Standard" license).

Developed by **Vince Ramirez**.
