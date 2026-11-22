# Wear Plates Synergy VE India – Website Rebuild Plan

Source: `knowledge_pdf/Untitled_removed.pdf` (56 scanned pages, no selectable text).
All text was transcribed from the scans; every image is left as an upload slot in the
admin panel (the client will upload enhanced photos). Page-by-page mapping:
`docs/PDF_CONTENT_MAPPING.txt`.

## Goals
1. Every piece of client content from the PDF is on the website, re-designed in a
   corporate / MNC style (not a copy of the brochure layout).
2. Everything is dynamic: text, lists, tables and images are editable in the admin panel.
3. Header shows the company logo plus **Make in India** and **ISO** logos, all uploadable
   from *Website Settings → General*.
4. Admin sidebar: main menu per website page → sub-menu per page section.

## Architecture
* **Section CMS** – `config/cms.php` registers every page and its sections and fields
  (text, textarea, image, lines, table, repeater, icon, url, boolean).
  Content is stored as JSON in `page_sections` (one row per page section).
  `cms('home.hero')` reads a section in Blade; results are cached.
* **Generic admin editor** – one controller and one form render any section from the
  registry, including repeaters (add / remove / reorder / per-item image upload).
* **Catalogue** – products/categories stay as models; products gain *highlights*,
  *specifications*, *technical data table* and *applications* fields.
* **Locations, Enquiries, Settings** – kept; settings keep only site-wide groups
  (General, Contact, Delivery, SEO, Social, Email).

## Site map (public)
| Page | URL | Main content (PDF pages) |
|---|---|---|
| Home | `/` | hero slider, trust strip, intro, capacity stats, products, Value Engineering, industries, process, projects, clients, CTA (3, 4, 6, 7, 9, 21) |
| About Us | `/about-us` | company profile, associations, mission & values compass, MD message, milestones, award, Synergy group (2, 3, 4, 6, 9, 21, 22, 23, 27, 28) |
| Leadership | `/leadership` | MD & COO profiles, management team, promoters (5, 24, 25, 26) |
| Products | `/products/...` | 8 categories, 26 products with specs and technical data tables (36, 41–46, 49–51, 10, 11, 43, 54, 55) |
| Industries & Services | `/industries` | 10 industry sectors with key jobs, steel mill roll reconditioning table (11–13, 17, 19) |
| Infrastructure | `/infrastructure` | capacity, facilities, machinery list, robotic process, design & drawings, 2026 expansion, plant gallery (4, 5, 7, 20, 22, 28, 32–34) |
| Quality | `/quality` | quality commitment, ISO / GST / Udyam certificates, QAP stages, testing (29–31, 35, 38) |
| Projects | `/projects` | iconic projects with specifications (8, 10, 14, 15, 53, 54, 55) |
| Technical Resources | `/technical-resources` | hardfacing article, wear mechanisms, earth-moving concept, World of Wear Plates pyramid, PPA applications (18, 37–40, 44) |
| Why Choose Us | `/why-choose-us` | reasons, Value Engineering / LPF, delivery (3, 6, 9) |
| Our Presence | `/our-presence` | offices & India map (1, 3, 21, 52) |
| Contact Us | `/contact-us` | addresses, enquiry form (1, 4, 19, 52) |

## Admin sidebar
Dashboard · Enquiries · Home Page ▸ sections · About Us ▸ · Leadership ▸ · Products ▸
(All Products, Add Product, Categories, page sections) · Industries ▸ · Infrastructure ▸ ·
Quality ▸ · Projects ▸ · Technical Resources ▸ · Why Choose Us ▸ · Our Presence ▸
(Locations, sections) · Contact ▸ · Website Settings ▸ (General, Contact, Delivery, SEO,
Social, Email) · My Profile.

## Phases
1. **Foundation** – `page_sections` table, model, `cms()` helper, `config/cms.php` registry.
2. **Admin** – grouped sidebar with sub-menus, generic section editor with repeaters,
   product form with the new fields, header-logo settings.
3. **Content** – seeders with all PDF content (sections, catalogue, locations, settings).
4. **Website** – header (logo + Make in India + ISO), mega navigation, new pages,
   redesigned existing pages, corporate styling, image placeholders.
5. **QA** – migrate + seed, render every page and admin screen, fix issues.

## Content decisions
* Personal details (family, hobbies) from the leadership profiles are left out;
  professional history is kept.
* Sales-only lines ("cash job", "100 % advance") are left out of product pages.
* Duplicate pages (16, 17, 39, 47, 48, 56) are used once.
* Contact details differ between pages (two mobiles, several emails) – all are
  seeded where they appeared; the client should confirm which to show.
