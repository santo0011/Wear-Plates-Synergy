# Wear Plates Synergy VE India – Corporate Website

Laravel 12 · MySQL · Bootstrap 5 · Font Awesome 6 · Blade components

## Local setup (XAMPP)

1. Start **MySQL** in the XAMPP Control Panel.
2. Create the database (once):
   ```
   C:\xampp\mysql\bin\mysql.exe -uroot -e "CREATE DATABASE IF NOT EXISTS wear_plates_synergy CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
   ```
3. Install and seed:
   ```
   composer install
   copy .env.example .env      # then set ADMIN_EMAIL / ADMIN_PASSWORD
   php artisan key:generate
   php artisan migrate --seed
   # Open the project through Apache (XAMPP) at http://localhost/ware
   ```
4. Website: http://localhost/ware · Admin: http://localhost/ware/admin

The project-root `.htaccess` forwards requests internally to Laravel's `public/` directory, so `/public` does not appear in the URL. Apache's `mod_rewrite` and `.htaccess` overrides must be enabled.

## Admin account

Created by `Database\Seeders\AdminSeeder` from `ADMIN_NAME`, `ADMIN_EMAIL`, `ADMIN_PASSWORD` in `.env`.
- If `ADMIN_PASSWORD` is empty (non-production), a random password is generated and printed once.
- In production, seeding stops unless a password of at least 12 characters is set.
- An existing admin's password is never overwritten by re-seeding. Change it from **My Profile**.

## What is managed from the admin panel

| Area | Where |
|---|---|
| Product categories (image, icon, SEO, order, visibility) | Product Categories |
| Products (image upload, featured, SEO) | Products |
| Enquiries (status: New / In Progress / Quoted / Closed / Spam, notes, WhatsApp reply) | Enquiries |
| Company name, tagline, logo, ISO / MSME badges | Website Content → General |
| Phone, WhatsApp, email, GSTIN, offices, verified address, Google Map | Contact Details |
| Hero and introduction text, hero image | Home Page |
| About text, tagline, MD name, message and photo | About & MD Message |
| "Why Choose Us" items (`icon \| Title \| Description` per line) | Why Choose Us |
| Courier partners and delivery text | Delivery & Logistics |
| Meta titles and descriptions | SEO |
| Offices on the India map | Locations / Presence |
| SMTP server, sender address, new-enquiry email alerts, test email | Website Settings → Email / SMTP |
| Admin login background photo | Website Settings → General |

## Email & password reset

- "Forgot password?" on the admin login sends a reset link (valid 60 minutes) to the admin's email.
- Emails are sent through the SMTP details saved in **Website Settings → Email / SMTP** (the SMTP password is stored encrypted).
  Until SMTP is set up, emails are written to `storage/logs/laravel.log` instead of being delivered.
- Gmail: host `smtp.gmail.com`, port `587`, encryption TLS, username = Gmail address, password = a Google **App Password**.

Information not in the company profile (email, full addresses, MD name, social links,
Google Map) is left empty and stays hidden on the site until it is filled in.

## Notes

- Uploaded images are stored in `public/uploads` (no `storage:link` needed). Script execution there is blocked by `.htaccess`.
- The seeded category artwork is original SVG illustration (`public/images/categories`). Replace it with real product photos via the admin.
- The India map is a simplified illustration. Pins use city-level coordinates only.
- Security: separate `admin` guard, hashed passwords, CSRF on all forms, login rate limiting (5 attempts),
  enquiry form throttling and honeypot, no-cache headers on admin pages, image MIME and size validation.
- SEO: clean URLs (`/products/{category}/{product}`), per-page meta tags, Open Graph, JSON-LD organization data, `/sitemap.xml`.

## Tests

```
php artisan test
```
The suite (20 feature tests) runs on an in-memory SQLite database and does not touch MySQL data.

## Deploying to production

Set `APP_ENV=production`, `APP_DEBUG=false`, the real `APP_URL`, and a strong `ADMIN_PASSWORD`, then run:
```
composer install --no-dev --optimize-autoloader
php artisan migrate --force --seed
php artisan config:cache && php artisan route:cache && php artisan view:cache
```
Point the web server's document root to the `public/` folder.
