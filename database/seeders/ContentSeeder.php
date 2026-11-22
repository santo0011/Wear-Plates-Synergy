<?php

namespace Database\Seeders;

use App\Models\PageSection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

/**
 * Website page content – taken WORD FOR WORD from the client's company profile PDF.
 * Verbatim source text: docs/PDF_TRANSCRIPT.txt (only pure spelling slips corrected, listed there).
 * Short design labels (small eyebrow labels, button texts) are not part of the PDF.
 * Images are intentionally empty: they are uploaded from the admin panel.
 *
 * Existing sections are left untouched unless CMS_SEED_OVERWRITE=1 is set:
 *   CMS_SEED_OVERWRITE=1 php artisan db:seed --class=ContentSeeder
 */
class ContentSeeder extends Seeder
{
    /** Home hero: three short slides (eyebrow, title, text, image, button text, button link). */
    public const HERO_SLIDES = [
        ['Since 2008 | ISO Certified | MSME Registered', 'The Leader In Wear Protection & **Maintenance Welding Technology**', null, null, 'Our Products & Solutions', '/products'],
        ['Expertise to Lead, Lead to The Bench Marking', 'Leading Through Designing **New Products & Development**', 'Comprehensive welding cladding and rebuilding services to the core sector industries.', null, 'Company Profile', '/about-us'],
        ['"Valued Engineering" VE', 'The right material at **the right place**', 'Higher LPF i.e. life prolonging factor and that brings out the R.O.I.', null, 'Why Choose Us', '/why-choose-us'],
    ];

    public function run(): void
    {
        $overwrite = (bool) env('CMS_SEED_OVERWRITE', false);

        foreach ($this->content() as $page => $sections) {
            foreach ($sections as $section => $content) {
                $row = PageSection::firstOrNew(['page' => $page, 'section' => $section]);
                if ($row->exists && ! $overwrite) {
                    continue;
                }
                // Keep any images already uploaded when overwriting text.
                $row->content = $row->exists ? $this->keepImages($content, $row->content ?? []) : $content;
                $row->is_active = $row->exists ? $row->is_active : true;
                $row->save();
            }
        }

        Cache::forget(PageSection::CACHE_KEY);
    }

    /** Carry over uploaded image paths from the old content (same keys / same item index). */
    private function keepImages(array $new, array $old): array
    {
        foreach ($new as $key => $value) {
            if (is_array($value) && is_array($old[$key] ?? null)) {
                foreach ($value as $i => $item) {
                    if (is_array($item) && is_array($old[$key][$i] ?? null)) {
                        $new[$key][$i] = $this->keepImages($item, $old[$key][$i]);
                    }
                }
            } elseif ($value === null && is_string($old[$key] ?? null) && str_starts_with($old[$key], 'uploads/')) {
                $new[$key] = $old[$key];
            }
        }

        return $new;
    }

    private function rows(array $keys, array $rows): array
    {
        return array_map(fn ($r) => array_combine($keys, array_pad($r, count($keys), null)), $rows);
    }

    private function content(): array
    {
        return [
            'home' => $this->home(),
            'about' => $this->about(),
            'leadership' => $this->leadership(),
            'products' => $this->products(),
            'industries' => $this->industries(),
            'infrastructure' => $this->infrastructure(),
            'quality' => $this->quality(),
            'projects' => $this->projects(),
            'resources' => $this->resources(),
            'why' => $this->why(),
            'presence' => $this->presence(),
            'contact' => $this->contact(),
        ];
    }

    /* ===== Verbatim paragraphs used on more than one page ===== */
    private const ABOUT_US = 'The leader in wear protection that includes Wear Liner, Parts, Components, Standard Plates, Hardox - Fabrications, Ceramics, Tungsten Carbide, Alloys Castings and Maintenance & Fabrication Welding Electrodes, Coatings for Thermal Insulation and Corrosion Resistance at High Temperature.';

    private const PROFILE_INTRO = 'Wear Plates Synergy VE India (WPSVEI) have taken organic leap to offer comprehensive **welding cladding and rebuilding services** to the core sector industries. The noble purpose behind this is to provide one stop comprehensive manufacturing unit of Wear Plates, having an inbuilt production capacity of 11000 sq.m per annum and beside that an exclusive initiative by providing welding solution to our esteemed customers, by adding sophisticated well equipped Associated work shop facility of **M/s Metal Form Mumbai** and International collaboration of various alloy formulation from our partner **Kladex UK.**';

    private const WEAR_PLATES_BRINGS = "The state of art WEAR PLATE manufacturing unit based at Ankleshwar dedicatedly working for core sector industries like Steel, Cement, Mining, Construction, Power, Sugar and Engineering across PAN India and abroad since 2009-10 FY.\nWell-equipped Engineering Work shop – with Machining & Cladding Facility for Steel, Cement and Power sector.\nAPI approved facility for critical Oil and Gas jobs & services\nHighly experienced management and supervisory team with more the 25 year of experience with L&T Welding Products Business, Reliance Industries and Welding Alloys UK in job execution\nTechnical knowhow of life enhancement in service performance of critical industrial components using patented Kladex FCAW and Metal core wire alloy formulations.";

    private const ACTIVITIES = 'Our activities, we manufacture wear plates, one of the Start-up manufacturing units, focusing on Wear Protection Market primarily in India though we export. We have presently a capacity of manufacturing 1100 sq.m.per month of Crc based Composite Weld Cladded Wear Plates, beside that we have an unique pipe cladding machine by which we can internally hard face small diameter seam less tube.';

    private const WE_AT = 'We at Wear Plates Synergy VE India, GIDC Ankleshwar, Gujarat, are completely engaged in combatting the wear and tear related structures with liners fitted or without, as per the drawing and with all specifications. We aim to provide the total solutions for all possible structural fabrication jobs through our expertise in weld metallurgy, wear protection and the best practices in Total Quality Management.';

    private const VALUED_ENGINEERING = 'Our simple and very effective approach for total solutions to our customers are based on value propositions and it is driven through a concept called "Valued Engineering" VE is nothing but a concept by using the right material at the right place which will finally lead to higher LPF i.e. life prolonging factor and that brings out the R.O.I.';

    private const OWN_PLANT = 'Wear Plates Synergy VE India have got its own a state of art manufacturing plant with machines embedded with latest technology, presently our capacity of 11000 sq.m. of WPI ABRASODUR (Crc) weld cladded wear plates and 5000 metre of WPI WEAR TUBE and 700 ton of Structural Fabrication per annum.';

    private const SPECIALITY = '**Our Speciality in producing Wear Resistant Plates both in CrC and QT range which are proven to be the best in handling Wear Prone Application in core sector industries like Steel, Cement, Power, Mining & Construction by enhancing the performance in terms of equipment availability for production and that finally leads to a highly convincing rather an attractive equation of LPF/LOI**';

    private const CONCEPT = 'The deposit belongs to the hyper Eutectic group. The behavior against wear is obtained from precipitated phases during the solidification of the deposits. The precipitation of the primary carbides of Chromium-Niobium complex carbides and Borides yield very high micro hardness in Wear coating, thus providing exceptionally high wear resistance making the plates very good for abrasion, erosion & impact.';

    private const PROCESS = 'Wear Plates "WPI ABRASODUR" are manufactured by fully automated-tri axial linear robot, depositing the various thickness of Wear coating on the base plate. This ensures accurate arc gap, correct angle of feed, uniform travel speed, speedy ripple, minimum dilution and uniform distribution of carbides, that helps to achieve uniform and homogeneous quality coating.';

    private const WORKING = 'Working with "WPI ABRASODUR" is very simple. The plates can be easily cut by plasma cutting machine and can be rolled in to radius, thanks to the mild steel base. Different fixing techniques make "WPI ABRASODUR" versatile for many applications.';

    private const APPLICATIONS = 'Mixer Liners, Crusher side Liners, Chutes, Dip Tube in Cyclone, Grit Cone, Ball Mill Inlet Feeder Chute, Cement Mill Inlet Cone without Screw, Cement Mill Valve (Half Open) Vertical Mill (VRM), Clinker Silo Outlet Cone, Pipes, Casing and Ventilator of Air Separator, Pipelines for Clinker Transport etc.';

    private const CUSTOMERS = 'We are delighted to share that we have been awarded the best start-up company in 2020 and we have already got a national and international customer base namely JSW, AMNS, TSL, Sun Flag, Qatar Steel, Tata Power, KHD, L&T, Reitz India, Loesche, Prism Cement, Sanghi Cement Digvijay Cement, India Cement, Ramco, Orient Cement, Shree Cement, Webber TLT and so on. We at Synergy Alloys we do emphasize the basic tools in our business practices in the highest degree and that is the quality, competitive pricing and delivery on time.';

