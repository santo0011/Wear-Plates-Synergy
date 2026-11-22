<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Product catalogue – text taken WORD FOR WORD from the client PDF (see docs/PDF_TRANSCRIPT.txt).
 * Images are left empty – upload them from Admin → Products.
 *
 * Existing records (matched by slug) are not changed unless CMS_SEED_OVERWRITE=1 is set; then the
 * text of seeded categories/products is replaced (uploaded images are kept).
 */
class CatalogueSeeder extends Seeder
{
    /** Slugs from earlier builds that are no longer part of the catalogue. */
    private const RETIRED_CATEGORY_SLUGS = [
        'wear-liners-and-plates', 'hardox-fabrications-and-standard-plates', 'ceramics-and-tungsten-carbide-components',
        'alloys-castings', 'thermal-insulation-and-corrosion-resistant-coatings', 'maintenance-and-fabrication-welding-electrodes',
    ];

    public function run(): void
    {
        $overwrite = (bool) env('CMS_SEED_OVERWRITE', false);

        ProductCategory::whereIn('slug', self::RETIRED_CATEGORY_SLUGS)
            ->whereDoesntHave('products', fn ($q) => $q->whereNotNull('image'))
            ->get()->each(fn ($c) => $c->delete());

        $keepSlugs = [];
        foreach ($this->catalogue() as $order => $cat) {
            $category = ProductCategory::firstOrNew(['slug' => $cat['slug']]);
            if (! $category->exists || $overwrite) {
                $category->fill([
                    'name' => $cat['name'],
                    'icon' => $cat['icon'],
                    'short_description' => $cat['short'],
                    'description' => $cat['description'],
                    'sort_order' => $order + 1,
                ]);
                if (! $category->exists) {
                    $category->image = $cat['image'];
                    $category->is_active = true;
                }
                $category->save();
            }

            foreach ($cat['products'] as $pOrder => $p) {
                $slug = $p['slug'] ?? Str::slug($p['name']);
                $keepSlugs[] = $slug;
                $product = Product::firstOrNew(['slug' => $slug]);
                if ($product->exists && ! $overwrite) {
                    continue;
                }
                $product->fill([
                    'product_category_id' => $category->id,
                    'name' => $p['name'],
                    'subtitle' => $p['subtitle'] ?? null,
                    'short_description' => $p['short'] ?? null,
                    'description' => $p['description'] ?? null,
                    'highlights' => isset($p['highlights']) ? implode("\n", $p['highlights']) : null,
                    'specifications' => isset($p['specs']) ? implode("\n", $p['specs']) : null,
                    'data_table_title' => $p['table_title'] ?? null,
                    'data_table' => isset($p['table']) ? implode("\n", $p['table']) : null,
                    'applications' => isset($p['applications']) ? implode("\n", $p['applications']) : null,
                    'sort_order' => $pOrder + 1,
                    'is_featured' => $p['featured'] ?? false,
                ]);
                if (! $product->exists) {
                    $product->is_active = true;
                }
                $product->save();
            }
        }

        // When overwriting, remove seeded products that are no longer in the PDF catalogue (only if they have no image).
        if ($overwrite) {
            Product::whereNotIn('slug', $keepSlugs)->whereNull('image')->delete();
            ProductCategory::whereNotIn('slug', array_column($this->catalogue(), 'slug'))
                ->whereDoesntHave('products')->delete();
        }
    }

    /** Bead property table – row labels exactly as printed on PDF pages 46, 49, 50. */
    private function beadTable(array $chem, string $hrc, string $carb, string $matrix, string $g65, string $frac): array
    {
        $rows = ['Bead Properties /Weld Chemistry | Value'];
        foreach ($chem as $el => $v) {
            $rows[] = "Chemical composition : (wt %) (On top surface) – $el | $v";
        }

        return array_merge($rows, [
            "Bulk hardness (HRc) | $hrc",
            "Microhardness (VPN) – Carbides (Load 100 gm) | $carb",
            "Microhardness (VPN) – Matrix (Load 200 gm) | $matrix",
            "Abrasive wear resistance (1/cc) ASTM G 65, Practice A | $g65",
            "Primary carbides in Eutectic matrix vol. Fraction of carbides | $frac",
        ]);
    }

