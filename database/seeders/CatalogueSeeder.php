<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

/**
 * Product categories and products named in the company profile.
 * Descriptions are intentionally general: no technical specifications are invented.
 * Existing records (matched by slug) are not overwritten.
 */
class CatalogueSeeder extends Seeder
{
    public function run(): void
    {
        $note = 'Contact our team with your application details for sizes, grades and availability.';

        $catalogue = [
            [
                'name' => 'Wear Liners & Plates',
                'slug' => 'wear-liners-and-plates',
                'icon' => 'fa-solid fa-layer-group',
                'image' => 'images/categories/wear-liners.svg',
                'short' => 'Wear liners, wear parts and components to protect equipment surfaces exposed to abrasion and impact.',
                'products' => [
                    ['Wear Liners', 'Wear liners for protecting equipment surfaces subject to wear.', true],
                    ['Wear Parts & Components', 'Replacement wear parts and components for industrial equipment.', true],
                ],
            ],
            [
                'name' => 'Hardox - Fabrications & Standard Plates',
                'slug' => 'hardox-fabrications-and-standard-plates',
                'icon' => 'fa-solid fa-industry',
                'image' => 'images/categories/hardox.svg',
                'short' => 'Hardox fabrications and standard plates supplied and fabricated to your requirements.',
                'products' => [
                    ['Hardox Fabrications', 'Fabricated assemblies made from Hardox wear plate.', true],
                    ['Standard Plates', 'Standard wear plates for fabrication and maintenance.', false],
                ],
            ],
            [
                'name' => 'Ceramics & Tungsten Carbide Components',
                'slug' => 'ceramics-and-tungsten-carbide-components',
                'icon' => 'fa-solid fa-cubes',
                'image' => 'images/categories/ceramics.svg',
                'short' => 'Ceramic and tungsten carbide wear components for applications that demand high wear resistance.',
                'products' => [
                    ['Ceramic Wear Components', 'Ceramic components for wear protection applications.', true],
                    ['Tungsten Carbide Components', 'Tungsten carbide components for wear protection applications.', false],
                ],
            ],
            [
                'name' => 'Alloys Castings',
                'slug' => 'alloys-castings',
                'icon' => 'fa-solid fa-gear',
                'image' => 'images/categories/castings.svg',
                'short' => 'Alloy castings for wear-prone parts and components.',
                'products' => [
                    ['Alloy Castings', 'Alloy castings for wear parts and components.', false],
                ],
            ],
            [
                'name' => 'Thermal Insulation & Corrosion Resistant Coatings',
                'slug' => 'thermal-insulation-and-corrosion-resistant-coatings',
                'icon' => 'fa-solid fa-temperature-high',
                'image' => 'images/categories/coatings.svg',
                'short' => 'Coatings for thermal insulation and corrosion resistance at high temperature.',
                'products' => [
                    ['Thermal Insulation Coatings', 'Coatings for thermal insulation applications.', false],
                    ['High-Temperature Corrosion Resistant Coatings', 'Coatings for corrosion resistance at high temperature.', false],
                ],
            ],
            [
                'name' => 'Maintenance & Fabrication Welding Electrodes',
                'slug' => 'maintenance-and-fabrication-welding-electrodes',
                'icon' => 'fa-solid fa-bolt',
                'image' => 'images/categories/electrodes.svg',
                'short' => 'Welding electrodes for maintenance and fabrication work.',
                'products' => [
                    ['Maintenance Welding Electrodes', 'Welding electrodes for maintenance applications.', true],
                    ['Fabrication Welding Electrodes', 'Welding electrodes for fabrication applications.', false],
                ],
            ],
        ];

        foreach ($catalogue as $order => $item) {
            $category = ProductCategory::firstOrCreate(['slug' => $item['slug']], [
                'name' => $item['name'],
                'icon' => $item['icon'],
                'image' => $item['image'],
                'short_description' => $item['short'],
                'description' => $item['short']."\n\n".$note,
                'sort_order' => $order + 1,
                'is_active' => true,
            ]);

            foreach ($item['products'] as $pOrder => [$name, $short, $featured]) {
                $slug = \Illuminate\Support\Str::slug($name);
                if (Product::where('slug', $slug)->exists()) {
                    continue;
                }
                Product::create([
                    'product_category_id' => $category->id,
                    'name' => $name,
                    'slug' => $slug,
                    'short_description' => $short,
                    'description' => $short."\n\n".$note,
                    'sort_order' => $pOrder + 1,
                    'is_active' => true,
                    'is_featured' => $featured,
                ]);
            }
        }
    }
}