    private const PPA = 'Our Product Spectrum which is a choice of renowned industrial houses today since all of our products and their services in core sector industries have been recognised to be very effective with an outstanding performance.';

    private const OPEN_TO_JOBS = "We are quite open and same time we are ready to join you in working together on any challenging Structural Fabrication Jobs especially in the area of the Wear Protection mainly for life enhancement programme of major machine components and equipment's as it prominently mentioned in our communication.";

    private const DIRECT_ATTENTION = 'We would like to get your direct attention especially on those applications we already have been handling with and on the top of it we would like to take up more challenging and higher capacity oriented jobs in structural fabrication. We have our young dynamic manufacturing team under the guidance of highly experienced leadership, is always ready to serve you by meeting up to your satisfaction level.';

    private const TRIAL_GROUND = 'As mentioned above, here we need also a trial ground to testify the muscle of our facility, product and services. We have outlined briefly our strength in handling structural fabrications jobs. We are confident that you will be able to witness an improvement in terms of handling heavy fabrication jobs by using our reliable product and processes.';

    private const KEY_JOBS = 'Key Jobs and services industry wise successfully undertaken by Synergy Alloys & Metal form and ready to serve on all India basis';

    private const ICONIC = 'We would like to place few iconic jobs which would explain our capabilities in the field of Structural Fabrication with Wear Plates:';

    private const EXPANSION = 'Driving the Capacity Expansion in 2026 with new 6KW Fiber Laser, Committed to Certified Materials with the Maximum Safety Margin to acquire the higher LPF, proving the fact of committed warranty to simplify the equation of Viability';

    private const ASTM = 'As materials are being developed, so are the techniques to measure wear resistance. Thirty years ago, very few reliable tests were available to actually measure wear. Now, many manufacturers and vendors, routinely employ standard wear tests, such as the ASTM G-65 as part of their Quality Assurance programs. These standard test results are also becoming an integral part of Original Equipment specifications.';

    private const DELIVERY = 'We at Synergy Alloys we do emphasize the basic tools in our business practices in the highest degree and that is the quality, competitive pricing and delivery on time.';

    private const FACILITY_LIST = [
        ['fa-solid fa-user-tie', '25 year plus experienced management and supervisory team'],
        ['fa-solid fa-user-gear', '6 G and Automation welding trained certified welders'],
        ['fa-solid fa-warehouse', '20,000 Sq feet covered work place'],
        ['fa-solid fa-gears', '30 feet, 12 feet and 8 feet lathe, 20 Ton Hydraulic press.'],
        ['fa-solid fa-spray-can-sparkles', 'Twin wire metal spray system'],
        ['fa-solid fa-circle-notch', 'Single & Twin wire roll weld cladding system'],
        ['fa-solid fa-rotate', '2 ton, 5 ton positioners and 30 ton rotators'],
        ['fa-solid fa-arrows-up-down', '20 Ton crane capacity'],
        ['fa-solid fa-tower-observation', 'Column and boom'],
        ['fa-solid fa-fire', 'All welding processes including SMAW, GMAW, GTAW with Plasma and HVOF powder spray facility and expertise.'],
        ['fa-solid fa-robot', 'X, Y, Z Axis specialized semi-automatic welding systems (Mill –Kits) –with 600 AMPs CC /CV power source capable of Open arc-FCAW, Gas shielded GMAW and solid wire and flux SAW process, offering wide Variety of alloy selection for any critical weld over lay and joining job challenge.'],
    ];

    /* =================================================================== HOME */
    private function home(): array
    {
        return [
            'hero' => [
                'slides' => $this->rows(['eyebrow', 'title', 'text', 'image', 'button_text', 'button_link'], self::HERO_SLIDES),
            ],
            'trust' => [
                'items' => $this->rows(['icon', 'title', 'text'], [
                    ['fa-solid fa-calendar-check', 'Since 2008', 'Serving India Since 2008'],
                    ['fa-solid fa-certificate', 'ISO Certified', 'ISO 9001:2015'],
                    ['fa-solid fa-building-circle-check', 'MSME Registered', 'UDYAM-GJ-06-0058021'],
                    ['fa-solid fa-award', 'The Leader in Wear Protection', 'The expertise to lead, Lead to the bench marking.'],
                    ['fa-solid fa-truck-fast', 'Blue Dart | DHL', 'Fast Delivery from Ankleshwar Plant Through Air & Surface Transports.'],
                ]),
            ],
            'about' => [
                'eyebrow' => 'About Us',
                'heading' => 'WEAR PLATES SYNERGY **VE INDIA**',
                'text' => self::ABOUT_US."\n\n".self::ACTIVITIES."\n\n".self::WE_AT,
                'points' => self::WEAR_PLATES_BRINGS,
                'image' => null,
                'image_2' => null,
                'badge_value' => '2008',
                'badge_label' => 'Since',
                'button_text' => 'Company Profile',
            ],
            'stats' => [
                'eyebrow' => 'Capacity',
                'heading' => 'Presently Our **Capacity**',
                'items' => $this->rows(['value', 'suffix', 'label', 'icon'], [
                    ['11000', 'sq.m.', 'WPI ABRASODUR (Crc) weld cladded wear plates per annum', 'fa-solid fa-layer-group'],
                    ['5000', 'metre', 'WPI WEAR TUBE per annum', 'fa-solid fa-circle-dot'],
                    ['700', 'ton', 'Structural Fabrication per annum', 'fa-solid fa-industry'],
                    ['20000', 'Sq feet', 'covered work place', 'fa-solid fa-warehouse'],
                    ['60', 'tons', 'minimum quantity per month', 'fa-solid fa-truck-ramp-box'],
                    ['25', '+', 'year plus experienced management and supervisory team', 'fa-solid fa-user-tie'],
                ]),
            ],
            'products' => [
                'eyebrow' => 'Product Spectrum',
                'heading' => 'Our Products & **Solutions**',
                'text' => self::PPA,
                'note' => null,
            ],
            'value_engineering' => [
                'eyebrow' => 'Valued Engineering',
                'heading' => '"Valued Engineering" **VE**',
                'text' => self::VALUED_ENGINEERING,
                'steps' => $this->rows(['icon', 'title', 'text'], [
                    ['fa-solid fa-atom', 'THE CONCEPT:', self::CONCEPT],
                    ['fa-solid fa-arrow-trend-up', 'Our Speciality', str_replace('**', '', self::SPECIALITY)],
                ]),
                'image' => null,
            ],
            'industries' => [
                'eyebrow' => 'Key Jobs',
                'heading' => 'Key Jobs and Services **Industry Wise**',
                'text' => self::KEY_JOBS,
                'note' => null,
            ],
            'process' => [
                'eyebrow' => 'The Process',
                'heading' => 'THE **PROCESS**',
                'text' => self::PROCESS,
                'steps' => $this->rows(['icon', 'title', 'text'], [
                    ['fa-solid fa-scissors', 'THE WORKING:', self::WORKING],
                    ['fa-solid fa-list-check', 'Applications', self::APPLICATIONS],
                    ['fa-solid fa-ruler-combined', 'Our Standard Plate', 'Our Standard Plate of 3000 x 1350 mm, of various composite thicknesses.'],
                    ['fa-solid fa-gauge-high', 'Production Capacity', 'Production Capacity : 1100 Sq.m. per month and 13200 sq.m. per annum'],
                ]),
                'image' => null,
            ],
            'projects' => [
                'eyebrow' => 'Iconic Jobs',
                'heading' => 'Structural Fabrication with **Wear Plates**',
                'text' => self::ICONIC,
                'note' => null,
            ],
            'clients' => [
                'eyebrow' => 'Customer Base',
                'heading' => 'National and International **Customer Base**',
                'text' => self::CUSTOMERS,
                'clients' => $this->rows(['name', 'logo'], array_map(fn ($n) => [$n], [
                    'JSW', 'AMNS', 'TSL', 'Sun Flag', 'Qatar Steel', 'Tata Power', 'KHD', 'L&T', 'Reitz India', 'Loesche',
                    'Prism Cement', 'Sanghi Cement', 'Digvijay Cement', 'India Cement', 'Ramco', 'Orient Cement', 'Shree Cement',
                    'Webber TLT', 'Tata Steel', 'SUN Flex', 'Techno Bright', 'Birla Corporation Cement', 'Star Cement',
                ])),
            ],
            'cta' => [
                'heading' => 'Expertise to Lead, Lead to **The Bench Marking**',
                'text' => self::DIRECT_ATTENTION,
                'button_text' => 'Contact Us',
                'button_link' => '/contact-us#enquiry',
                'image' => null,
            ],
        ];
    }

