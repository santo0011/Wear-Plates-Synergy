<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

/**
 * Website content & settings. Values come only from the company profile; anything not
 * provided (email, full addresses, MD name, social links) is left blank for the admin to fill.
 * Re-running the seeder never overwrites values already edited in the admin panel.
 */
class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $about = 'The leader in wear protection that includes Wear Liner, Parts, Components, Standard Plates, Hardox - Fabrications, Ceramics, Tungsten Carbide, Alloys Castings and Maintenance & Fabrication Welding Electrodes, Coatings for Thermal Insulation and Corrosion Resistance at High Temperature.';

        $settings = [
            'general' => [
                ['site_name', 'Company Name', 'text', 'Wear Plates Synergy VE India'],
                ['tagline', 'Tagline', 'text', 'The Leader in Wear Protection'],
                ['motto', 'Motto', 'text', 'Expertise to Lead, Lead to the Bench Marking', 'Shown in the top bar and footer.'],
                ['established_year', 'Established Year', 'text', '2008'],
                ['logo', 'Company Logo', 'image', null, 'Square PNG/WebP with transparent background (min. 400 × 400 px). Leave empty to use the bundled company badge.'],
                ['header_logo_strip', 'Header Logo Strip (all logos in one image)', 'image', null, 'Recommended. ONE transparent PNG containing all certification logos side by side (Make in India, ISO, MSME …), about 600 × 120 px. Shown in the website header only – when uploaded it replaces the individual logos below in the header. The footer always uses the individual logos.'],
                ['make_in_india_logo', 'Make in India Logo (footer)', 'image', null, 'Official Make in India logo, transparent PNG (about 400 × 200 px). Shown in the footer (and in the header only if no Header Logo Strip is uploaded).'],
                ['iso_logo', 'ISO Certification Logo (footer)', 'image', null, 'ISO 9001:2015 certification mark from your certification body, transparent PNG (about 300 × 300 px).'],
                ['msme_logo', 'MSME Logo (footer)', 'image', null, 'Optional MSME / Udyam logo, transparent PNG.'],
                ['iso_label', 'ISO Standard Text', 'text', 'ISO 9001:2015', 'Shown next to the ISO logo and in badges.'],
                ['iso_certified', 'Show ISO badge', 'boolean', '1'],
                ['msme_registered', 'Show MSME badge', 'boolean', '1'],
                ['make_in_india', 'Show Make in India badge', 'boolean', '1'],
                ['login_image', 'Admin Login Background Image', 'image', null, 'Optional wide photo (e.g. your plant). Leave empty to use a collage of product images.'],
                ['footer_about', 'Footer About Text', 'textarea', 'The leader in wear protection that includes Wear Liner, Parts, Components, Standard Plates, Hardox - Fabrications, Ceramics, Tungsten Carbide, Alloys Castings and Maintenance & Fabrication Welding Electrodes, Coatings for Thermal Insulation and Corrosion Resistance at High Temperature.'],
            ],
            'contact' => [
                ['phone', 'Mobile Number', 'tel', '+91 7981795006'],
                ['phone_secondary', 'Second Mobile Number', 'tel', '+91 8800556273', 'Optional. Leave empty to hide.'],
                ['whatsapp_number', 'WhatsApp Number', 'tel', '+91 7981795006', 'Include the country code. Used for all WhatsApp buttons.'],
                ['email', 'Email Address', 'email', 'care.wpsynergyveindia@gmail.com', 'Leave empty to hide email on the website.'],
                ['email_secondary', 'Second Email Address', 'email', 'wpsvei@gmail.com', 'Optional. Leave empty to hide.'],
                ['gstin', 'GSTIN', 'text', '24AGWPD5947K1ZT'],
                ['udyam_number', 'Udyam (MSME) Registration No.', 'text', 'UDYAM-GJ-06-0058021'],
                ['corporate_office', 'Corporate Office (short)', 'text', 'Mumbai'],
                ['plant_location', 'Manufacturing Plant (short)', 'text', 'GIDC, Ankleshwar, Gujarat'],
                ['registered_office', 'Registered Office Address', 'textarea', 'G-66, Ground Floor, Ganesh Square, GIDC, Ankleshwar 393002, Dist. Bharuch, Gujarat, India'],
                ['plant_address', 'Manufacturing Plant Address', 'textarea', 'Plot No. 4311, Near Crystal Chowkdi (BSNL Tower), GIDC, Ankleshwar 393002, Dist. Bharuch, Gujarat, India'],
                ['business_hours', 'Business Hours', 'text', null, 'Optional, e.g. "Mon – Sat, 9:30 am – 6:30 pm". Leave empty to hide.'],
                ['google_map_embed', 'Google Map Embed URL', 'embed', null, 'Google Maps → Share → Embed a map → copy the src URL.'],
            ],
            'delivery' => [
                ['delivery_heading', 'Section Heading', 'text', 'Our Freight Forwarder'],
                ['delivery_partners', 'Courier / Logistics Partners', 'text', 'Blue Dart | DHL', 'Separate names with "|".'],
                ['delivery_text', 'Delivery Text', 'textarea', 'Fast Delivery from Ankleshwar Plant Through Air & Surface Transports.'],
            ],
            'seo' => [
                ['meta_title_suffix', 'Title Suffix', 'text', 'Wear Plates Synergy VE India'],
                ['meta_description', 'Default Meta Description', 'textarea', 'Wear Plates Synergy VE India – the leader in wear protection since 2008. Wear liners, plates, Hardox fabrications, ceramics, tungsten carbide, alloy castings, welding electrodes and high-temperature coatings.'],
                ['meta_keywords', 'Meta Keywords', 'text', 'wear plates, wear liners, Hardox fabrication, ceramics, tungsten carbide, alloy castings, welding electrodes, thermal insulation coatings, Ankleshwar'],
                ['seo_home_title', 'Home Page Title', 'text', 'Wear Plates Synergy VE India | The Leader in Wear Protection'],
                ['seo_about_description', 'About Page Description', 'textarea', 'About Wear Plates Synergy VE India – serving industry since 2008 with wear protection solutions. ISO certified and MSME registered.'],
                ['seo_products_description', 'Products Page Description', 'textarea', 'Wear liners & plates, Hardox fabrications, ceramics & tungsten carbide components, alloy castings, coatings and welding electrodes from Wear Plates Synergy VE India.'],
                ['seo_why_description', 'Why Choose Us Description', 'textarea', 'Why industries choose Wear Plates Synergy VE India: ISO certified, MSME registered, serving since 2008 with pan-India presence and technical service.'],
                ['seo_presence_description', 'Our Presence Description', 'textarea', 'Corporate office in Mumbai, manufacturing plant at GIDC Ankleshwar, Gujarat and regional offices across India.'],
                ['seo_contact_description', 'Contact Page Description', 'textarea', 'Contact Wear Plates Synergy VE India for wear protection enquiries. Call or WhatsApp +91 7981795006.'],
            ],
            'mail' => [
                ['smtp_host', 'SMTP Host', 'text', null, 'e.g. smtp.gmail.com or mail.yourdomain.com. Leave empty to disable sending email.'],
                ['smtp_port', 'SMTP Port', 'text', '587', 'Usually 587 (TLS) or 465 (SSL).'],
                ['smtp_encryption', 'Encryption', 'select', 'tls'],
                ['smtp_username', 'SMTP Username', 'text', null, 'Usually your full email address.'],
                ['smtp_password', 'SMTP Password', 'password', null, 'Stored encrypted. For Gmail use an App Password. Leave blank to keep the saved password.'],
                ['mail_from_address', 'From Email Address', 'email', null, 'Address emails are sent from (often the same as the SMTP username).'],
                ['mail_from_name', 'From Name', 'text', 'Wear Plates Synergy VE India'],
                ['enquiry_notify_email', 'Send New Enquiry Alerts To', 'email', null, 'Optional. Receive an email whenever a customer submits the enquiry form.'],
            ],
            'social' => [
                ['social_linkedin', 'LinkedIn URL', 'url', null],
                ['social_facebook', 'Facebook URL', 'url', null],
                ['social_instagram', 'Instagram URL', 'url', null],
                ['social_youtube', 'YouTube URL', 'url', null],
            ],
        ];

        foreach ($settings as $group => $items) {
            foreach ($items as $order => $item) {
                [$key, $label, $type, $value] = $item;
                $setting = Setting::firstOrNew(['key' => $key]);
                $setting->fill([
                    'group' => $group,
                    'label' => $label,
                    'type' => $type,
                    'help' => $item[4] ?? null,
                    'sort_order' => $order,
                ]);
                // Never overwrite values edited in the admin panel; only fill empty ones.
                if (! $setting->exists || $setting->value === null || $setting->value === '') {
                    $setting->value = $value;
                }
                $setting->save();
            }
        }

        // Page content moved to the page-section editor (Admin → Website Pages).
        Setting::whereIn('group', ['home', 'about', 'why'])->delete();

        Cache::forget(Setting::CACHE_KEY);
    }
}
