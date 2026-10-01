=== Estatein ===
Contributors: vinceramirez
Requires at least: 6.5
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Custom dark real estate theme built from the "Real Estate Business Website UI Template – Dark Theme" Figma design by Produce UI.

== Description ==

Estatein is a classic PHP theme with:

* Custom Post Types: Property, Testimonial, FAQ, Team Member, Client, Service, Office, Enquiry.
* Taxonomies: Property Category, Property Location, Service Group, Office Type.
* ACF field groups registered in code (inc/fields.php), with fallbacks so the site works without ACF.
* Page templates: About Us, Properties (server-side search and filters), Services, Contact. The Home page uses front-page.php.
* Built-in forms (contact, property inquiry, property enquiry, newsletter). They are protected by a nonce and a honeypot, saved under Enquiries and emailed with wp_mail.
* Customizer panel "Estatein": banner, header button, CTA, FAQ heading, contact details, social links, footer.
* SEO: meta descriptions, Open Graph tags and JSON-LD. These turn off automatically when an SEO plugin is active.
* GSAP scroll animations, disabled for prefers-reduced-motion.

== Installation ==

1. Upload the theme zip in Appearance > Themes > Add New > Upload Theme, then activate it.
2. Install the free Advanced Custom Fields plugin (recommended).
3. Demo content (pages, menus, properties, testimonials, FAQs, team, clients, services, offices) is imported on activation. To run it again, use Tools > Estatein Demo or `wp estatein import-demo`.

== Frequently Asked Questions ==

= Where do I edit the page text? =
Edit the page itself. The ACF fields are grouped in tabs per section. Global elements are edited in Appearance > Customize > Estatein.

= Where do form submissions go? =
Every submission is saved under Enquiries in the dashboard. It is also emailed to the address set in the Customizer (by default, the site admin email).

== Changelog ==

= 1.0.0 =
* Initial release.

== Credits ==

* Design: "Real Estate Business Website UI Template – Dark Theme" by Produce UI (Figma Community).
* Urbanist font: SIL Open Font License 1.1.
* GSAP and ScrollTrigger by GreenSock: GSAP Standard "No Charge" License.