    /* =================================================================== ABOUT */
    private function about(): array
    {
        return [
            'hero' => [
                'title' => 'Company Profile',
                'subtitle' => 'WEAR PLATES SYNERGY VE INDIA – MANUFACTURING, REBUILDING & MAINTENANCE SERVICES',
                'image' => null,
            ],
            'profile' => [
                'eyebrow' => 'Company Profile',
                'heading' => 'Manufacturing, Rebuilding & **Maintenance Services**',
                'lead' => '(In association with Metal Form – Mumbai and technical collaboration with Kladex UK & Turkey)',
                'text' => self::PROFILE_INTRO."\n\n".self::ACTIVITIES."\n\n".self::WE_AT."\n\n".self::OWN_PLANT."\n\n".self::SPECIALITY,
                'points' => self::WEAR_PLATES_BRINGS,
                'image' => null,
                'image_2' => null,
            ],
            'association' => [
                'eyebrow' => 'Association',
                'heading' => 'In Association with Metal Form – Mumbai and Technical Collaboration with **Kladex UK & Turkey**',
                'text' => 'Wear plates together with their Associated workshop of M/s Metal Form and unique Kladex range of consumable brings comprehensive solutions for various steel mill rolls and other components',
                'partners' => $this->rows(['name', 'role', 'text', 'logo'], [
                    ['M/s Metal Form Mumbai', 'An Associated workshop facility', "Kulwant Singh – Director – Owner of M/s Metal Form Mumbai. Associated work shop of Wear Plates Synergy VE India – 25 years' Experience with Metal Form serving Oil & Gas Industry jobs and services-API approved facility"],
                    ['Kladex UK & Turkey', 'Technical collaboration', 'International collaboration of various alloy formulation from our partner Kladex UK. Technical knowhow of life enhancement in service performance of critical industrial components using patented Kladex FCAW and Metal core wire alloy formulations.'],
                    ['Synergy Alloys', 'SYNERGY Alloys & Wear Solutions', 'Synergy alloys the new leader in maintenance welding & services'],
                ]),
            ],
            'values' => [
                'eyebrow' => 'Mission & Values Roadmap',
                'heading' => 'FORGE WITH STRENGTH. **SERVE WITH SYNERGY.**',
                'text' => 'Building durable wear solutions with partnership, reliability, and integrity across India since 2008.',
                'values' => $this->rows(['direction', 'title', 'text', 'icon'], [
                    ['North', 'INNOVATION', 'We challenge the ordinary to create extraordinary solutions like Abrasodur.', 'fa-solid fa-lightbulb'],
                    ['East', 'RELIABILITY', 'We care for our machines, our people and uptime.', 'fa-solid fa-shield-halved'],
                    ['South', 'EXCELLENCE', 'We are committed to highest standards in quality safety performance.', 'fa-solid fa-star'],
                    ['West', 'INTEGRITY', 'We are honest transparent accountable.', 'fa-solid fa-scale-balanced'],
                ]),
                'image' => null,
            ],
            'timeline' => [
                'eyebrow' => 'Our Journey',
                'heading' => 'Serving India **Since 2008**',
                'items' => $this->rows(['year', 'title', 'text'], [
                    ['2008', 'Since 2008', 'Wear Plates Synergy VE India – Serving India Since 2008'],
                    ['2009-10', 'WEAR PLATE manufacturing unit', 'The state of art WEAR PLATE manufacturing unit based at Ankleshwar dedicatedly working for core sector industries like Steel, Cement, Mining, Construction, Power, Sugar and Engineering across PAN India and abroad since 2009-10 FY.'],
                    ['2020', 'Best start-up company', 'We are delighted to share that we have been awarded the best start-up company in 2020'],
                    ['2024-25', 'Iconic Project Showcase', 'PROJECT: HEAVY-DUTY CONICAL HOPPER / SILO FABRICATION – Year: 2024-25 | Location: GIDC Ankleshwar Fabrication Yard'],
                    ['2026', 'PROJECT', self::EXPANSION],
                ]),
            ],
            'group' => [
                'eyebrow' => 'Synergy',
                'heading' => 'SYNERGY **Alloys & Wear Solutions**',
                'text' => 'Synergy - developing the expertise in designing welding alloys & wear resistance materials',
                'statements' => "Synergy alloys the new leader in maintenance welding & services\nSynergy is built with highly talented team\nSynergy spreading across the growth by keeping footprints in national & international maps\nSynergy walks through the philosophy of turn scrap into savings\nSynergy is dedicated to bring out the new generation welding alloys & wear solutions for the future to come\nThe proven leadership of synergy by practicing the bench marking on quality & delivery of goods\nSynergy to combat the wear mechanism by developing value engineering which finally leads to total solutions",
                'products' => $this->rows(['name', 'image'], array_map(fn ($n) => [$n], [
                    'Testing Lab', 'Syno Sswp', 'Performance Polymers', 'Syno Tubes', 'Syno WR Tile 100*100', 'Syno Mmaw Rods',
                    'Syno Carb Wear Plates', 'Syno FC Wire', 'Syno Ceramics', 'Syno Ultraglide', 'Syno Turnkey Solutions',
                    'Syno Digital Welding Equipments', 'Syno Casting/ Impact Hammers',
                ])),
                'image' => null,
                'image_2' => null,
                'website' => 'www.synergyalloys.com',
            ],
        ];
    }

