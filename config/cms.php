<?php

/*
|--------------------------------------------------------------------------
| Website content registry
|--------------------------------------------------------------------------
| Every editable page section of the public website. The admin sidebar shows one
| main menu per page and one sub-menu per section; the section editor form is built
| from the field list below. Content is stored in the `page_sections` table.
|
| Field syntax:  'key' => [type, label, help?, ...options]
| Types: text, textarea, image, lines (one item per line), pairs ("Label | Value" per
| line), table (pipe-separated rows, first row = header), icon (Font Awesome class),
| url, boolean, repeater (needs 'fields' => [...] and optional 'title' => field key).
*/

$hero = [
    'title' => ['text', 'Page Title'],
    'subtitle' => ['textarea', 'Banner Text', 'One or two short sentences under the title.'],
    'image' => ['image', 'Banner Background Image', 'Wide photo, at least 1920 × 600 px. Leave empty for the default dark steel background.'],
];

$heading = [
    'eyebrow' => ['text', 'Small Label Above Heading', 'e.g. "Who We Are". Optional.'],
    'heading' => ['text', 'Heading', 'Wrap words in **double asterisks** to highlight them in the brand colour.'],
];

$icon = ['icon', 'Icon', 'Font Awesome icon class, e.g. "fa-solid fa-industry". Browse free icons at fontawesome.com/icons.'];

