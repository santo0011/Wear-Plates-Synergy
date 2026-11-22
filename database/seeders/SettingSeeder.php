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
                ['established_year', 'Established Year', 'text', '2008'],
                ['iso_certified', 'Show "ISO Certified" badge', 'boolean', '1'],
                ['msme_registered', 'Show "MSME Registered" badge', 'boolean', '1'],
                ['logo', 'Logo', 'image', null, 'Optional. Upload a replacement logo badge (square PNG/WebP with transparent background). Leave empty to use the company badge.'],
                ['login_image', 'Admin Login Background Image', 'image', null, 'Optional wide photo (e.g. your plant). Leave empty to use a collage of product images.'],
                ['footer_about', 'Footer About Text', 'textarea', 'The leader in wear protection – wear liners, plates, Hardox fabrications, ceramics, tungsten carbide, alloy castings, welding electrodes and high-temperature coatings.'],
            ],
            'contact' => [
                ['phone', 'Mobile Number', 'tel', '+91 7981795006'],
                ['whatsapp_number', 'WhatsApp Number', 'tel', '+91 7981795006', 'Include the country code. Used for all WhatsApp buttons.'],
                ['email', 'Email Address', 'email', null, 'Leave empty to hide email on the website.'],
                ['gstin', 'GSTIN', 'text', '24AGWPD5947K1ZT'],
                ['corporate_office', 'Corporate Office', 'text', 'Mumbai'],
                ['plant_location', 'Manufacturing Plant', 'text', 'GIDC, Ankleshwar, Gujarat'],
                ['plant_address', 'Verified Full Plant Address', 'textarea', null, 'Enter only a verified complete address. Shown on the Contact page when filled.'],
                ['business_hours', 'Business Hours', 'text', null, 'Optional, e.g. "Mon – Sat". Leave empty to hide.'],
                ['google_map_embed', 'Google Map Embed URL', 'embed', null, 'Add only once a verified full address is available. Google Maps → Share → Embed a map → copy the src URL.'],
            ],
            'home' => [
                ['hero_title', 'Hero Title', 'text', 'WEAR PLATES SYNERGY VE INDIA'],
                ['hero_tagline', 'Hero Tagline', 'text', 'The Leader in Wear Protection'],
                ['hero_text', 'Hero Text', 'textarea', 'Wear liners, parts and components, standard plates, Hardox fabrications, ceramics, tungsten carbide, alloy castings, welding electrodes and high-temperature coatings – complete wear protection solutions for industry.'],
                ['hero_image', 'Hero Background Image', 'image', null, 'Optional wide photo (min. 1600px). Leave empty for the default industrial artwork.'],
                ['intro_heading', 'Introduction Heading', 'text', 'Complete Wear Protection Solutions'],
                ['intro_text', 'Introduction Text', 'textarea', $about],
            ],
            'about' => [
                ['about_heading', 'About Heading', 'text', 'The Leader in Wear Protection'],
                ['about_text', 'Company Description', 'textarea', $about],
                ['about_tagline', 'About Tagline', 'text', 'The expertise to lead, Lead to the bench marking.'],
                ['about_image', 'About Image', 'image', null, 'Optional photo of the plant or products.'],
                ['md_name', 'Managing Director Name', 'text', null, 'Optional. Leave empty to show the title only.'],
                ['md_title', 'Managing Director Title', 'text', 'Managing Director'],
                ['md_message', "Managing Director's Message", 'textarea', 'We are committed to delivering premium wear protection solutions with reliability, innovation, and service excellence. Our focus is on quality, durability, and service excellence.'],
                ['md_photo', 'Managing Director Photo', 'image', null],
            ],
            'why' => [
                ['why_heading', 'Section Heading', 'text', 'Why Choose Us'],
                ['why_intro', 'Section Introduction', 'textarea', 'Since 2008 we have supplied wear protection solutions to industry from our manufacturing plant at GIDC, Ankleshwar, supported by offices across India.'],
                ['why_items', 'Reasons (one per line)', 'list', implode("\n", [
                    'fa-solid fa-certificate | ISO Certified & MSME Registered | A certified and registered enterprise focused on consistent quality.',
                    'fa-solid fa-calendar-check | Serving Since 2008 | Experience in supplying wear protection products and solutions since 2008.',
                    'fa-solid fa-map-location-dot | Pan-India Presence | Corporate office in Mumbai, manufacturing plant at GIDC Ankleshwar and regional offices across India.',
                    'fa-solid fa-helmet-safety | Custom Fabrications & Engineering Support | Fabrication and engineering support tailored to your application.',
                    'fa-solid fa-shield-halved | Quality Materials & Durable Solutions | Wear protection products chosen for durability and reliable service life.',
                    'fa-solid fa-screwdriver-wrench | Technical Service | Technical guidance and service support from enquiry to delivery.',
                ]), 'Format per line: icon class | Title | Description. Icons from fontawesome.com (free, solid).'],
            ],
            'delivery' => [
                ['delivery_heading', 'Section Heading', 'text', 'Delivery & Logistics'],
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
                if (! $setting->exists) {
                    $setting->value = $value;
                }
                $setting->save();
            }
        }

        Cache::forget(Setting::CACHE_KEY);
    }
}