    /* =================================================================== LEADERSHIP */
    private function leadership(): array
    {
        return [
            'hero' => [
                'title' => 'Management Profile',
                'subtitle' => 'We have our young dynamic manufacturing team under the guidance of highly experienced leadership',
                'image' => null,
            ],
            'intro' => [
                'eyebrow' => 'Leadership',
                'heading' => 'Highly Experienced **Management**',
                'text' => 'Highly experienced management and supervisory team with more the 25 year of experience with L&T Welding Products Business, Reliance Industries and Welding Alloys UK in job execution',
            ],
            'md_message' => [
                'eyebrow' => "From the Managing Director's Desk",
                'heading' => 'Message from the **Managing Director**',
                'name' => 'D. Deepti',
                'designation' => 'Managing Director',
                'photo' => null,
                'message' => "I am privileged to be part of a talented team enriched with high engineering skill and expertise. Our organization's vision and mission define our purpose and the path we strive to accomplish. As a team, we are committed to delivering excellence in all our key result areas, empowering us to undertake more challenging assignments today and in the years to come.\n\nI extend my best wishes to each one of you — our valued stakeholders — for great success and growth together.\n\n\"We are committed to delivering premium wear protection solutions with reliability, innovation, and service excellence. Our focus is on quality, durability,",
                'quote' => 'The expertise to lead, Lead to the bench marking. The leader in Wear Protection.',
            ],
            'leaders' => [
                'leaders' => [
                    [
                        'name' => 'Pyaare Lal Mangal',
                        'designation' => 'MD (Managing Director)',
                        'location' => 'GIDC Ankleshwar',
                        'photo' => null,
                        'summary' => '30 years with RIL, lastly served as Sr. Vice President',
                        'highlights' => "Name: Pyaare Lal Mangal.\nFamily: Married with Neena Kapoor of Kanpur. Blessed Ch. Son & loving daughter.\nAcademic: Successfully done Double Post Graduation in Commerce+ Economics from Jiwaji University, Gwalior + Dip. Exim., CAIIB.\n1st Love: Badminton I was college captain for 2 consecutive yrs. Also awarded Gwalior, Indore, Bhopal, Jabalpur, Dist. badminton championship.\nExperience: Thrilled with every challenges assigned to work 41 yrs. as Mkt. Professional Pan India & International arena. Eventually retired as Exe. Director.\nExpertise: I was self-made, self-oriented, self-motivated & built team under my leadership. My forte lies Launching n brand building, planning complex strategies. Also widely travelled Pan India Lisbon, Barcelona, B.'desh, Nepal for jt. Venture.\nOn Personal Note: I feel incarnated while roaming on any beach site. I had created win win situation during my tenure with various repute of high esteem Corporate Groups unlike RIL, GSIC, RESHAM GROUP of Gujarat etc.\nHeartfelt Hobbies Produced short film titled \"SELF RESPECT\". Created almost 165 poems.",
                        'quote' => 'My Philosophy" Individually we are drop of water. Together we are Ocean."',
                    ],
                    [
                        'name' => 'Prasanta Dhar',
                        'designation' => 'The Chief Operating Officer',
                        'location' => 'Mumbai',
                        'photo' => null,
                        'summary' => '**Prasanta Dhar**, The Chief Operating Officer, **Wear Plates Synergy VE India**, Mumbai with a back ground of being a Mechanical Engineer with a Specification on modern Welding & Foundry Technology from Jadavpur University, basically from a Kolkata based humble Bengali family, Married having family with two children. Presently based in Mumbai, the professional Summary 40+ years of cross-functional expertise spanning Indian Navy, L&T Eutectic/MIPD and the Manufacturing Leadership in Wear Protection Technology. Deep domain knowledge in abrasion-resistant plates, hard-facing, application engineering, and strategic business growth for core sector industries. Known for blending defence-grade discipline with commercial acumen to deliver zero-compromise quality and on-time execution.',
                        'highlights' => "Indian Navy | Jr. Engineer — Built foundational expertise in mechanical systems, maintenance, and reliability engineering in mission-critical marine environments. Instilled lifelong commitment to precision, safety, and defect-free execution.\nL&T Welding Products Division | Application Development & Product Management for Core Sector Industries — Cement, Steel, Power, Mining & Constructions, Developed wear solutions using CDP® Wear Plates, CastoDur Diamond Plates, EnDOtec hardfacing. Promoted to be an Exclusive PAN-India Product Management Role, operating from Mumbai. Held with market development responsibilities for wear plate business across PAN India.\nSuccessfully completed Management Development Programme at IIM Bangalore under company sponsorship — strengthening strategic and leadership capabilities and International Exposure.\nRepresented L&T Eutectic/E+C at WP-Technology Meets in Germany & Poland on two occasions, collaborating with parent company Castolin Eutectic on global wear protection trends.\nManufacturing Leadership undergone real time WP Capacity Expansion-moved to Manufacturing with end-to-end ownership of WP Capacity Expansion: Project Management, Project Execution, Vendor Management, Business Development, Estimation & Proposals.\nGlobal Sourcing: Visited China in 2018-19 to establish strategic business alliances for new generation wear plate, raw material and technology. After having four years of enriched manufacturing experience in plant operations, capex execution, and supply chain.\nBack to the L&T Head Office at Powai Campus, as a Head of Wear Plate Business, driving strategic initiatives, New Product Development in wear protection technology, Strategic Alliances with global leaders in abrasion-resistant steel-growth Bridge Initiative of CBC — expanding market reach and application segments\nExecutive Leadership allowed to shoulder the SAWS (Pvt Ltd) the role of Executive Director, for 6 years — led P&L operations and business transformation of private limited company in wear solutions.\nCurrently: Chief Operating Officer, Wear Plates Synergy VE India, Mumbai, Leading the team with the great understanding of 400/450/500/600 HBW through hard wear plate and their role in business with focus on the existing market of Hardox®, Sumihard®, XAR®, Rockstar®.\nDriving the capacity expansion in 2026 with new 6kW fiber laser. Committed to certified material, 12-month warranty, and shutdown-critical dispatch reliability for Indian Core sectors\nThought Leadership 2006: Presented on behalf of L&T Eutectic India at technical forum in Poland\n2015: Co-authored article in Impulse magazine with Dr. P. Shiva R&D Head \" wear plates Ready-to-Use Versions \".\nCOE of L&T - HR showcased the standalone project which was successfully executed on the concept of \" Value Engineering \" - CCS - Create - Capture - Share the theme pioneered and promoted under the leadership in 2007-08 on wards.\nDid apply for the Patent on a solution in wear plate segment with an additional features on flowability from a concept on \"Crack Free and Ripple Free Surface\" to enhance the throughput.",
                        'quote' => 'On behalf of Wear Plates Synergy Team, wishing all the very best to our COO and " Congratulations on such a distinguished 40-year journey "',
                    ],
                ],
            ],
            'team' => [
                'eyebrow' => 'Management profile',
                'heading' => 'Management **Profile**',
                'text' => null,
                'members' => $this->rows(['name', 'designation', 'experience', 'years', 'photo'], [
                    ['P L Mangal', 'Managing Director - Wear Plates Synergy VE India, GIDC Ankleshwar', '30 years with RIL, lastly served as Sr. Vice President', '30'],
                    ['Prasanta Dhar', 'Chief Operating Officer Wear Plates Synergy VE India, Mumbai', '30 Years with L&T Welding Products Division, Lastly served as JGM', '30'],
                    ['Shyam P Monala', 'General Manager Product Specialist, Product Positioning and New Product Development', "27 years' Experience with Vizag Steel Plant handling Tribology", '27'],
                    ['Alpesh Patel', 'Head Manufacturing (cv)', 'Wear Plates Manufacturing specialist, have very rich experience of 20 years of structural fabrication in clubbing with an Weld Cladding Technology of last 14 years', '20'],
                    ['Alish Radadiya', 'Manager QC', 'having indepth knowledge of, CAD, clubbing with CNC Plasma cutting, weld chemistry and fabrication tolerance, having a rich experience of 12 years in the Plant', '12'],
                    ['Abhijit Dhodapkar', 'Consultant', '29 years with Eutectic L&T and WA –UK India and Head Esab and Lincoln ME Distributors', '29'],
                    ['Jignesh Chauhan', 'Design and Estimation', '15 Years –Hard Core Designing with Wear Plate Technology', '15'],
                    ['KK Rao', 'Consultant Rebuilding services', '25 years with EWAC Tero lab facility and WA – India Integra work shop', '25'],
                    ['Kulwant Singh', 'Director – Owner of M/s Metal Form Mumbai. Associated work shop of Wear Plates Synergy VE India', "25 years' Experience with Metal Form serving Oil & Gas Industry jobs and services-API approved facility", '25'],
                ]),
                'promoters' => 'Promoter: Mr. P L Mangal & Mr. Bhawar Singh Rajput, having Textile and Finance business background.',
            ],
        ];
    }

    /* =================================================================== PRODUCTS */
    private function products(): array
    {
        return [
            'hero' => [
                'title' => 'Our Products & Solutions',
                'subtitle' => 'WPI - products and solutions for longer life time and lower maintenance costs!',
                'image' => null,
            ],
            'intro' => [
                'eyebrow' => 'Product Spectrum',
                'heading' => 'Product **Spectrum**',
                'text' => self::PPA,
                'wear_modes' => null,
            ],
            'spectrum' => [
                'eyebrow' => 'Make - WPSVEI',
                'heading' => 'Special Alloys and **Unique Metals**',
                'text' => "Special Alloys and Unique Metals : Tungsten Carbides, Silicon Carbides, Rich Cr-C Alloys, Ceramics, Cast Basalt and FR- Applications-Wear Parts, Wear Components\n\nA full gamut of Low Heat Input Maintenance Welding Electrodes under the Brand Name : **WPI Eutectic-Uni-Alloys (EUA)**, make \"WPSVEI\"",
            ],
        ];
    }