    private function catalogue(): array
    {
        $applications = ['Mixer Liners', 'Crusher side Liners', 'Chutes', 'Dip Tube in Cyclone', 'Grit Cone', 'Ball Mill Inlet Feeder Chute',
            'Cement Mill Inlet Cone without Screw', 'Cement Mill Valve (Half Open)', 'Vertical Mill (VRM)', 'Clinker Silo Outlet Cone', 'Pipes',
            'Casing and Ventilator of Air Separator', 'Pipelines for Clinker Transport etc.'];
        $abrasodurHighlights = [
            'WPI - ABRASODUR WEAR PLATES: Can be delivered as pre-finished blanks with fixation elements, sinkhole bores or others.',
            'Recoating is carried out with similar alloy electrodes or core wires.',
            'The plates can be easily cut by plasma cutting machine and can be rolled in to radius, thanks to the mild steel base.',
            'Different fixing techniques make "WPI ABRASODUR" versatile for many applications.',
        ];
        $abrasodurSpecs = [
            'WPI Wear plates - Abrasodur (Base material + Coating = Total, mm) | 5+3=8, 6+4=10, 6+5=11, 8+5=13, 8+8=16, 10+5=15, 10+8=18, 15+5=20 – Further dimensions on request.',
            'Our Standard Plate | 3000 x 1350 mm, of various composite thicknesses.',
            'Production Capacity | 1100 Sq.m. per month and 13200 sq.m. per annum',
        ];
        $ptaTable = ['Base material mm | Coating mm | Total mm', '3 | 2 | 5', '5 | 3 | 8', '6 | 4 | 10', '6 | 5 | 11', '8 | 5 | 13', '8 | 8 | 16', '10 | 8 | 18', '15 | 5 | 20'];
        $special = 'Special Alloys and Unique Metals : Tungsten Carbides, Silicon Carbides, Rich Cr-C Alloys, Ceramics, Cast Basalt and FR- Applications-Wear Parts, Wear Components';

        return [
            /* ---------------------------------------------------------------- 1 */
            [
                'name' => 'WPI Abrasodur Wear Plates',
                'slug' => 'composite-wear-plates',
                'icon' => 'fa-solid fa-layer-group',
                'image' => 'images/categories/wear-liners.svg',
                'short' => 'WPSVEI Wear Plates are branded as "WPI ABRASODUR" are ready to use wear plate consisting of a base material of carbon steel (IS 2062, ST37 or ST52) and a hard Wear Coating according to DIN 8555 group number 10 (High Carbon, High Chromium content with special carbides formulation of Niobium, Tungsten).',
                'description' => "The fabrication of the WPI - plate is carried out by use of a core wire welding process. The extreme wear resistance is achieved by use of high quality WPI core wire consumables with high Chromium and Carbon content. The addition of complex carbides enables the formation of a high content of Chromium- and special carbides, so that the required properties are achievable in the first layer in accordance to the DIN 8555 E10-60G. The characteristic, hypereutectic weld metal of the FeCrC-hardfacing alloy consists of large, primary deposited carbides of the type M7C3, embedded in the eutectic matrix.\n\n**WPI - ABRASODUR WEAR PLATES:** Can be delivered as pre-finished blanks with fixation elements, sinkhole bores or others. Recoating is carried out with similar alloy electrodes or core wires.\n\n**THE CONCEPT:** The deposit belongs to the hyper Eutectic group. The behavior against wear is obtained from precipitated phases during the solidification of the deposits. The precipitation of the primary carbides of Chromium-Niobium complex carbides and Borides yield very high micro hardness in Wear coating, thus providing exceptionally high wear resistance making the plates very good for abrasion, erosion & impact.\n\n**Our Standard Plate of 3000 x 1350 mm, of various composite thicknesses.**\n**Production Capacity : 1100 Sq.m. per month and 13200 sq.m. per annum**",
                'products' => [
                    [
                        'name' => 'WPI ABRASODUR 091004™',
                        'slug' => 'wpi-abrasodur-091004',
                        'subtitle' => "C-Cr-alloy in Fe base, Hardness 55 to 60 HRC for the application of 'A' + 'I' widely used in RMHP and HEMM",
                        'short' => 'WPI 091004 is a composite hardfacing plate consisting of a weldable steel plate and a wear resistant coating composed of the core wire WPI 0910.',
                        'description' => 'WPI 091004 is a composite hardfacing plate consisting of a weldable steel plate and a wear resistant coating composed of the core wire WPI 0910. The hardfaced coating of C-Cr-alloy is related to the alloy group Fe14 according to EN 14700 (group 10 former DIN 8555). Chromium carbides in combination with borides and other metals produce an adamantine and wear resistant coating. These coatings are useful in the ceramics industry, mineral crushing, mining industry, fan and other applications that have high demands for wear resistance.',
                        'highlights' => $abrasodurHighlights,
                        'specs' => array_merge(['Product | WPI Abrasodur 091004', 'Matrix | FeCrC'], $abrasodurSpecs),
                        'table_title' => 'Bead Properties /Weld Chemistry',
                        'table' => $this->beadTable(['C' => '3.5 - 4.0', 'Cr' => '18 - 22', 'Si' => '0.4 – 0.6', 'Mn' => '2.0 – 2.5', 'Fe' => 'Bal'], '55 - 60', '1000 - 1150', '520 - 580', '45–50', '20–25%'),
                        'applications' => $applications,
                        'featured' => true,
                    ],
                    [
                        'name' => 'WPI ABRASODUR 220513™',
                        'slug' => 'wpi-abrasodur-220513',
                        'subtitle' => "C-Cr-alloy in Fe base, Hardness 59 to 62 HRC for the application of 'E' + 'A' + 'm I' at ambient and elevated temp. Both",
                        'short' => "WPI ABRASODUR 220513™, C-Cr-alloy in Fe base, Hardness 59 to 62 HRC for the application of 'E' + 'A' + 'm I' at ambient and elevated temp. Both",
                        'description' => null,
                        'highlights' => $abrasodurHighlights,
                        'specs' => $abrasodurSpecs,
                        'table_title' => 'Bead Properties /Weld Chemistry',
                        'table' => $this->beadTable(['C' => '3.5 - 4.5', 'Si' => '0.1', 'Mn' => '0.5', 'Cr' => '18 – 24', 'Fe' => 'Bal', 'B' => 'Traces'], '59 - 61', '1030 - 1080', '580 - 650', '50 - 55', '18 - 20%'),
                        'applications' => $applications,
                    ],
                    [
                        'name' => 'WPI ABRASODUR 311263™',
                        'slug' => 'wpi-abrasodur-311263',
                        'subtitle' => "C-Cr-alloy in Fe base with complex carbides, Hardness 58 to 62 HRC for 'A' + 'E' + 'I' at elevated Temperature",
                        'short' => 'Abrasodur 311263 is a composite hardfacing plate consisting of a weldable steel plate and a wear resistant coating, which offers high wear protection against abrasion and moderate impact and impinging load.',
                        'description' => 'Abrasodur 311263 is a composite hardfacing plate consisting of a weldable steel plate and a wear resistant coating, which offers high wear protection against abrasion and moderate impact and impinging load. The coatings are temperature resistant up to 650°C as well as qualified for corrosion protection. The coating of a C-Cr-alloy with additional incorporated complex carbides of the type CrC, VC, MoC, WC and MoC is related to the alloy group Fe16 according to EN 14700 (group 10 former DIN 8555). Steel fabrication, fans in the hot gas area, foundries',
                        'highlights' => $abrasodurHighlights,
                        'specs' => array_merge(['Product | WPI Abrasodur 311263', 'Matrix | FeCrC+ complex carbides'], $abrasodurSpecs),
                        'table_title' => 'Bead Properties /Weld Chemistry',
                        'table' => $this->beadTable(['C' => '3.0 - 4.0', 'Si' => '0.1', 'Mn' => '0.5', 'Cr' => '12 – 16', 'Nb' => '3.0 – 6.0', 'Fe' => 'Bal'], '60 - 66', '950 - 1030', '585 - 625', '65 - 70', '25 - 30%'),
                    ],
                    [
                        'name' => 'WPI ABRASODUR 280773™',
                        'slug' => 'wpi-abrasodur-280773',
                        'subtitle' => "C- Cr-alloy in Fe base with complex carbides, Hardness 60- 66 HRC for 'A'+'E' at elevated temperature",
                        'short' => 'WPI 280773 is a composite hardfacing plate consisting of a weldable steel plate and a wear resistant coating composed of a core wire coating WPI 2807.',
                        'description' => 'WPI 280773 is a composite hardfacing plate consisting of a weldable steel plate and a wear resistant coating composed of a core wire coating WPI 2807. The hardface coating of C-Cr-alloy with additional incorporated complex carbides related to the alloy group Fe15 according to EN 14700 (group 10 former DIN 8555). Chromium- and Niobium-carbides in combination with borides produce an adamantine and wear resistant coating applied for components exposed to energizing wear. This is not suitable for impact and impinging loads. This is useful for cement and concrete pumps, coatings for the ceramics industry, mineral crushing, mining industry, fan and recycling industries.',
                        'highlights' => $abrasodurHighlights,
                        'specs' => array_merge(['Product | WPI Abrasodur 280773', 'Matrix | FeCrNbC'], $abrasodurSpecs),
                        'table_title' => 'Bead Properties /Weld Chemistry',
                        'table' => $this->beadTable(['C' => '3.0-4.0', 'Si' => '0.1', 'Mn' => '0.5', 'Cr' => '13 –16', 'Nb' => '3.0-6.0', 'Mo' => '3.0-6.0', 'W' => '0.5– 1.5', 'V' => '0.2–1.0', 'Fe' => 'Bal'], '58 - 63', '1000 - 1150', '540 - 600', '75 - 80', '30 - 35%'),
                    ],
                    [
                        'name' => 'WPI CASTALLOYARCO PLATE 201267™',
                        'slug' => 'wpi-castalloyarco-plate-201267',
                        'subtitle' => "C- Cr-alloy in Fe base with complex carbides, Hardness 57- 62 HRC for 'A'+'E' + 'I' at ambient and elevated temp. both",
                        'short' => "It's a Crack free and Smooth Surface, Composite Wear Plates",
                        'description' => "WPI CASTALLOYARCO PLATE 201267™, C- Cr-alloy in Fe base with complex carbides, Hardness 57- 62 HRC for 'A'+'E' + 'I' at ambient and elevated temp. both. It's a Crack free and Smooth Surface, Composite Wear Plates",
                        'specs' => [
                            'Hardfacing MOC | Complex – Rich primary M7C3, Carbide + Ni + W + b Carbide',
                            'Required Thickness | As Specified – 5+3, 6+4, 6+6, 8+4, 8+5, 8+6, 10+4, 10+6, 10+8, 10+10, 12+4, 12+8, and so on',
                            'Operating Temperature | Deg C – Ambient',
                            'Method Of Manufacture | Robotics – Manufacturing By Flux Cored Welding Process Single / Double Layer Formation',
                            'Fixing Arrangement type of liner plate | As Recommended – 1. Stitch welding 2. CSK Hole 3. Plug welding 4. STUD welding (By automatic STUD Gun)',
                            'Wear Factor | Severe – Extreme Abrasion & Impact',
                            'Surface Finish | As Deposited – Ripples With SRC (Stress Reverse Control)',
                        ],
                        'table_title' => 'Bead Properties /Weld Chemistry',
                        'table' => $this->beadTable(['C' => '4.0 – 5.5', 'Si' => '0.5-1.0', 'Mn' => '0.7- 1.0', 'Cr' => '22 - 26', 'Ni' => '2.0 – 3.0', 'W' => '3.0 – 6.0'], '59 - 63', '950 - 1030', '585 - 625', '65 - 70', '25 - 30%'),
                    ],
                ],
            ],

            /* ---------------------------------------------------------------- 2 */
            [
                'name' => 'WPI PTA Platinum-Wear Plates',
                'slug' => 'pta-platinum-wear-plates',
                'icon' => 'fa-solid fa-gem',
                'image' => 'images/categories/ceramics.svg',
                'short' => 'The PLATINUM plate, fabricated with the high performance welding process PTA (Plasma Transferred Arc), is the answer to the increasing demands to improve profitability and productivity in the heavy industry where we are able to apply special Iron based alloys.',
                'description' => '**WPI - PLATINUM - WEAR PLATES:** The PLATINUM plate, fabricated with the high performance welding process PTA (Plasma Transferred Arc), is the answer to the increasing demands to improve profitability and productivity in the heavy industry where we are able to apply special Iron based alloys. Primary carbides in combination with Ni-, Co and Fe based alloys improve the wear resistance significantly, compared with conventionally welded Cr-carbides. High wear resistant Fused Tungsten Carbides (FTC) in combination with a Ni-alloy offer excellent protection against sliding, grooving and rolling wear. The Ni-based alloy offers exceptional properties for temperatures up to 500°C. The PTA process features a metallurgical bond with a dilution limited to 5 - 10%. A further advantage is the fabrication of thin wear plates which can be used to manufacture weight optimized protective components (e.g. fan blades)',
                'products' => [
                    [
                        'name' => 'WPI Yemenite 100440',
                        'slug' => 'wpi-yemenite-100440',
                        'subtitle' => 'PTA + Ni-B-Si +FTC',
                        'short' => 'WPI Yemenite 100440 is a composite hardfacing plate consisting of a weldable steel plate and a wear resistant coating.',
                        'description' => 'WPI Yemenite 100440 is a composite hardfacing plate consisting of a weldable steel plate and a wear resistant coating. The hardfacing layer consists of a Ni-B-Si matrix alloy with embedded Fused Tungsten Carbide (FTC). The Chromium free alloy enables higher hardness compared to the M7C3 type and comprises higher matrix hardness. The incorporated FTC offers a specific hardness larger than 2.340 HV0.1. The low melting point of the matrix alloy, as well as the plasma processing of the composite material, enables a metallurgical bonding to the base material and a minimized dilution. To be applied to sliding, grooving, rolling impact wear; superior properties for temperatures up to 500°C.',
                        'table_title' => 'WPI PTA wear plates',
                        'table' => $ptaTable,
                    ],
                    [
                        'name' => 'WPI Zirconite 120469',
                        'slug' => 'wpi-zirconite-120469',
                        'subtitle' => 'PTA + Ni-B-Si +SFTC',
                        'short' => 'WPI Zirconite 201267 is a composite hardfacing plate consisting of a weldable steel plate and a wear resistant coating similar to the 1061 WP.',
                        'description' => 'WPI Zirconite 201267 is a composite hardfacing plate consisting of a weldable steel plate and a wear resistant coating similar to the 1061 WP. Contrary to the 1061 WP Spherical Fused Tungsten (DURMAT-SFTC), carbide particles are embedded into a Ni-B-Si matrix to achieve a high performance wear resistance coating. SFTC is known as one of the hardest and most wear resistant materials and achieves hardness values over 3000 HV0.1. The low melting point of the matrix alloy, as well as the plasma processing of the composite material enables a metallurgical bonding to the base material and a minimized dilution. To be applied to sliding, grooving, rolling impact wear; superior properties for temperatures up to 500°C.',
                        'table_title' => 'WPI PTA wear plates',
                        'table' => $ptaTable,
                    ],
                    [
                        'name' => 'WPI Partite SCD 58LP',
                        'slug' => 'wpi-partite-scd-58lp',
                        'subtitle' => 'PTA + Ni-Mo-Cr+ FTC',
                        'short' => 'WPI Partite is a composite hardfacing plate consisting of a weldable ferritic or austenitic steel plate and a combined wear and corrosion resistant coating.',
                        'description' => 'WPI Partite is a composite hardfacing plate consisting of a weldable ferritic or austenitic steel plate and a combined wear and corrosion resistant coating. The hardface coating consists of corrosion resistant NiCrMo-alloy reinforced with FTC. The Mo- and Cr matrix alloy allows the application even under corrosion loads up to working temperatures of 600°C. The reinforcing FTC reveals hardness larger than 2,340 HV0.1. By the use of the unique PTA system, a metallurgical bonding to the base material and a minimized dilution is achieved. To be applied to sliding, grooving, rolling impact wear; superior properties for temperatures up to 600°C, good corrosion resistance.',
                        'table_title' => 'WPI PTA wear plates',
                        'table' => $ptaTable,
                    ],
                ],
            ],

            /* ---------------------------------------------------------------- 3 */
            [
                'name' => 'WPI THWPHARDOX (Through Hard Wear Plate)',
                'slug' => 'through-hard-wear-plates',
                'icon' => 'fa-solid fa-shield',
                'image' => 'images/categories/hardox.svg',
                'short' => 'WPI THWPHARDOX (Through Hard Wear Plate) in the range 400, 450, 500 & 600',
                'description' => "WPSVEI developed and registered as a Trade Mark \"WPI THWPHARDOX\" in the range of 400, 450, 500 and 600 which are today proven and replaced Hardox in major clients in core sector industries including L&T, Tata Steel, JSW, AMNS, SUN Flex, Techno Bright, Birla Corporation Cement, Star Cement, KHD and so on. The outstanding features and benefits of \"WPI THWPHARDOX\", are mentioned in our Technical Data Sheet.\n\nWe are keeping a good stock of our \"WPI THWPHARDOX\" range, thickness starting from 6 mm to 40 mm with a standard length of 6000 mm x width of 2000 mm.",
                'products' => [
                    [
                        'name' => 'WPI THWPHARDOX PLATE™ 500 / 400 / 450 / 600',
                        'slug' => 'wpi-thwphardox-plate',
                        'subtitle' => "\"Through Hard Wear Plate\", Hardness 478 to 540 BHN for THWP 500 and so on, for severe 'I' and 'A', widely used in material handling and mining application",
                        'short' => 'CFSSWP is designed to meet strict demands on strength, consistent in quality, smoothness and surface condition.',
                        'description' => "**About CFSSWP**\nCFSSWP is designed to meet strict demands on strength, consistent in quality, smoothness and surface condition. The unique combination of consistently high hardness and excellent impact toughness makes CFSSWP wear plate an appropriate material for a wide range of applications.\nThe high hardness and wear resistance of CFSSWP plate greatly extend the useful life of the product. Due to its high strength, the products made can be still be lighter, and can carry higher payloads. Also, CFSSWP plate has a good resistance against impact also at low temperatures. Its good weld ability and machinability properties simplify its production and repair and patch work for various applications.\n\nHigh smoothness, higher payloads, lower maintenance costs, good availability, and a longer service life combine to provide better overall economy and also assure you give choking free zones.\n\nWPSVEI developed and registered as a Trade Mark \"WPI THWPHARDOX\" in the range of 400, 450, 500 and 600 which are today proven and replaced Hardox in major clients in core sector industries including L&T, Tata Steel, JSW, AMNS, SUN Flex, Techno Bright, Birla Corporation Cement, Star Cement, KHD and so on.\n\nWe are keeping a good stock of our \"WPI THWPHARDOX\" range, thickness starting from 6 mm to 40 mm with a standard length of 6000 mm x width of 2000 mm.",
                        'specs' => [
                            'Types | CFSSWP, Gas Cutting (GC), 300, 350, 400, 450, 500 BHN',
                            'Sizes | All sizes from 2 mm to 210 mm',
                            'Hardness | Quenched and Tempered Hardness 38 to 44 HRC',
                            '0.2% Proof Stress Min. Mpa | 590',
                            'Tensile Strength Min. Mpa | 780 to 980',
                            'Elongation | 11%',
                            'Note | Elongation values are given on the Gauge Length as per IS: 3803 - 1976, i.e. Lo = 5.65 √So 1% elongation values given are applicable to all sizes of sizes of sheets, plates, strips and bars.',
                            'Density (lb/cu.in) | 0.28',
                            'Specific Gravity | 7.7',
                            'Specific Heat | 0.11 (Btu/lb/Deg F - [32-212 Deg F])',
                            'Electrical Resistivity | 330 (Microhm-cm (at 68 Deg F))',
                            'Melting Point (Deg F) | 2650',
                            'Modulus of Elasticity | 29',
                            'Weld Ability | Welding may be performed after preheating to 300-400 F (149-204 C) with post weld tempering at temperatures for 2 hours. Filler metal should be AWS E/Er - 309 or equivalent.',
                            'Sizes | Available in all sizes and custom shape as desired by user.',
                        ],
                        'table_title' => 'Chemical Properties',
                        'table' => ['C | Mn | Cr | Al | Si | S | P | Nb+V+Ti', '0.23% | 1.6% | 0.65% | 0.10% | 0.50% | .50% | 0.050% | 0.15% (max)'],
                        'featured' => true,
                    ],
                    [
                        'name' => 'BREAKER PLATE - WPI THWPHARDOX 400®',
                        'slug' => 'breaker-plate-wpi-thwphardox-400',
                        'subtitle' => '40 MM THICK | Registered Trade Mark - Govt. of India | Crusher Impact Liner',
                        'short' => 'Premium Cash Job - Blue Epoxy - CNC Slotted - 40MM Composite - WPSYNERGY Brand',
                        'description' => '® Registered Trade Mark under Govt. of India - Brand Owned by Wear Plates Synergy VE India - Since 2008',
                        'specs' => [
                            'Product Used | WPI THWPHARDOX 400® - 40 MM THICK - Registered Trade Mark - Govt. of India',
                            'Brand Owner | Wear Plates Synergy VE India - Since 2008 - ISO & MSME Certified Company',
                            'Trade Mark Status | ® Registered Brand - Product: WPI THWPHARDOX 400® - 40mm Composite Plate',
                            'Construction | WPI THW Overlay 10mm (60-62 HRC) + PHARDOX 400 Base 30mm = 40mm Total Thick',
                            'Hardness | Dual - Face 60-62 HRC abrasion resistant + Base 400 BHN impact resistant - tested',
                            'Profile | 3D Curved to crusher shell radius + Long slots 150x25mm + Holes dia 22mm - CNC cut',
                            'Cutting | CNC Plasma + CNC Bending + CNC Drilling - 0.3mm tolerance, zero crack, no delamination',
                            'Bonding | WPI THW hardfaced on PHARDOX 400 - vacuum brazed, certified - TM product only',
                            'Paint | RAL 5005 Blue Epoxy - 80 microns, anti-rust, white edge marking for QA - WPSYNERGY',
                            'Size | 1250 x 650 x 40mm thick - Custom as per Puzzolana / Metso / Sandvik / Propel crushers',
                            'Life | 3X vs MS - 400 BHN resists boulder impact + 62 HRC resists sand abrasion - TM quality',
                            'Application | Jaw / Cone / VSI / Hammer Crusher - Cement, Iron Ore, Coal, Mining - OEM approved',
                            'Stock | Ready 4 nos / 8 nos set - GIDC Ankleshwar - 24 hrs dispatch - Trade Mark genuine product',
                            'QC | Hardness + DP test on slots + Dimensional report + Trade Mark Certificate - 100% traceable',
                            'Cash Value | Highest margin - Brand premium - 100% advance - Repeat every shutdown - ® Protected',
                        ],
                        'featured' => true,
                    ],
                ],
            ],

            /* ---------------------------------------------------------------- 4 */
            [
                'name' => 'WPI WEAR TUBE™',
                'slug' => 'wear-tubes-and-lined-pipes',
                'icon' => 'fa-solid fa-circle-dot',
                'image' => 'images/categories/coatings.svg',
                'short' => "WPI WEAR TUBE™ with ABRASODUR ALLOY, SDIHFTP- Small Diameter Internally Hard Faced Tube / Pipe with C- Cr-alloy in Fe base with complex carbides, Hardness 57 - 62 HRC for 'A'+'E'+ 'mI' at ambient and elevated temp. Both",
                'description' => 'The product developed called WPI WEAR TUBE, basically seamless internally hard-faced pipe starting from 100 NB, a detailed leaflet attached herewith for your kind reference. It is meant for handling abrasive media through its internal circular section. It has got wider application base in your plant especially in RMHS, SP, BF, LD, for handling raw material, charge mix, cold and hot sinter and Ferrochrome, lime, Ash handling etc. Here in, we would like to draw your kind attention immediately on the application in particular in LD, that is Flux Charging Chute, we can even supply the entire length of almost 28 m with all branches along with expansion joints as per your design, we shall look for an immediate opportunity to serve with again the value engineering concept in mind i.e. the pipe below expansion joint would be of Hot Hardness Resistance overlaying. The Pic. Given below speaks about our workmanship & Quality of Finished Goods at Synergy Plant.',
                'products' => [
                    [
                        'name' => 'WPI WEAR TUBE™ with ABRASODUR ALLOY',
                        'slug' => 'wpi-wear-tube',
                        'subtitle' => "C- Cr-alloy in Fe base with complex carbides, Hardness 57 - 62 HRC for 'A'+'E'+ 'mI' at ambient and elevated temp. Both",
                        'short' => 'The product developed called WPI WEAR TUBE, basically seamless internally hard-faced pipe starting from 100 NB',
                        'description' => 'The product developed called WPI WEAR TUBE, basically seamless internally hard-faced pipe starting from 100 NB, a detailed leaflet attached herewith for your kind reference. It is meant for handling abrasive media through its internal circular section. It has got wider application base in your plant especially in RMHS, SP, BF, LD, for handling raw material, charge mix, cold and hot sinter and Ferrochrome, lime, Ash handling etc. Here in, we would like to draw your kind attention immediately on the application in particular in LD, that is Flux Charging Chute, we can even supply the entire length of almost 28 m with all branches along with expansion joints as per your design, we shall look for an immediate opportunity to serve with again the value engineering concept in mind i.e. the pipe below expansion joint would be of Hot Hardness Resistance overlaying.',
                        'specs' => ['Capacity | 5000 metre of WPI WEAR TUBE per annum', 'Pipe Cladding Machine | Pipe Cladding machine – TI, India – 200 Mtr/Month'],
                        'featured' => true,
                    ],
                    [
                        'name' => 'Small Diameter Internally Hard Faced Tubes/Pipes',
                        'slug' => 'small-diameter-internally-hard-faced-tubes',
                        'subtitle' => 'SDIHFTP',
                        'short' => 'Tube with a minimum diameter of 75 mm are available, with a maximum length of 3m.',
                        'description' => "Tube with a minimum diameter of 75 mm are available, with a maximum length of 3m.\nTubes can be fitted with standard flanges so that the replacement of worn out tubes can be done quickly and easily.\nVirtually any inner diameter from the 75 - 300 mm range can be produced by applying a different wearfacing thickness inside the standard base tube. This allows a close mating joint to the existing installation thus avoiding turbulent media flow.\n\nSDIHFTP offers a comprehensive of solutions for virtually all wear-intensive tube systems in industries like",
                        'highlights' => ['Flanged Joints: SDIHFTP are available with slip-on or integral flanges in accordance with DIN standards.', 'Other types of flange joints are also available upon request.'],
                        'applications' => ['Mines and quarries', 'Sand and gravel pits', 'Shredders and recycling plants', 'Iron and steel mills', 'Cement and ready - mix concrete plants', 'Refinery & Fertilizer', 'Foundries and coking plants', 'Incineration plants and electric power stations'],
                    ],
                    [
                        'name' => 'BULK DISPATCH – COMPOSITE LINED PIPES',
                        'slug' => 'composite-lined-pipes-elbows-reducers',
                        'subtitle' => '20-25 Pipes Per Truck | Ready Painted | Pan India Delivery',
                        'short' => 'Iconic Cash Proof Since 2024 - Not Job Work, OEM Supply',
                        'description' => 'FULL TRUCK DISPATCH – BULK',
                        'specs' => [
                            'Product | Composite Lined Pipes + Elbows + Reducers – Full Lot',
                            'Quantity | 20-25 pipes + 10 fittings per truck – 100M per order',
                            'Lining | CCO / Abrasodur 6+4mm inside – 62 HRC, smooth finish',
                            'Outer | MS shell + Red Oxide Primer – ready to install',
                            'Packing | Wooden pallets + End caps – no damage in transit',
                            'Delivery | GIDC Ankleshwar to Chittorgarh, Hyderabad, Noida in 3 days',
                            'Proof | Photo of loaded truck – customers trust bulk capability',
                            'Application | Cement, power, steel – shutdown replacement',
                            'Stock | Ready stock 50M – urgent breakdown in 24 hrs dispatch',
                            'QC | Each pipe number punched + hardness report attached',
                            'Cash Note | Fastest cash – advance 50%, balance before dispatch',
                        ],
                    ],
                    [
                        'name' => 'SPRAY NOZZLE RING MANIFOLD - CEMENT PLANTS',
                        'slug' => 'spray-nozzle-ring-manifold',
                        'subtitle' => 'Gas Conditioning Tower | Kiln Cooling | Cooler Spray | Wear Tube + Abrasodur CCO Lined',
                        'short' => 'Premium Cash Job - Wear Tube Internally Wear Faced by Abrasodur (CCO) - Vertical Pipe Full - WPSYNERGY®',
                        'description' => '® Registered Trade Mark - Brand Owned by Wear Plates Synergy VE India - Since 2008 - WPI THWPHARDOX 400® Family',
                        'specs' => [
                            'Job Name | Spray Nozzle Ring / Quench Ring / Cooling Ring Manifold - For Cement Kiln GCT & Cooler - Vertical Full',
                            'Photo Job | COMPLETE RING + VERTICAL PIPE FULLY VISIBLE - Flanged Elbow Inlet - Full Circle - 28 Nozzles - CUT BOTTOM',
                            'MOC - CORRECTED FINAL | Wear Tube (Heavy Duty) - Internally Wear Faced by Abrasodur (CCO) Overlay - 6mm CCO Lining',
                            'Overlay Spec | Abrasodur - Chromium Carbide Overlay (CCO) - 60-65 HRC - Inside full bore - Anti-Abrasion + Erosion Proof',
                            'Construction | Main Ring 150 NB Wear Tube + 200 NB Cross Header + Vertical Inlet 100 NB Elbow - All internally CCO lined',
                            'Vertical Pipe | Fully Visible - 90° Elbow with 150# Flange - ANSI B16.5 Drilled - Wear Tube + CCO lined - Ready to bolt',
                            'Nozzles | 3/4 inch Half Couplings x 28 Nos - Angled 30° - Full penetration TIG welded - CCO protected - Uniform spray',
                            'Welding | 100% TIG Root + MIG Cover - All joints DP Tested - Zero crack - Zero leakage - CCO intact - ISO Workshop',
                            'Inlet | Vertical 4 inch / 6 inch Flanged Elbow - Top entry - Easy maintenance - Gasket + Bolts included - GCT proven',
                            'Function | Water spray with abrasive dust / slurry - Gas cooling / dust suppression / kiln shell / cooler spray - Cement',
                            'Advantage | Wear Tube + Abrasodur CCO Inside - Life 3X vs MS pipe - No internal erosion - No choking - No flange leak',
                            'Fabrication | Segmented bending + Fixture + Internal CCO overlay after bending + Vertical pipe alignment <1mm - Full Ring',
                            'Application | UltraTech / Ambuja / ACC / Shree / Dalmia / JK / Chettinad - GCT / Kiln / Cooler / Bag House - High abrasion',
                            'QC | Hydrotest 10 kg/cm² + DP Test + CCO Thickness Check + Dimensional + Circularity + Flange flatness - ISO Certified',
                            'Dispatch | Made to order - GIDC Ankleshwar - 72 Hrs - Wooden pallet packing - Pan India 24 Hrs - WPSYNERGY® Brand',
                            'Brand Owner | Wear Plates Synergy VE India - Since 2008 - ISO & MSME - ® Registered Trade Mark Owner - WPI THWPHARDOX Family',
                        ],
                        'featured' => true,
                    ],
                ],
            ],

            /* ---------------------------------------------------------------- 5 */
            [
                'name' => 'Structural Fabrication with Wear Plates',
                'slug' => 'wear-resistant-fabrications',
                'icon' => 'fa-solid fa-industry',
                'image' => 'images/categories/hardox.svg',
                'short' => 'We would like to place few iconic jobs which would explain our capabilities in the field of Structural Fabrication with Wear Plates:',
                'description' => 'We at Wear Plates Synergy VE India, GIDC Ankleshwar, Gujarat, are completely engaged in combatting the wear and tear related structures with liners fitted or without, as per the drawing and with all specifications. We aim to provide the total solutions for all possible structural fabrication jobs through our expertise in weld metallurgy, wear protection and the best practices in Total Quality Management.',
                'products' => [
                    ['name' => 'GRIT CONE', 'slug' => 'grit-cones', 'short' => 'We have developed our own expertise in manufacturing high capacity GRIT CONE', 'description' => 'We have developed our own expertise in manufacturing high capacity GRIT CONE, here we have shown below, one such CONE from Cement Plant weighing 13 ton and other one is for Steel Plant weighing 16 ton. Both have finally met the high level of satisfaction of End Users.', 'featured' => true],
                    [
                        'name' => 'HEAVY-DUTY CONICAL HOPPER / SILO FABRICATION',
                        'slug' => 'conical-hoppers-silo-cones',
                        'subtitle' => 'Premium Wear Solutions — Heavy Duty Fabrication',
                        'short' => 'Large Dia Cone Fabrication, Rolling, Welding & Liner Fitting',
                        'description' => 'ICONIC PROJECT SHOWCASE – PROJECT: HEAVY-DUTY CONICAL HOPPER / SILO FABRICATION',
                        'specs' => [
                            'Client Sector | Cement / Mining / Power - Bulk Material Handling',
                            'Material Used | Abrasodur 400 / 500 Wear Plate + MS Structure (12+4mm)',
                            'Job Type | Large Dia Cone Fabrication, Rolling, Welding & Liner Fitting',
                            'Dimensions | ~3.5M Dia Bottom, Heavy Conical Body with Access Hatch',
                            'Year | 2024-25',
                            'Location | GIDC Ankleshwar Fabrication Yard',
                            'Challenge | High abrasion zone, heavy impact, seamless conical rolling',
                        ],
                    ],
                    ['name' => 'The Rotor', 'slug' => 'heavy-rotors-structural-fabrication', 'short' => 'We have fabricated The Rotor having self-weight of 16 ton', 'description' => 'We have fabricated The Rotor having self-weight of 16 ton which is a typical unit undergone Static Balancing and Dynamic Balancing, third party inspection finally qualified and led to Customer Satisfaction.'],
                    ['name' => 'The Project of a Cement Plant', 'slug' => 'cement-plant-structures', 'short' => 'The Project of a Cement Plant, right from the Crusher House to Packing Plant', 'description' => 'The Project of a Cement Plant, right from the Crusher House to Packing Plant as per the drawing of all technological structures, weighing around 75 tons, we have completed and delivered from our Plant on schedule. Today we geared up to deliver a minimum quantity of 60 tons per month.'],
                ],
            ],

            /* ---------------------------------------------------------------- 6 */
            [
                'name' => 'Maintenance & Fabrication Welding Electrodes',
                'slug' => 'welding-consumables',
                'icon' => 'fa-solid fa-bolt',
                'image' => 'images/categories/electrodes.svg',
                'short' => 'A full gamut of Low Heat Input Maintenance Welding Electrodes under the Brand Name : WPI Eutectic-Uni-Alloys (EUA), make "WPSVEI"',
                'description' => "A full gamut of Low Heat Input Maintenance Welding Electrodes under the Brand Name : **WPI Eutectic-Uni-Alloys (EUA)**, make \"WPSVEI\"\n\nTechnical knowhow of life enhancement in service performance of critical industrial components using patented Kladex FCAW and Metal core wire alloy formulations.",
                'products' => [
                    ['name' => 'SPECIAL WELDING WIRES', 'slug' => 'special-welding-wires', 'short' => 'Technical knowhow of life enhancement in service performance of critical industrial components using patented Kladex FCAW and Metal core wire alloy formulations.', 'description' => 'Technical knowhow of life enhancement in service performance of critical industrial components using patented Kladex FCAW and Metal core wire alloy formulations.', 'featured' => true],
                    [
                        'name' => 'Wear Plates –Kladex (Steel Mill roll reconditioning) services',
                        'slug' => 'kladex-roll-cladding-consumables',
                        'short' => 'Wear plates together with their Associated workshop of M/s Metal Form and unique Kladex range of consumable brings comprehensive solutions for various steel mill rolls and other components',
                        'description' => "Wear plates together with their Associated workshop of M/s Metal Form and unique Kladex range of consumable brings comprehensive solutions for various steel mill rolls and other components\n\nWear Plates offers international references of respective to our Wear Plate products and services including HSM of the use of Kladex wires on above rolls with performance data and chemistry to counter hi friction /pressure /temp exposure and hardness requirements.",
                        'table_title' => 'Steel Mill roll reconditioning',
                        'table' => [
                            'Application | WA Wires | Weld Clad | Kladex',
                            'Table rolls | Chromecore 420M (420+1Mo), Chromecore 414MM, Chromecore 414N-O (Insitu), Chromecore 4142000 | Weld clad 8 | Kladex M10 G/S',
                            'Scale Breaker Rolls | Chromecore 420M (420+1Mo), Chromecore DN, Chromecore 414-2000 | Weldclad 4 | Kladex M20 G/S',
                            'Roughing Mills & Vertical Edger rolls, Casing | Hardface 503-S, Hardface 401HS-S, Hardface C-S, Hardface 51Nb-S | Weldclad 4, Weldclad 3 | Kladex M22 G/S',
                            'Roughing stand Coupling Boxes | Hardface AP-O | Weldclad 17, Weldclad 4 | ',
                            'ROLL JOURNALS | Hardface |  | ',
                            'Looper roll | DN, 414-2000, 414N, Dualhard C-S |  | Kladex N2',
                            'Run out table rolls | DN, 414N, 414MM |  | ',
                            'Pinch and wrapper rolls | 414-2000/414-Pinch/414-Wrapper, Dualhard C-S, Hardface W-S | Weldclad 17 | Kladex N2',
                            'Coil box rolls | Hardface DCO, DN, 414-2000, Dualhard C-S | Weldclad 4 | Kladex M22 G/S',
                            'Back Up rolls | Backup-S |  | Kladex M30 G/S',
                            'Tube forming rolls | DN |  | ',
                        ],
                    ],
                    ['name' => 'WPI Eutectic-Uni-Alloys (EUA)', 'slug' => 'wpi-eutectic-uni-alloys-electrodes', 'short' => 'A full gamut of Low Heat Input Maintenance Welding Electrodes under the Brand Name : WPI Eutectic-Uni-Alloys (EUA), make "WPSVEI"', 'description' => 'A full gamut of Low Heat Input Maintenance Welding Electrodes under the Brand Name : WPI Eutectic-Uni-Alloys (EUA), make "WPSVEI"'],
                ],
            ],

            /* ---------------------------------------------------------------- 7 */
            [
                'name' => 'Special Alloys and Unique Metals',
                'slug' => 'carbides-ceramics-castings',
                'icon' => 'fa-solid fa-cubes',
                'image' => 'images/categories/castings.svg',
                'short' => $special,
                'description' => $special,
                'products' => [
                    ['name' => 'Tungsten Carbide', 'slug' => 'tungsten-carbide', 'short' => $special, 'featured' => true],
                    ['name' => 'Silicon Carbide', 'slug' => 'silicon-carbide', 'short' => $special],
                    ['name' => 'Castings', 'slug' => 'castings', 'short' => $special],
                    ['name' => 'Rich Cr-C Alloys', 'slug' => 'rich-cr-c-alloys', 'short' => $special],
                    ['name' => 'Ceramics', 'slug' => 'ceramics', 'short' => $special],
                    ['name' => 'Cast Basalt', 'slug' => 'cast-basalt', 'short' => $special],
                ],
            ],

            /* ---------------------------------------------------------------- 8 */
            [
                'name' => 'Thermal Insulation & Corrosion Resistant Coatings',
                'slug' => 'coatings-surface-engineering',
                'icon' => 'fa-solid fa-temperature-high',
                'image' => 'images/categories/coatings.svg',
                'short' => 'Coatings for Thermal Insulation and Corrosion Resistance at High Temperature.',
                'description' => "Coatings for Thermal Insulation and Corrosion Resistance at High Temperature.\n\nAll welding processes including SMAW, GMAW, GTAW with Plasma and HVOF powder spray facility and expertise.",
                'products' => [
                    ['name' => 'Thermal Insulation & Corrosion Resistant Coatings', 'slug' => 'thermal-insulation-corrosion-resistant-coatings', 'short' => 'Coatings for Thermal Insulation and Corrosion Resistance at High Temperature.'],
                    ['name' => 'Thermal Metal powder Spray coating of shafts', 'slug' => 'thermal-spray-hvof-coatings', 'short' => 'Thermal spray metal coating on sugar mill shaft, bearing area & machining', 'applications' => ['Thermal Metal powder Spray coating of shafts', 'Thermal spray metal coating on sugar mill shaft, bearing area & machining', 'HVOF powder spray facility and expertise']],
                    ['name' => 'Surface coating engineering on valves, valve seats & stem shaft', 'slug' => 'weld-overlay-cladding', 'short' => 'Surface coating engineering on valves, valve seats & stem shaft by using Stellite weld overlay, inconel overlay, Hastelloy, Colmonoy & Tungsten Carbide overlay', 'description' => 'Surface coating engineering on valves, valve seats & stem shaft by using Stellite weld overlay, inconel overlay, Hastelloy, Colmonoy & Tungsten Carbide overlay by using various process such as thermal spray, GTAW welding, SMAW welding & MIG welding including machining & grinding services.', 'applications' => ['Inconel & Stellite weld overlay on pressure vessel nozzles', 'Weld Repair & overhauling of various valves']],
                ],
            ],
        ];
    }
}