return [
    'pages' => [

        /* ------------------------------------------------------------------ HOME */
        'home' => [
            'label' => 'Home Page',
            'icon' => 'fa-solid fa-house',
            'route' => 'home',
            'sections' => [
                'hero' => [
                    'label' => 'Hero Slider',
                    'icon' => 'fa-solid fa-images',
                    'fields' => [
                        'slides' => ['repeater', 'Slides', 'Large banner slides at the top of the home page. 2–4 slides work best.', 'title' => 'title', 'fields' => [
                            'eyebrow' => ['text', 'Small Label'],
                            'title' => ['text', 'Title', 'Wrap words in **double asterisks** to highlight them in gold.'],
                            'text' => ['textarea', 'Text'],
                            'image' => ['image', 'Background Photo', 'Wide photo, at least 1920 × 900 px (plant, products, site work).'],
                            'button_text' => ['text', 'Button Text'],
                            'button_link' => ['text', 'Button Link', 'Full URL or a path like /products'],
                        ]],
                    ],
                ],
                'trust' => [
                    'label' => 'Trust Strip',
                    'icon' => 'fa-solid fa-shield-halved',
                    'fields' => [
                        'items' => ['repeater', 'Badges', 'Short credentials shown just under the slider.', 'title' => 'title', 'fields' => [
                            'icon' => $icon,
                            'title' => ['text', 'Title'],
                            'text' => ['text', 'Sub Text'],
                        ]],
                    ],
                ],
                'about' => [
                    'label' => 'About Introduction',
                    'icon' => 'fa-solid fa-building',
                    'fields' => $heading + [
                        'text' => ['textarea', 'Text', 'Blank line = new paragraph.'],
                        'points' => ['lines', 'Key Points', 'One point per line.'],
                        'image' => ['image', 'Main Photo', 'Portrait or square photo, e.g. the plant or a large fabrication (min. 900 px).'],
                        'image_2' => ['image', 'Second Photo', 'Smaller overlapping photo (min. 600 px).'],
                        'badge_value' => ['text', 'Badge Number', 'e.g. 2008'],
                        'badge_label' => ['text', 'Badge Label', 'e.g. Since'],
                        'button_text' => ['text', 'Button Text'],
                    ],
                ],
                'stats' => [
                    'label' => 'Capacity Numbers',
                    'icon' => 'fa-solid fa-chart-column',
                    'fields' => $heading + [
                        'items' => ['repeater', 'Numbers', 'Counters animate from 0. Use digits only in "Number".', 'title' => 'label', 'fields' => [
                            'value' => ['text', 'Number'],
                            'suffix' => ['text', 'Suffix', 'e.g. +, T, m², m'],
                            'label' => ['text', 'Label'],
                            'icon' => $icon,
                        ]],
                    ],
                ],
                'products' => [
                    'label' => 'Products Section',
                    'icon' => 'fa-solid fa-boxes-stacked',
                    'fields' => $heading + [
                        'text' => ['textarea', 'Introduction'],
                        'note' => ['text', 'Note', 'Product categories are listed automatically from Products → Categories.'],
                    ],
                ],
                'value_engineering' => [
                    'label' => 'Value Engineering',
                    'icon' => 'fa-solid fa-scale-balanced',
                    'fields' => $heading + [
                        'text' => ['textarea', 'Text'],
                        'steps' => ['repeater', 'Steps', null, 'title' => 'title', 'fields' => [
                            'icon' => $icon,
                            'title' => ['text', 'Title'],
                            'text' => ['textarea', 'Text'],
                        ]],
                        'image' => ['image', 'Background Photo', 'Optional wide photo shown darkened behind this section.'],
                    ],
                ],
                'industries' => [
                    'label' => 'Industries Strip',
                    'icon' => 'fa-solid fa-industry',
                    'fields' => $heading + [
                        'text' => ['textarea', 'Introduction'],
                        'note' => ['text', 'Note', 'The industry cards are taken from Industries → Industry Sectors.'],
                    ],
                ],
                'process' => [
                    'label' => 'How We Work',
                    'icon' => 'fa-solid fa-gears',
                    'fields' => $heading + [
                        'text' => ['textarea', 'Introduction'],
                        'steps' => ['repeater', 'Steps', null, 'title' => 'title', 'fields' => [
                            'icon' => $icon,
                            'title' => ['text', 'Title'],
                            'text' => ['textarea', 'Text'],
                        ]],
                        'image' => ['image', 'Photo', 'Robotic cladding / workshop photo (min. 1000 px wide).'],
                    ],
                ],
                'projects' => [
                    'label' => 'Projects Teaser',
                    'icon' => 'fa-solid fa-diagram-project',
                    'fields' => $heading + [
                        'text' => ['textarea', 'Introduction'],
                        'note' => ['text', 'Note', 'The first 3 projects from Projects → Iconic Projects are shown.'],
                    ],
                ],
                'clients' => [
                    'label' => 'Clients',
                    'icon' => 'fa-solid fa-handshake',
                    'fields' => $heading + [
                        'text' => ['textarea', 'Introduction'],
                        'clients' => ['repeater', 'Clients', 'Name is shown when no logo is uploaded.', 'title' => 'name', 'fields' => [
                            'name' => ['text', 'Client Name'],
                            'logo' => ['image', 'Logo', 'Transparent PNG, about 300 × 150 px.'],
                        ]],
                    ],
                ],
                'cta' => [
                    'label' => 'Call to Action',
                    'icon' => 'fa-solid fa-bullhorn',
                    'fields' => [
                        'heading' => ['text', 'Heading'],
                        'text' => ['textarea', 'Text'],
                        'button_text' => ['text', 'Button Text'],
                        'button_link' => ['text', 'Button Link'],
                        'image' => ['image', 'Background Photo', 'Optional wide photo.'],
                    ],
                ],
            ],
        ],

        /* ------------------------------------------------------------------ ABOUT */
        'about' => [
            'label' => 'About Us',
            'icon' => 'fa-solid fa-building',
            'route' => 'about',
            'sections' => [
                'hero' => ['label' => 'Page Banner', 'icon' => 'fa-solid fa-panorama', 'fields' => $hero],
                'profile' => [
                    'label' => 'Company Profile',
                    'icon' => 'fa-solid fa-id-card',
                    'fields' => $heading + [
                        'lead' => ['textarea', 'Lead Paragraph', 'Larger introductory text.'],
                        'text' => ['textarea', 'Body Text', 'Blank line = new paragraph. **bold** supported.'],
                        'points' => ['lines', 'What We Bring', 'One point per line.'],
                        'image' => ['image', 'Main Photo', 'Plant or product photo (min. 900 px).'],
                        'image_2' => ['image', 'Second Photo', 'Optional (min. 600 px).'],
                    ],
                ],
                'association' => [
                    'label' => 'Associations',
                    'icon' => 'fa-solid fa-handshake-angle',
                    'fields' => $heading + [
                        'text' => ['textarea', 'Introduction'],
                        'partners' => ['repeater', 'Partners', null, 'title' => 'name', 'fields' => [
                            'name' => ['text', 'Name'],
                            'role' => ['text', 'Relationship', 'e.g. Technical Collaboration'],
                            'text' => ['textarea', 'Description'],
                            'logo' => ['image', 'Logo', 'Optional transparent PNG.'],
                        ]],
                    ],
                ],
                'values' => [
                    'label' => 'Mission & Values',
                    'icon' => 'fa-solid fa-compass',
                    'fields' => $heading + [
                        'text' => ['textarea', 'Mission Statement'],
                        'values' => ['repeater', 'Values (Compass Points)', 'Four values shown around the compass.', 'title' => 'title', 'fields' => [
                            'direction' => ['text', 'Compass Direction', 'North / East / South / West'],
                            'title' => ['text', 'Value'],
                            'text' => ['textarea', 'Description'],
                            'icon' => $icon,
                        ]],
                        'image' => ['image', 'Compass Image', 'Optional. A default compass graphic is drawn when empty.'],
                    ],
                ],
                'timeline' => [
                    'label' => 'Milestones',
                    'icon' => 'fa-solid fa-timeline',
                    'fields' => $heading + [
                        'items' => ['repeater', 'Milestones', null, 'title' => 'title', 'fields' => [
                            'year' => ['text', 'Year'],
                            'title' => ['text', 'Title'],
                            'text' => ['textarea', 'Text'],
                        ]],
                    ],
                ],
                'group' => [
                    'label' => 'Synergy Group',
                    'icon' => 'fa-solid fa-sitemap',
                    'fields' => $heading + [
                        'text' => ['textarea', 'Introduction'],
                        'statements' => ['lines', 'Group Philosophy', 'One statement per line.'],
                        'products' => ['repeater', 'Group Product Range', 'Tiles with photo and name.', 'title' => 'name', 'fields' => [
                            'name' => ['text', 'Name'],
                            'image' => ['image', 'Photo', 'Square photo, min. 500 px.'],
                        ]],
                        'image' => ['image', 'Infrastructure Photo', 'Group building photo.'],
                        'image_2' => ['image', 'Workshop Photo'],
                        'website' => ['text', 'Group Website', 'e.g. www.synergyalloys.com'],
                    ],
                ],
            ],
        ],

        /* ------------------------------------------------------------------ LEADERSHIP */
        'leadership' => [
            'label' => 'Leadership',
            'icon' => 'fa-solid fa-user-tie',
            'route' => 'leadership',
            'sections' => [
                'hero' => ['label' => 'Page Banner', 'icon' => 'fa-solid fa-panorama', 'fields' => $hero],
                'md_message' => [
                    'label' => "MD's Message",
                    'icon' => 'fa-solid fa-quote-left',
                    'fields' => $heading + [
                        'name' => ['text', 'Name'],
                        'designation' => ['text', 'Designation'],
                        'photo' => ['image', 'Photo', 'Portrait photo, about 600 × 750 px.'],
                        'message' => ['textarea', 'Message', 'Blank line = new paragraph.'],
                        'quote' => ['text', 'Highlighted Quote'],
                    ],
                ],
                'intro' => [
                    'label' => 'Introduction',
                    'icon' => 'fa-solid fa-align-left',
                    'fields' => $heading + ['text' => ['textarea', 'Text']],
                ],
                'leaders' => [
                    'label' => 'Leader Profiles',
                    'icon' => 'fa-solid fa-id-badge',
                    'fields' => [
                        'leaders' => ['repeater', 'Leaders', 'Detailed profiles shown one below the other.', 'title' => 'name', 'fields' => [
                            'name' => ['text', 'Name'],
                            'designation' => ['text', 'Designation'],
                            'location' => ['text', 'Based At'],
                            'photo' => ['image', 'Photo', 'Portrait photo, about 600 × 750 px.'],
                            'summary' => ['textarea', 'Profile Summary', 'Blank line = new paragraph.'],
                            'highlights' => ['lines', 'Career Highlights', 'One highlight per line.'],
                            'quote' => ['text', 'Personal Philosophy / Quote'],
                        ]],
                    ],
                ],
                'team' => [
                    'label' => 'Management Team',
                    'icon' => 'fa-solid fa-people-group',
                    'fields' => $heading + [
                        'text' => ['textarea', 'Introduction'],
                        'members' => ['repeater', 'Team Members', null, 'title' => 'name', 'fields' => [
                            'name' => ['text', 'Name'],
                            'designation' => ['text', 'Designation'],
                            'experience' => ['textarea', 'Experience'],
                            'years' => ['text', 'Years of Experience', 'Number only, e.g. 30'],
                            'photo' => ['image', 'Photo', 'Optional square photo. Initials are shown when empty.'],
                        ]],
                        'promoters' => ['textarea', 'Promoters Note'],
                    ],
                ],
            ],
        ],

        /* ------------------------------------------------------------------ PRODUCTS */
        'products' => [
            'label' => 'Products',
            'icon' => 'fa-solid fa-box',
            'route' => 'products.index',
            'extra_links' => [
                ['admin.products.index', 'admin.products.index', 'fa-solid fa-list', 'All Products'],
                ['admin.products.create', 'admin.products.create', 'fa-solid fa-plus', 'Add Product'],
                ['admin.categories.index', 'admin.categories.*', 'fa-solid fa-layer-group', 'Categories'],
            ],
            'sections' => [
                'hero' => ['label' => 'Page Banner', 'icon' => 'fa-solid fa-panorama', 'fields' => $hero],
                'intro' => [
                    'label' => 'Page Introduction',
                    'icon' => 'fa-solid fa-align-left',
                    'fields' => $heading + [
                        'text' => ['textarea', 'Text'],
                        'wear_modes' => ['pairs', 'Wear Mode Legend', 'One per line: "Code | Meaning", e.g. "A | Abrasion".'],
                    ],
                ],
                'spectrum' => [
                    'label' => 'Special Alloys Note',
                    'icon' => 'fa-solid fa-atom',
                    'fields' => $heading + ['text' => ['textarea', 'Text']],
                ],
            ],
        ],

        /* ------------------------------------------------------------------ INDUSTRIES */
        'industries' => [
            'label' => 'Industries & Services',
            'icon' => 'fa-solid fa-industry',
            'route' => 'industries',
            'sections' => [
                'hero' => ['label' => 'Page Banner', 'icon' => 'fa-solid fa-panorama', 'fields' => $hero],
                'intro' => [
                    'label' => 'Introduction',
                    'icon' => 'fa-solid fa-align-left',
                    'fields' => $heading + ['text' => ['textarea', 'Text']],
                ],
                'sectors' => [
                    'label' => 'Industry Sectors',
                    'icon' => 'fa-solid fa-city',
                    'fields' => [
                        'sectors' => ['repeater', 'Sectors', 'Each sector gets a tab with its key jobs.', 'title' => 'name', 'fields' => [
                            'name' => ['text', 'Sector Name'],
                            'icon' => $icon,
                            'summary' => ['textarea', 'Short Description'],
                            'jobs' => ['lines', 'Key Jobs & Services', 'One job per line.'],
                            'image' => ['image', 'Photo', 'Landscape photo, min. 1000 px wide.'],
                        ]],
                    ],
                ],
                'rolls' => [
                    'label' => 'Roll Reconditioning Table',
                    'icon' => 'fa-solid fa-table',
                    'fields' => $heading + [
                        'text' => ['textarea', 'Introduction'],
                        'table' => ['table', 'Table', 'One row per line, columns separated by "|". The first line is the header row.'],
                        'note' => ['textarea', 'Note Below Table'],
                    ],
                ],
                'capability' => [
                    'label' => 'Rebuilding Capability',
                    'icon' => 'fa-solid fa-screwdriver-wrench',
                    'fields' => $heading + [
                        'text' => ['textarea', 'Text'],
                        'points' => ['lines', 'Processes', 'One per line.'],
                        'image' => ['image', 'Photo'],
                    ],
                ],
            ],
        ],

        /* ------------------------------------------------------------------ INFRASTRUCTURE */
        'infrastructure' => [
            'label' => 'Infrastructure',
            'icon' => 'fa-solid fa-warehouse',
            'route' => 'infrastructure',
            'sections' => [
                'hero' => ['label' => 'Page Banner', 'icon' => 'fa-solid fa-panorama', 'fields' => $hero],
                'overview' => [
                    'label' => 'Plant Overview',
                    'icon' => 'fa-solid fa-industry',
                    'fields' => $heading + [
                        'text' => ['textarea', 'Text'],
                        'image' => ['image', 'Plant Photo', 'Min. 1000 px wide.'],
                    ],
                ],
                'capacity' => [
                    'label' => 'Production Capacity',
                    'icon' => 'fa-solid fa-gauge-high',
                    'fields' => $heading + [
                        'items' => ['repeater', 'Capacity Figures', null, 'title' => 'label', 'fields' => [
                            'value' => ['text', 'Number'],
                            'suffix' => ['text', 'Unit', 'e.g. m², m, T'],
                            'label' => ['text', 'Label'],
                            'icon' => $icon,
                        ]],
                    ],
                ],
                'facilities' => [
                    'label' => 'Facilities',
                    'icon' => 'fa-solid fa-list-check',
                    'fields' => $heading + [
                        'text' => ['textarea', 'Introduction'],
                        'items' => ['repeater', 'Facilities', null, 'title' => 'title', 'fields' => [
                            'icon' => $icon,
                            'title' => ['text', 'Title'],
                            'text' => ['textarea', 'Details'],
                        ]],
                    ],
                ],
                'process' => [
                    'label' => 'Manufacturing Process',
                    'icon' => 'fa-solid fa-robot',
                    'fields' => $heading + [
                        'text' => ['textarea', 'Text'],
                        'steps' => ['repeater', 'Process Steps', null, 'title' => 'title', 'fields' => [
                            'title' => ['text', 'Title'],
                            'text' => ['textarea', 'Text'],
                            'image' => ['image', 'Photo', 'Optional.'],
                        ]],
                    ],
                ],
                'machines' => [
                    'label' => 'Machinery List',
                    'icon' => 'fa-solid fa-table-list',
                    'fields' => $heading + [
                        'text' => ['textarea', 'Introduction'],
                        'table' => ['table', 'Machines Table', 'One machine per line, columns separated by "|". First line is the header.'],
                    ],
                ],
                'design' => [
                    'label' => 'Design & Engineering',
                    'icon' => 'fa-solid fa-compass-drafting',
                    'fields' => $heading + [
                        'text' => ['textarea', 'Text'],
                        'drawings' => ['repeater', 'Drawings', 'Engineering drawings shown in a gallery.', 'title' => 'title', 'fields' => [
                            'title' => ['text', 'Title'],
                            'image' => ['image', 'Drawing Image'],
                        ]],
                    ],
                ],
                'expansion' => [
                    'label' => 'Capacity Expansion',
                    'icon' => 'fa-solid fa-rocket',
                    'fields' => $heading + [
                        'text' => ['textarea', 'Text'],
                        'points' => ['lines', 'Highlights', 'One per line.'],
                        'image' => ['image', 'Photo', 'Optional.'],
                    ],
                ],
                'gallery' => [
                    'label' => 'Plant Gallery',
                    'icon' => 'fa-solid fa-photo-film',
                    'fields' => $heading + [
                        'photos' => ['repeater', 'Photos', null, 'title' => 'caption', 'fields' => [
                            'caption' => ['text', 'Caption'],
                            'image' => ['image', 'Photo'],
                        ]],
                    ],
                ],
            ],
        ],

        /* ------------------------------------------------------------------ QUALITY */
        'quality' => [
            'label' => 'Quality & Certifications',
            'icon' => 'fa-solid fa-award',
            'route' => 'quality',
            'sections' => [
                'hero' => ['label' => 'Page Banner', 'icon' => 'fa-solid fa-panorama', 'fields' => $hero],
                'commitment' => [
                    'label' => 'Quality Commitment',
                    'icon' => 'fa-solid fa-medal',
                    'fields' => $heading + [
                        'text' => ['textarea', 'Text'],
                        'points' => ['lines', 'Key Points', 'One per line.'],
                        'image' => ['image', 'Photo'],
                    ],
                ],
                'certificates' => [
                    'label' => 'Certificates',
                    'icon' => 'fa-solid fa-certificate',
                    'fields' => $heading + [
                        'text' => ['textarea', 'Introduction'],
                        'items' => ['repeater', 'Certificates', 'Upload a clear scan of each certificate (portrait, min. 1000 px tall).', 'title' => 'title', 'fields' => [
                            'title' => ['text', 'Certificate'],
                            'issuer' => ['text', 'Issued By'],
                            'number' => ['text', 'Certificate / Registration No.'],
                            'validity' => ['text', 'Validity'],
                            'scope' => ['textarea', 'Scope / Details'],
                            'image' => ['image', 'Certificate Scan'],
                        ]],
                    ],
                ],
                'qap' => [
                    'label' => 'Quality Assurance Plan',
                    'icon' => 'fa-solid fa-clipboard-check',
                    'fields' => $heading + [
                        'text' => ['textarea', 'Introduction'],
                        'steps' => ['repeater', 'Inspection Stages', null, 'title' => 'title', 'fields' => [
                            'stage' => ['text', 'Stage'],
                            'title' => ['text', 'Check'],
                            'norm' => ['textarea', 'Acceptance Norm'],
                            'record' => ['text', 'Record'],
                        ]],
                        'note' => ['textarea', 'Note'],
                    ],
                ],
                'testing' => [
                    'label' => 'Testing',
                    'icon' => 'fa-solid fa-flask',
                    'fields' => $heading + [
                        'text' => ['textarea', 'Text'],
                        'items' => ['repeater', 'Tests', null, 'title' => 'title', 'fields' => [
                            'icon' => $icon,
                            'title' => ['text', 'Title'],
                            'text' => ['textarea', 'Text'],
                        ]],
                        'image' => ['image', 'Testing Lab Photo'],
                    ],
                ],
            ],
        ],

        /* ------------------------------------------------------------------ PROJECTS */
        'projects' => [
            'label' => 'Projects',
            'icon' => 'fa-solid fa-diagram-project',
            'route' => 'projects',
            'sections' => [
                'hero' => ['label' => 'Page Banner', 'icon' => 'fa-solid fa-panorama', 'fields' => $hero],
                'intro' => [
                    'label' => 'Introduction',
                    'icon' => 'fa-solid fa-align-left',
                    'fields' => $heading + ['text' => ['textarea', 'Text']],
                ],
                'projects' => [
                    'label' => 'Iconic Projects',
                    'icon' => 'fa-solid fa-trophy',
                    'fields' => [
                        'projects' => ['repeater', 'Projects', 'The first 3 also appear on the home page.', 'title' => 'title', 'fields' => [
                            'title' => ['text', 'Project Title'],
                            'category' => ['text', 'Category', 'e.g. Heavy Fabrication, Wear Tubes'],
                            'summary' => ['textarea', 'Description', 'Blank line = new paragraph.'],
                            'specs' => ['pairs', 'Project Details', 'One per line: "Label | Value", e.g. "Weight | 16 ton".'],
                            'image' => ['image', 'Main Photo', 'Landscape, min. 1200 px wide.'],
                            'image_2' => ['image', 'Second Photo', 'Optional.'],
                        ]],
                    ],
                ],
                'capability' => [
                    'label' => 'Fabrication Capability',
                    'icon' => 'fa-solid fa-helmet-safety',
                    'fields' => $heading + [
                        'text' => ['textarea', 'Text'],
                        'button_text' => ['text', 'Button Text'],
                        'image' => ['image', 'Background Photo'],
                    ],
                ],
            ],
        ],

        /* ------------------------------------------------------------------ RESOURCES */
        'resources' => [
            'label' => 'Technical Resources',
            'icon' => 'fa-solid fa-book-open',
            'route' => 'resources',
            'sections' => [
                'hero' => ['label' => 'Page Banner', 'icon' => 'fa-solid fa-panorama', 'fields' => $hero],
                'hardfacing' => [
                    'label' => 'What is Hardfacing',
                    'icon' => 'fa-solid fa-fire-flame-curved',
                    'fields' => $heading + [
                        'lead' => ['textarea', 'Lead Text'],
                        'text' => ['textarea', 'Article', 'Blank line = new paragraph. **bold** supported.'],
                        'facts' => ['repeater', 'Key Facts', null, 'title' => 'label', 'fields' => [
                            'value' => ['text', 'Figure'],
                            'label' => ['text', 'Label'],
                        ]],
                        'image' => ['image', 'Photo'],
                    ],
                ],
                'wear' => [
                    'label' => 'Types of Wear',
                    'icon' => 'fa-solid fa-burst',
                    'fields' => $heading + [
                        'text' => ['textarea', 'Introduction'],
                        'types' => ['repeater', 'Wear Mechanisms', null, 'title' => 'title', 'fields' => [
                            'icon' => $icon,
                            'title' => ['text', 'Title'],
                            'text' => ['textarea', 'Text'],
                        ]],
                    ],
                ],
                'mining' => [
                    'label' => 'Earth Moving & Mining',
                    'icon' => 'fa-solid fa-truck-monster',
                    'fields' => $heading + [
                        'text' => ['textarea', 'Text'],
                        'image' => ['image', 'Photo'],
                    ],
                ],
                'world' => [
                    'label' => 'World of Wear Plates',
                    'icon' => 'fa-solid fa-layer-group',
                    'fields' => $heading + [
                        'text' => ['textarea', 'Introduction'],
                        'tiers' => ['repeater', 'Tiers', 'Shown as a pyramid, top to bottom.', 'title' => 'name', 'fields' => [
                            'name' => ['text', 'Tier Name'],
                            'items' => ['lines', 'Items', 'One per line.'],
                        ]],
                    ],
                ],
                'applications' => [
                    'label' => 'Proven Applications',
                    'icon' => 'fa-solid fa-check-double',
                    'fields' => $heading + [
                        'text' => ['textarea', 'Introduction'],
                        'items' => ['repeater', 'Applications', null, 'title' => 'title', 'fields' => [
                            'title' => ['text', 'Title'],
                            'image' => ['image', 'Photo'],
                        ]],
                    ],
                ],
            ],
        ],

        /* ------------------------------------------------------------------ WHY */
        'why' => [
            'label' => 'Why Choose Us',
            'icon' => 'fa-solid fa-thumbs-up',
            'route' => 'why',
            'sections' => [
                'hero' => ['label' => 'Page Banner', 'icon' => 'fa-solid fa-panorama', 'fields' => $hero],
                'reasons' => [
                    'label' => 'Reasons',
                    'icon' => 'fa-solid fa-list-ol',
                    'fields' => $heading + [
                        'text' => ['textarea', 'Introduction'],
                        'items' => ['repeater', 'Reasons', null, 'title' => 'title', 'fields' => [
                            'icon' => $icon,
                            'title' => ['text', 'Title'],
                            'text' => ['textarea', 'Text'],
                        ]],
                    ],
                ],
                'promise' => [
                    'label' => 'Our Promise',
                    'icon' => 'fa-solid fa-handshake',
                    'fields' => $heading + [
                        'text' => ['textarea', 'Text'],
                        'points' => ['lines', 'Points', 'One per line.'],
                        'image' => ['image', 'Photo'],
                    ],
                ],
            ],
        ],

        /* ------------------------------------------------------------------ PRESENCE */
        'presence' => [
            'label' => 'Our Presence',
            'icon' => 'fa-solid fa-map-location-dot',
            'route' => 'presence',
            'extra_links' => [
                ['admin.locations.index', 'admin.locations.*', 'fa-solid fa-location-dot', 'Offices & Locations'],
            ],
            'sections' => [
                'hero' => ['label' => 'Page Banner', 'icon' => 'fa-solid fa-panorama', 'fields' => $hero],
                'intro' => [
                    'label' => 'Introduction',
                    'icon' => 'fa-solid fa-align-left',
                    'fields' => $heading + [
                        'text' => ['textarea', 'Text'],
                        'network' => ['lines', 'Service Network Cities', 'Cities served by our network (shown as tags). One per line.'],
                    ],
                ],
            ],
        ],

        /* ------------------------------------------------------------------ CONTACT */
        'contact' => [
            'label' => 'Contact Us',
            'icon' => 'fa-solid fa-envelope',
            'route' => 'contact',
            'sections' => [
                'hero' => ['label' => 'Page Banner', 'icon' => 'fa-solid fa-panorama', 'fields' => $hero],
                'intro' => [
                    'label' => 'Introduction',
                    'icon' => 'fa-solid fa-align-left',
                    'fields' => $heading + [
                        'text' => ['textarea', 'Text'],
                        'offices' => ['repeater', 'Office Addresses', 'Full addresses shown on the contact page.', 'title' => 'name', 'fields' => [
                            'name' => ['text', 'Office Name'],
                            'address' => ['textarea', 'Address'],
                            'phone' => ['text', 'Phone'],
                            'email' => ['text', 'Email'],
                        ]],
                    ],
                ],
            ],
        ],
    ],
];