    /* =================================================================== INDUSTRIES */
    private function industries(): array
    {
        $sectors = [
            ['Steel Plants and OEMs', 'fa-solid fa-industry', "Reconditioning of Table Rolls\nCladding of caster rolls\nWrapper rolls, ERT rolls and Pinch rolls\nRepairing of Briquetting Rolls, Everlasting & Ball Valves\nRepairing of broken gears, Gear Boxes, various components of machines,\nCritical components machining after welding by using CNC machine with suitable jigs & fixtures"],
            ['Cement Plants and OEMs', 'fa-solid fa-warehouse', "VRM onsite hard facing of Loesche 53.3+3 and other variants\nPfeiffer mill MPS - OK mill for Clinker, slag, raw mill,\nPolysius Mill Reconditioning of Roller Press rolls including welding & machining\nCement liners, hoppers Hard Face overlay & fabrication\nWeld repair of Girth Gear, Shafts by applying advance welding technique & thermal process\nSupply of tailor made wear plates for anti – wear & anti abrasion applications"],
            ['Oil & Gas Jobs done for (ONGC)', 'fa-solid fa-oil-well', "Sea Water Lift Pump – complete component repair\nMud Pump Modules\nScrew Compressor\nFire Water Pump along with Column Pipe Repair & Manufacturing - Carbon Steel, Duplex Stainless Steel\nThermal Metal powder Spray coating of shafts\nAnchor Winch Gear Repair"],
            ['Petrochemical & fertilizer Industry', 'fa-solid fa-flask', "Weld Repair & overhauling of various valves\nSurface coating engineering on valves, valve seats & stem shaft by using Stellite weld overlay, inconel overlay, Hastelloy, Colmonoy & Tungsten Carbide overlay by using various process such as thermal spray, GTAW welding, SMAW welding & MIG welding including machining & grinding services."],
            ['Forging Industry', 'fa-solid fa-hammer', "Critical weld repair of forging press in MUSCO Khopoli\nforging press eccentric shaft, Column"],
            ['Sugar Industry', 'fa-solid fa-seedling', "Weld repair of Sugar Mill Roller\nThermal spray metal coating on sugar mill shaft, bearing area & machining"],
            ['Fabrication & Engineering Industry', 'fa-solid fa-gears', "Crack repair of floor boring machine, Gear Box & Bed\nInconel & Stellite weld overlay on pressure vessel nozzles\nInconel reformer pipeline welding"],
            ['Defense Industry', 'fa-solid fa-shield-halved', "Critical weld repair of Hydraulic Press crown & cylinder in ordnance factory\nWeld repair & machine of forging\npress Tube to Tube sheet welding"],
        ];

        return [
            'hero' => [
                'title' => 'Key Jobs and Services',
                'subtitle' => self::KEY_JOBS,
                'image' => null,
            ],
            'intro' => [
                'eyebrow' => 'Industry Wise',
                'heading' => 'Key Jobs and Services **Industry Wise**',
                'text' => self::OPEN_TO_JOBS,
            ],
            'sectors' => [
                'sectors' => array_map(fn ($s) => ['name' => $s[0], 'icon' => $s[1], 'summary' => null, 'jobs' => $s[2], 'image' => null], $sectors),
            ],
            'rolls' => [
                'eyebrow' => 'Steel Mill roll reconditioning',
                'heading' => 'Wear Plates –Kladex (Steel Mill Roll **Reconditioning**) Services',
                'text' => 'Wear plates together with their Associated workshop of M/s Metal Form and unique Kladex range of consumable brings comprehensive solutions for various steel mill rolls and other components',
                'table' => implode("\n", [
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
                ]),
                'note' => 'Wear Plates offers international references of respective to our Wear Plate products and services including HSM of the use of Kladex wires on above rolls with performance data and chemistry to counter hi friction /pressure /temp exposure and hardness requirements.',
            ],
            'capability' => [
                'eyebrow' => 'Rebuilding & Maintenance',
                'heading' => 'Welding Cladding and **Rebuilding Services**',
                'text' => "All welding processes including SMAW, GMAW, GTAW with Plasma and HVOF powder spray facility and expertise.\n\nX, Y, Z Axis specialized semi-automatic welding systems (Mill –Kits) –with 600 AMPs CC /CV power source capable of Open arc-FCAW, Gas shielded GMAW and solid wire and flux SAW process, offering wide Variety of alloy selection for any critical weld over lay and joining job challenge.",
                'points' => "Well-equipped Engineering Work shop – with Machining & Cladding Facility for Steel, Cement and Power sector.\nAPI approved facility for critical Oil and Gas jobs & services\nTwin wire metal spray system\nSingle & Twin wire roll weld cladding system",
                'image' => null,
            ],
        ];
    }

    /* =================================================================== INFRASTRUCTURE */
    private function infrastructure(): array
    {
        return [
            'hero' => [
                'title' => 'Infrastructure',
                'subtitle' => 'Wear Plates Synergy VE India have got its own a state of art manufacturing plant with machines embedded with latest technology',
                'image' => null,
            ],
            'overview' => [
                'eyebrow' => 'Manufacturing Plant',
                'heading' => 'State of Art **Manufacturing Plant**',
                'text' => self::OWN_PLANT."\n\n".self::ACTIVITIES,
                'image' => null,
            ],
            'capacity' => [
                'eyebrow' => 'Capacity',
                'heading' => 'Production **Capacity**',
                'items' => $this->rows(['value', 'suffix', 'label', 'icon'], [
                    ['11000', 'sq.m.', 'WPI ABRASODUR (Crc) weld cladded wear plates per annum', 'fa-solid fa-layer-group'],
                    ['1100', 'sq.m.', 'per month of Crc based Composite Weld Cladded Wear Plates', 'fa-solid fa-calendar-days'],
                    ['13200', 'sq.m.', 'per annum – Production Capacity', 'fa-solid fa-gauge-high'],
                    ['5000', 'metre', 'WPI WEAR TUBE per annum', 'fa-solid fa-circle-dot'],
                    ['700', 'ton', 'Structural Fabrication per annum', 'fa-solid fa-industry'],
                    ['60', 'tons', 'minimum quantity per month', 'fa-solid fa-truck-ramp-box'],
                ]),
            ],
            'facilities' => [
                'eyebrow' => 'Facilities',
                'heading' => 'Wear Plates Synergy VE India & **Metal Form**',
                'text' => 'Wear Plates Synergy VE India & Metal Form (An Associated workshop facility) comprises of:',
                'items' => array_map(fn ($f) => ['icon' => $f[0], 'title' => $f[1], 'text' => null], self::FACILITY_LIST),
            ],
            'process' => [
                'eyebrow' => 'The Process',
                'heading' => 'THE **PROCESS**',
                'text' => self::PROCESS,
                'steps' => $this->rows(['title', 'text', 'image'], [
                    ['THE WORKING:', self::WORKING],
                    ['Applications', self::APPLICATIONS],
                    ['Our Standard Plate', 'Our Standard Plate of 3000 x 1350 mm, of various composite thicknesses.'],
                    ['Production Capacity', 'Production Capacity : 1100 Sq.m. per month and 13200 sq.m. per annum'],
                ]),
            ],
            'machines' => [
                'eyebrow' => 'Machines',
                'heading' => 'List of **Major Machines**',
                'text' => null,
                'table' => implode("\n", [
                    'Sr No. | Machine Name | Machine Description | Make | Capacity | Quantity',
                    '01 | Wear Plate Machine | Four Head Wear Plate Cladding machine | SRT, India | 500 SqMtr/Month | 01 No',
                    '02 | Wear Plate Machine | Four Head Wear Plate Cladding machine | TI, India | 500 SqMtr/Month | 01 No',
                    '03 | Pipe Cladding Machine | Pipe Cladding machine | TI, India | 200 Mtr/Month | 01 No',
                    '04 | Welding Power Source | IdealArc DC 1000 | Lincoln Electric USA | 1000 AMPS | 02 Nos',
                    '05 | Welding Power Source | Flextec 650 | Lincoln Electric USA | 650 Amps | 06 Nos',
                    '06 | Welding Wire Feeder | Flex Feed | Lincoln Electric USA | 600 Amps | 06 Nos',
                    '07 | Welding Power Source | Power Ind 500I | Lincoln Electric India | 500 Amps | 02 Nos',
                    '08 | Plate Rolling Machine | Himalaya KGH/25/5 | Himalaya, India | 30 MM THK. | 01 No',
                    '09 | CNC Plasma Cutting Machine | Flexcut 200 | Lincoln Electric USA | 50 MM THK | 01 No',
                    '10 | CNC manual Plasma | Tomahawk 1538 | Lincoln Electric USA | 25 MM THK | 02 No.',
                    '11 | Screw Air Compressor | GA11FF | Atlas Copco India | 10 Bar | 02 Nos',
                    '12 | Radial Drilling Machine | MAAN 50 X 16/1300 | Maan Technoplus | 65 MM | 01 No',
                    '13 | Lathe Machine | ATH/1/380/380/100 | SRUSHTI, India | 400 MM | 01 No',
                    '14 | Magnetic Drill Machine | JMB35 | JAY Industries | 50 MM | 02 Nos',
                    '15 | Forklift | 5MT | Godrej, India | 5MT | 01 No',
                    '16 | Overhead Crane | 15MT | Tirupati Cranes | 15MT | 01 No',
                ]),
            ],
            'design' => [
                'eyebrow' => 'Design and Estimation',
                'heading' => 'Design and **Estimation**',
                'text' => '15 Years –Hard Core Designing with Wear Plate Technology',
                'drawings' => $this->rows(['title', 'image'], [
                    ['Layout and development of Reducing elbow'],
                    ['TRUNCATED PIPE 45°'],
                    ['Part No. 64.08.10093 – For Approval'],
                ]),
            ],
            'expansion' => [
                'eyebrow' => 'Project',
                'heading' => 'Capacity Expansion in **2026**',
                'text' => self::EXPANSION,
                'points' => "Strategic Alliance with Global Leader\nGrowth Bridge Initiative of CBC - expanding market reach and application segments\nCommitted to certified material, 12-month warranty, and shutdown-critical dispatch reliability for Indian Core sectors",
                'image' => null,
            ],
            'gallery' => [
                'eyebrow' => 'Gallery',
                'heading' => 'Quality of Finished Goods at **Synergy Plant**',
                'photos' => $this->rows(['caption', 'image'], [
                    ['Quality of Finished Goods at Synergy Plant'],
                    ['SYNERGY INFRASTRUCTURE'],
                    ['SYNERGY WORKSHOP'],
                    ['Testing Lab'],
                    ['Structural Fabrication with Wear Plates'],
                    ['GIDC Ankleshwar Fabrication Yard'],
                ]),
            ],
        ];
    }

    /* =================================================================== QUALITY */
    private function quality(): array
    {
        return [
            'hero' => [
                'title' => 'Quality & Certifications',
                'subtitle' => 'Since 2008 | ISO & MSME Certified',
                'image' => null,
            ],
            'commitment' => [
                'eyebrow' => 'Quality',
                'heading' => 'Quality, Competitive Pricing and **Delivery on Time**',
                'text' => self::DELIVERY."\n\n".self::ASTM,
                'points' => "Uniform Microstructure Throughout the Thickness\nUniform Hardness\nQC: Hardness + DP test on slots + Dimensional report + Trade Mark Certificate - 100% traceable\nQC: Each pipe number punched + hardness report attached\nQC: Hydrotest 10 kg/cm² + DP Test + CCO Thickness Check + Dimensional + Circularity + Flange flatness - ISO Certified",
                'image' => null,
            ],
            'certificates' => [
                'eyebrow' => 'Certificates',
                'heading' => 'ISO Certified & **MSME Registered**',
                'text' => null,
                'items' => $this->rows(['title', 'issuer', 'number', 'validity', 'scope', 'image'], [
                    ['Certificate of Registration – (Quality Management System) ISO 9001:2015', 'Paramount Quality Certifications (PQC)', 'QG-61481/0626 (Client Id: 57001)', 'Issuance Date: 13.06.2026 | Date of Expiry: 12.06.2029', 'For the following Scope: MANUFACTURE AND SUPPLY WEAR COMPONENTS TO STEEL, CEMENT, POWER, MINING AND CONSTRUCTION INDUSTRIES, BRAND WPI ABRASODUR, WPI WEAR TUBE WITH ABRASODUR ALLOYS, WPI THWPHARDOX PLATE.'],
                    ['UDYAM REGISTRATION CERTIFICATE', 'Government of India – Ministry of Micro, Small and Medium Enterprises', 'UDYAM-GJ-06-0058021', 'Type of Enterprise: Micro (2026-27)', 'Major Activity: MANUFACTURING. Date of Incorporation / Registration of Enterprise: 01/04/2020'],
                    ['Registration Certificate – Form GST REG-06', 'Government of India', '24AGWPD5947K1ZT', 'Period of Validity From 14/05/2026', 'Legal Name: WEAR PLATES SYNERGY VE INDIA. Constitution of Business: Proprietorship. Type of Registration: Regular'],
                    ['® Registered Trade Mark', 'Govt. of India', 'WPI THWPHARDOX 400®', 'Since 2008', '® Registered Trade Mark under Govt. of India - Brand Owned by Wear Plates Synergy VE India - Since 2008'],
                ]),
            ],
            'qap' => [
                'eyebrow' => 'Quality Assurance Plan',
                'heading' => 'Quality Assurance **Plan**',
                'text' => 'Description of Job :- Make of (WPI E450) & (WPI Abrasodur) plates | Size :- As Per Purchase Order 12 mm thk E450BR | QAP NO:- LB-04 / Rev No.00',
                'steps' => $this->rows(['stage', 'title', 'norm', 'record'], [
                    ['Material Test', 'Material Composition - Chemical Report', 'As Per Chemical Grade - E450 BR (Sample from Each Lot)', 'NABL Lab Report'],
                    ['Material Test', 'Hardness Test', 'Hardness - 30 HRc (Test Conducted On Ready material randomly basis) – Uniform Hardness', 'Inspection Report'],
                    ['Material Test', 'Micro Test', 'Includes Primary chromium Carbides & Eutectic carbides along with Austenite & Martensite. (Uniform Microstructure Throughout the Thickness)', 'Lab Report'],
                    ['Dimensional & Visual Check', 'Dimensional Check', 'As specified – 100% – Side grind it and Check Thickness of deposition', 'Inspection Report'],
                    ['Dimensional & Visual Check', 'Visual Inspection', 'No Manual Welding Work Allowed, Chrome Carbide Deposition to be Perfect in alignment with each other. (Consistent Beads Required. Bead Overlap to be Proper)', 'Inspection Report'],
                    ['Final Inspection of Job After Completion', 'Dimensional Check & Visual Check', 'OD, ID, Thk & Length to be maintained as per required Size and As Per Standard Requirement Visual Finishing to be maintained at final Stage before dispatch – M/s Wear Plates Will Depute IMCO QA Inspector At Your Site For Final Inspection Of Wear Plates.', 'Inspection Report & Photos'],
                ]),
                'note' => "H: Hold Point - Inspection is mandatory for this Stage, Manufacturer can not proceed without clearance of the stage cleared by the concerned agency or obtained waiver in writing.\nW:- Witness Point - Same as Hold Point, however manufacturer may proceed maintaining internal inspection report for review at a later date in case concerned agency does not present himself on the proposed date of inspection.\nR:- Review of inspection documents & activities carried out by concerned agency for compliance & acceptance.",
            ],
            'testing' => [
                'eyebrow' => 'Technical Data',
                'heading' => 'Bead Properties / **Weld Chemistry**',
                'text' => 'Wear Plate Material Properties & Technical Data',
                'items' => $this->rows(['icon', 'title', 'text'], [
                    ['fa-solid fa-flask', 'Chemical composition : (wt %) (On top surface)', null],
                    ['fa-solid fa-gauge-high', 'Bulk hardness (HRc)', null],
                    ['fa-solid fa-microscope', 'Microhardness (VPN) Load (gm)', null],
                    ['fa-solid fa-hill-rockslide', 'Abrasive wear resistance (1/cc) ASTM G 65, Practice A', null],
                    ['fa-solid fa-chart-pie', 'Primary carbides in Eutectic matrix vol. Fraction of carbides', null],
                    ['fa-solid fa-water', 'Hydrotest 10 kg/cm² + DP Test', null],
                ]),
                'image' => null,
            ],
        ];
    }

    /* =================================================================== PROJECTS */
    private function projects(): array
    {
        return [
            'hero' => [
                'title' => 'Iconic Project Showcase',
                'subtitle' => 'Premium Wear Solutions — Heavy Duty Fabrication',
                'image' => null,
            ],
            'intro' => [
                'eyebrow' => 'Iconic Jobs',
                'heading' => 'Structural Fabrication with **Wear Plates**',
                'text' => self::ICONIC."\n\n".self::DIRECT_ATTENTION,
            ],
            'projects' => [
                'projects' => [
                    [
                        'title' => 'The Project of a Cement Plant',
                        'category' => 'Structural Fabrication',
                        'summary' => 'The Project of a Cement Plant, right from the Crusher House to Packing Plant as per the drawing of all technological structures, weighing around 75 tons, we have completed and delivered from our Plant on schedule. Today we geared up to deliver a minimum quantity of 60 tons per month.',
                        'specs' => null, 'image' => null, 'image_2' => null,
                    ],
                    [
                        'title' => 'GRIT CONE',
                        'category' => 'Structural Fabrication',
                        'summary' => 'We have developed our own expertise in manufacturing high capacity GRIT CONE, here we have shown below, one such CONE from Cement Plant weighing 13 ton and other one is for Steel Plant weighing 16 ton. Both have finally met the high level of satisfaction of End Users.',
                        'specs' => null, 'image' => null, 'image_2' => null,
                    ],
                    [
                        'title' => 'The Rotor',
                        'category' => 'Structural Fabrication',
                        'summary' => 'We have fabricated The Rotor having self-weight of 16 ton which is a typical unit undergone Static Balancing and Dynamic Balancing, third party inspection finally qualified and led to Customer Satisfaction.',
                        'specs' => null, 'image' => null, 'image_2' => null,
                    ],
                    [
                        'title' => 'PROJECT: HEAVY-DUTY CONICAL HOPPER / SILO FABRICATION',
                        'category' => 'Iconic Project Showcase',
                        'summary' => 'Premium Wear Solutions — Heavy Duty Fabrication',
                        'specs' => "Client Sector | Cement / Mining / Power - Bulk Material Handling\nMaterial Used | Abrasodur 400 / 500 Wear Plate + MS Structure (12+4mm)\nJob Type | Large Dia Cone Fabrication, Rolling, Welding & Liner Fitting\nDimensions | ~3.5M Dia Bottom, Heavy Conical Body with Access Hatch\nYear | 2024-25\nLocation | GIDC Ankleshwar Fabrication Yard\nChallenge | High abrasion zone, heavy impact, seamless conical rolling",
                        'image' => null, 'image_2' => null,
                    ],
                    [
                        'title' => 'SPRAY NOZZLE RING MANIFOLD - CEMENT PLANTS',
                        'category' => 'Wear Tube',
                        'summary' => "Gas Conditioning Tower | Kiln Cooling | Cooler Spray | Wear Tube + Abrasodur CCO Lined\n\nPremium Cash Job - Wear Tube Internally Wear Faced by Abrasodur (CCO) - Vertical Pipe Full - WPSYNERGY®",
                        'specs' => "Job Name | Spray Nozzle Ring / Quench Ring / Cooling Ring Manifold - For Cement Kiln GCT & Cooler - Vertical Full\nPhoto Job | COMPLETE RING + VERTICAL PIPE FULLY VISIBLE - Flanged Elbow Inlet - Full Circle - 28 Nozzles - CUT BOTTOM\nMOC - CORRECTED FINAL | Wear Tube (Heavy Duty) - Internally Wear Faced by Abrasodur (CCO) Overlay - 6mm CCO Lining\nOverlay Spec | Abrasodur - Chromium Carbide Overlay (CCO) - 60-65 HRC - Inside full bore - Anti-Abrasion + Erosion Proof\nConstruction | Main Ring 150 NB Wear Tube + 200 NB Cross Header + Vertical Inlet 100 NB Elbow - All internally CCO lined\nVertical Pipe | Fully Visible - 90° Elbow with 150# Flange - ANSI B16.5 Drilled - Wear Tube + CCO lined - Ready to bolt\nNozzles | 3/4 inch Half Couplings x 28 Nos - Angled 30° - Full penetration TIG welded - CCO protected - Uniform spray\nWelding | 100% TIG Root + MIG Cover - All joints DP Tested - Zero crack - Zero leakage - CCO intact - ISO Workshop\nInlet | Vertical 4 inch / 6 inch Flanged Elbow - Top entry - Easy maintenance - Gasket + Bolts included - GCT proven\nFunction | Water spray with abrasive dust / slurry - Gas cooling / dust suppression / kiln shell / cooler spray - Cement\nAdvantage | Wear Tube + Abrasodur CCO Inside - Life 3X vs MS pipe - No internal erosion - No choking - No flange leak\nFabrication | Segmented bending + Fixture + Internal CCO overlay after bending + Vertical pipe alignment <1mm - Full Ring\nApplication | UltraTech / Ambuja / ACC / Shree / Dalmia / JK / Chettinad - GCT / Kiln / Cooler / Bag House - High abrasion\nQC | Hydrotest 10 kg/cm² + DP Test + CCO Thickness Check + Dimensional + Circularity + Flange flatness - ISO Certified\nDispatch | Made to order - GIDC Ankleshwar - 72 Hrs - Wooden pallet packing - Pan India 24 Hrs - WPSYNERGY® Brand\nBrand Owner | Wear Plates Synergy VE India - Since 2008 - ISO & MSME - ® Registered Trade Mark Owner - WPI THWPHARDOX Family",
                        'image' => null, 'image_2' => null,
                    ],
                    [
                        'title' => 'FULL TRUCK DISPATCH – BULK',
                        'category' => 'Wear Lined Components',
                        'summary' => "20-25 Pipes Per Truck | Ready Painted | Pan India Delivery\n\nIconic Cash Proof Since 2024 - Not Job Work, OEM Supply",
                        'specs' => "Product | Composite Lined Pipes + Elbows + Reducers – Full Lot\nQuantity | 20-25 pipes + 10 fittings per truck – 100M per order\nLining | CCO / Abrasodur 6+4mm inside – 62 HRC, smooth finish\nOuter | MS shell + Red Oxide Primer – ready to install\nPacking | Wooden pallets + End caps – no damage in transit\nDelivery | GIDC Ankleshwar to Chittorgarh, Hyderabad, Noida in 3 days\nProof | Photo of loaded truck – customers trust bulk capability\nApplication | Cement, power, steel – shutdown replacement\nStock | Ready stock 50M – urgent breakdown in 24 hrs dispatch\nQC | Each pipe number punched + hardness report attached\nCash Note | Fastest cash – advance 50%, balance before dispatch",
                        'image' => null, 'image_2' => null,
                    ],
                    [
                        'title' => 'Iconic Jobs – Structural Fabrication with Wear Plates',
                        'category' => 'Structural Fabrication',
                        'summary' => self::ICONIC,
                        'specs' => null, 'image' => null, 'image_2' => null,
                    ],
                ],
            ],
            'capability' => [
                'eyebrow' => 'Trial Ground',
                'heading' => 'A Trial Ground to Testify **the Muscle of Our Facility**',
                'text' => self::TRIAL_GROUND,
                'button_text' => 'Contact Us',
                'image' => null,
            ],
        ];
    }

    /* =================================================================== RESOURCES */
    private function resources(): array
    {
        return [
            'hero' => [
                'title' => 'Facts',
                'subtitle' => 'How is Hardfacing a part of our everyday life?',
                'image' => null,
            ],
            'hardfacing' => [
                'eyebrow' => 'Facts',
                'heading' => 'How is Hardfacing a Part of Our **Everyday Life?**',
                'lead' => 'We know what you\'re probably thinking, …"A part of our life??? We hope not, it sounds like some kind of debilitating facial disease associated with hardening of the arteries." Relax, **Hardfacing** is an industrial process used to prolong equipment life. It has nothing to do with the your bodily attributes. But it does affect practically everything you touch, see, taste, feel and consume. It is very much associated with **WEAR.**',
                'text' => implode("\n\n", [
                    "I have estimated that industry spends over \$2.2 Billion annually because of wear. As a member of Management, I'm sure you're concerned about how this all affects your bottom line. Your particular industry may employ a number of methods to combat wear, such as painting, lubricating, plating, and galvanizing, just to mention a few. Your industry may in fact employ a growing technology known as **Hardfacing,** hardsurfacing, or cladding. If not, then I think you will find the following most interesting.",
                    "**What is Hardfacing?** Well, quite simply, **Hardfacing** is an industrial welding or overlay process that is applied to material handling components to resist wear and prolong working life. Not only is this technique cost effective, it is highly versatile as well. Simply by changing the **Hardfacing** alloy, a component can exhibit as much as 30 times its intended life. I don't mean to imply that all **Hardfacing** components exhibit this type of dramatic result, but the technology, applied properly, can produce some very satisfying returns.",
                    "In the same way that wear touches practically every aspect of our lives, so too does **Hardfacing.** That may seem a little far fetched, but I assure you it is true. Just to see how extensive this process is, let's take a simple task like running an errand to the grocery store and examine just where **Hardfacing** plays a role.",
                    "As you start for the door, you are asked to take out the trash. On the way you switch out the light as you go. Let's stop right here to examine how we benefit by **Hardfacing:**",
                    '**Light Switch:** Made of plastic Plastics or polymers are extruded under very high pressures into molds. The extrusion press resembles a screw auger and it experiences excessive wear. Polymers are abrasive in nature. The flights and barrel of the screw auger are systematically hardfaced with Nickel or Cobalt **Hardfacing** to extend their life.',
                    self::ASTM,
                    'We live in an age of great consumption, particularly in the West. As we progress into the next century, the World will make even greater demands upon raw materials and consumer products. Hardfacing has not only met the demands of the current century, it will no doubt rise to the challenge of the next century.',
                    "You may not have even heard of the word \"Hardfacing\" before reading this article. Let's face it, it's not a household word and not even a popular industrial word, but it lives with us daily. It is responsible for bringing us many of the products and services we enjoy today. Perhaps, knowing just how invasive this technology is in everyday industrial settings, a new appreciation will be realized for its vast contribution and potential in a war against wear.",
                    'It has been said, (author unknown) that a dollar saved in Production is equitable to a 10 times increase in Sales and a 20 times increase in Profits. The numbers may be a little off, depending upon the industry, etc., but the concept is sound. Hardfacing can play a major role in your bottom line and become your household or industrial "Word"',
                ]),
                'facts' => $this->rows(['value', 'label'], [
                    ['$2.2 Billion', 'industry spends over $2.2 Billion annually because of wear'],
                    ['30 times', 'a component can exhibit as much as 30 times its intended life'],
                    ['10 times', 'a dollar saved in Production is equitable to a 10 times increase in Sales'],
                    ['20 times', 'and a 20 times increase in Profits'],
                ]),
                'image' => null,
            ],
            'wear' => [
                'eyebrow' => 'Wear',
                'heading' => 'What Webster Has to Say About **WEAR**',
                'text' => "Wear is just not associated with tires, clothes, cars, TV's and lawn mowers. It happens to everything, sooner or later and sometimes faster than we care to think about. Practically everything we come in contact with is associated with some type of wear. But before we get into the meat of the subject, let's take a look at what Webster has to say about WEAR:\n\n• To cause to deteriorate by use.\n• To impair or diminish by use or attrition.\n• Consume or waste gradually.\n• To produce gradually by friction or attrition.\n• To exhaust or lessen the strength of.\n\nIndustry defines it in more technical terms:",
                'types' => $this->rows(['icon', 'title', 'text'], [
                    ['fa-solid fa-hill-rockslide', 'Abrasion', null],
                    ['fa-solid fa-fire-flame-simple', 'Friction', null],
                    ['fa-solid fa-droplet', 'Corrosion', null],
                    ['fa-solid fa-wind', 'Erosion', null],
                    ['fa-solid fa-wave-square', 'Fretting', null],
                    ['fa-solid fa-burst', 'Fatigue', null],
                ]),
            ],
            'mining' => [
                'eyebrow' => 'Concept',
                'heading' => 'Earth Moving **and Mining**',
                'text' => "Applications continue to grow as the mining industry continues to strive to reduce the costs associated with wear. Cladded plate has enjoyed a spiraling acceptance throughout the world and will continue to flourish, limited only by the lack of maintenance ingenuity and creativity.\n\nEarth moving and mining has yielded an abundance of man's most basic necessities for thousands of years. The movement of the earth's crust requires tough tooling and ground engagement implements built to survive some of the most hostile environments. It has been a constant struggle to improve methods and materials to effectively reduce costly downtime and component replacements. Wear plates or abrasion resisting plates as they are often referred to, continue to fill a wide variety of applications and have enjoyed a great deal of success in applications involving abrasion and impact resistance. Ceramics are on the far end of the toughness spectrum. They provide excellent abrasion resistance, but are limited in applications involving moderate to heavy impact, and are not weldable. However, cladded wear plates provide the abrasion resistance that approaches ceramics, the weldability of mild steel, and moderate impact properties. These unique properties offer the user opportunities to significantly extend component life and reduce downtime.",
                'image' => null,
            ],
            'world' => [
                'eyebrow' => 'Wear Protection',
                'heading' => 'World of **Wear Plates**',
                'text' => null,
                'tiers' => $this->rows(['name', 'items'], [
                    [null, "Wear Resistant Rubber Liners\nCeramic / PU Liners\nCeramic Embedded Rubber Liners"],
                    [null, "SS Base CR-C WP\nSS (310) Base with Abrasodur A 45 Coating\nSS Base Complex Carbide WP\nHistalloy Lined WP\nARC Spray Coated Plates"],
                    [null, "CFSSHT (QT) WP\nACHBSS WP\nC Rolled WP\nSSHPWP (Single Thick WP Thin)\nSHTW-P Backed with MS"],
                    [null, "WC Coating with Nickel Base\nWC STRIP Brazing\nWC Coated\nWC Weld Clad\nWC Brazing Alloy"],
                    [null, "Alloy Casting\nBimetal Casting\nSinter Cast\nNI Hard Casting\nH - Chrome-MN"],
                    [null, "ERGOTEM SA Greece\nTRITEN CORPORN. UK\nWPI CR-C (60S) Composite WEAR PLATES\nVIDAPLATE LTD. USA\nVAUDIT GERMANY"],
                    [null, "RAEX400/500 FINLAND\nJFE EVERHARD JAPAN\nHARDOX400 / 500 / 600 SWEDEN\nDILLIDUR400 / 500 GERMANY\nCREUSABRO 4800 FRANCE"],
                ]),
            ],
            'applications' => [
                'eyebrow' => 'Application',
                'heading' => 'Proven Potential **Applications (PPA)**',
                'text' => self::PPA,
                'items' => $this->rows(['title', 'image'], [
                    ["How we're different"], ['Products from world leaders'], ['WPI Abrasodur 091004'], ['Vertical Roller Mill'],
                    ['Cold Sinter Screen'], ['Hot Sinter Screen'], ['RMC Plant'], ['RMC Pan Liners'], ['RMC Pan Side Liners'],
                    ['Local Worldwide – Wherever you are, we are'],
                ]),
            ],
        ];
    }

    /* =================================================================== WHY */
    private function why(): array
    {
        return [
            'hero' => [
                'title' => 'Why Choose Us',
                'subtitle' => 'Since 2008 | ISO Certified | MSME Registered | The Leader in Wear Protection',
                'image' => null,
            ],
            'reasons' => [
                'eyebrow' => 'Why Choose Us',
                'heading' => 'Why Choose **Us**',
                'text' => null,
                'items' => $this->rows(['icon', 'title', 'text'], [
                    ['fa-solid fa-certificate', 'ISO Certified & MSME Registered', null],
                    ['fa-solid fa-calendar-check', '15+ Years of Excellence – Serving Since 2008', null],
                    ['fa-solid fa-map-location-dot', 'Pan-India Presence & Fast Delivery', null],
                    ['fa-solid fa-helmet-safety', 'Custom Fabrications & Engineering Support', null],
                    ['fa-solid fa-shield-halved', 'High-Quality Materials & Durable Solutions', null],
                    ['fa-solid fa-headset', 'Dedicated After-Sales & Technical Service', null],
                ]),
            ],
            'promise' => [
                'eyebrow' => 'Delivery',
                'heading' => 'Quality, Competitive Pricing and **Delivery on Time**',
                'text' => self::DELIVERY,
                'points' => "Fast Delivery from Ankleshwar Plant Through Air & Surface Transports.\nStock: Ready 4 nos / 8 nos set - GIDC Ankleshwar - 24 hrs dispatch - Trade Mark genuine product\nStock: Ready stock 50M – urgent breakdown in 24 hrs dispatch\nDelivery: GIDC Ankleshwar to Chittorgarh, Hyderabad, Noida in 3 days\nDispatch: Made to order - GIDC Ankleshwar - 72 Hrs - Wooden pallet packing - Pan India 24 Hrs - WPSYNERGY® Brand",
                'image' => null,
            ],
        ];
    }

    /* =================================================================== PRESENCE */
    private function presence(): array
    {
        return [
            'hero' => [
                'title' => 'Our Presence Across India',
                'subtitle' => 'Corporate Office: Mumbai • Manufacturing Plant: GIDC, Ankleshwar, Gujarat',
                'image' => null,
            ],
            'intro' => [
                'eyebrow' => 'Our Presence',
                'heading' => 'Our Presence **Across India**',
                'text' => "Corporate Office: Mumbai\nManufacturing Plant: GIDC, Ankleshwar, Gujarat\nRegional Offices: Ahmedabad | Kolkata | Hyderabad | Guwahati | Palampur | Jamshedpur | Noida | Prayagraj | Chittorgarh",
                'network' => "Ahmedabad\nBangalore\nChennai\nDelhi\nGuwahati\nHyderabad\nKolkata\nMumbai\nNagpur\nPune\nJamshedpur\nUdaipur\nBaroda\nBhopal\nCoimbatore\nVizag",
            ],
        ];
    }

    /* =================================================================== CONTACT */
    private function contact(): array
    {
        return [
            'hero' => [
                'title' => 'Contact',
                'subtitle' => 'ORDER US | FREIGHT | GST | CONTACT',
                'image' => null,
            ],
            'intro' => [
                'eyebrow' => 'Contact',
                'heading' => 'WEAR PLATES SYNERGY **VE INDIA**',
                'text' => 'In Order To Serve You Better & Faster From Our Plant- Ankleshwar Through Air And Surface Transports',
                'offices' => $this->rows(['name', 'address', 'phone', 'email'], [
                    ['Reg. Office', 'G 66, Ground Floor, Ganesh Square, GIDC, Ankleshwar, Dist. Bharuch, Gujarat -393002, India.', '+91 7981795006', 'care.wpsynergyveindia@gmail.com'],
                    ['Works', 'GIDC Plot No 4311, BSNL Tower, Ankleshwar, Dist. Bharuch, Gujarat-393002, India.', null, 'wpsvei@gmail.com'],
                    ['Manufacturing Plant', 'Plot No: 4311, Near Crystal Chowkdi, Gidc, Ankleshwar 393002, Bharuch, Gujarat.', null, 'info.wpsynergyveindia@gmail.com'],
                    ['Mumbai Office', 'Palladio Building - 702, Hiranandani Estate GB Road, Thane West, Maharashtra - 400607. India.', '+91 8800556273', 'plmangal.wpsynergy@gmail.com'],
                    ['Hyderabad Office', 'Gautam Nagar, H no. 19-37, Malkajgiri, Secunderabad 500047, Telangana, India.', null, null],
                    ['Kolkata Office', 'Lotus Park 58 Regent Estate, Shree Colony Road, Kolkata-700047, West Bengal, India.', null, null],
                    ['Udaipur Office', 'Upali Badi, Bari, P.O Badgaon, Udaipur, Pin-313011, Rajasthan, India.', null, null],
                ]),
            ],
        ];
    }
}
