<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\PageSection;
use App\Support\Cms;
use Database\Seeders\CatalogueSeeder;
use Database\Seeders\ContentSeeder;
use Database\Seeders\LocationSeeder;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ContentEditorTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    private const PNG = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([SettingSeeder::class, CatalogueSeeder::class, LocationSeeder::class, ContentSeeder::class]);
        $this->admin = Admin::create(['name' => 'Admin', 'email' => 'admin@test.local', 'password' => 'Secret-Pass-123']);
        Storage::fake('uploads');
    }

    public function test_every_registered_section_is_seeded_and_editable(): void
    {
        $this->actingAs($this->admin, 'admin');

        foreach (Cms::pages() as $page => $def) {
            foreach (array_keys($def['sections']) as $section) {
                $this->assertDatabaseHas('page_sections', ['page' => $page, 'section' => $section]);
                $this->get(route('admin.content.edit', [$page, $section]))
                    ->assertOk()
                    ->assertSee($def['sections'][$section]['label']);
            }
        }
    }

    public function test_sidebar_lists_pages_and_their_sections(): void
    {
        $this->actingAs($this->admin, 'admin');

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Website Pages')
            ->assertSee('Hero Slider')
            ->assertSee('Iconic Projects')
            ->assertSee('General &amp; Logos', false);
    }

    public function test_unknown_section_returns_404_and_guests_are_redirected(): void
    {
        $this->get(route('admin.content.edit', ['home', 'hero']))->assertRedirect(route('admin.login'));

        $this->actingAs($this->admin, 'admin');
        $this->get(route('admin.content.edit', ['home', 'nope']))->assertNotFound();
        $this->get(route('admin.content.edit', ['nope', 'hero']))->assertNotFound();
    }

    public function test_simple_section_update_is_shown_on_the_website(): void
    {
        $this->actingAs($this->admin, 'admin');

        $this->put(route('admin.content.update', ['about', 'profile']), [
            'is_active' => 1,
            'content' => [
                'eyebrow' => 'Company Profile',
                'heading' => 'Updated **Heading**',
                'lead' => 'A new lead paragraph.',
                'text' => "First paragraph.\n\nSecond paragraph.",
                'points' => "One\nTwo",
            ],
        ])->assertRedirect(route('admin.content.edit', ['about', 'profile']));

        $this->get(route('about'))
            ->assertOk()
            ->assertSee('Updated <span class="hl">Heading</span>', false)
            ->assertSee('<p>Second paragraph.</p>', false);
    }

    public function test_hidden_section_is_not_rendered(): void
    {
        $this->actingAs($this->admin, 'admin');
        $this->put(route('admin.content.update', ['home', 'clients']), ['is_active' => 0, 'content' => ['heading' => 'Our clients']])
            ->assertRedirect();

        $this->get('/')->assertOk()->assertDontSee('id="sec-clients"', false);
    }

    public function test_repeater_items_are_saved_in_order_with_image_uploads(): void
    {
        $this->actingAs($this->admin, 'admin');

        $this->put(route('admin.content.update', ['home', 'hero']), [
            'is_active' => 1,
            'content' => ['slides' => [
                'n2' => ['title' => 'Second slide', 'text' => 'B'],
                'n1' => ['title' => 'First slide', 'text' => 'A'],
                'n3' => ['title' => '', 'text' => ''], // empty rows are dropped
            ]],
        ])->assertRedirect();

        $slides = PageSection::where(['page' => 'home', 'section' => 'hero'])->first()->content['slides'];
        $this->assertSame(['Second slide', 'First slide'], array_column($slides, 'title'));

        // Upload an image to the first slide
        $png = UploadedFile::fake()->createWithContent('slide.png', base64_decode(self::PNG));
        $this->put(route('admin.content.update', ['home', 'hero']), [
            'is_active' => 1,
            'content' => ['slides' => ['i0' => ['title' => 'Second slide', 'image' => $png], 'i1' => ['title' => 'First slide']]],
        ])->assertSessionHasNoErrors()->assertRedirect();

        $slides = PageSection::where(['page' => 'home', 'section' => 'hero'])->first()->content['slides'];
        $path = $slides[0]['image'];
        $this->assertStringStartsWith('uploads/content/', $path);
        Storage::disk('uploads')->assertExists(substr($path, strlen('uploads/')));
        $this->get('/')->assertSee($path, false);

        // Saving again keeps the image via the hidden "__current" field…
        $this->put(route('admin.content.update', ['home', 'hero']), [
            'is_active' => 1,
            'content' => ['slides' => ['i0' => ['title' => 'Second slide', 'image__current' => $path]]],
        ])->assertRedirect();
        $this->assertSame($path, PageSection::where(['page' => 'home', 'section' => 'hero'])->first()->content['slides'][0]['image']);
        Storage::disk('uploads')->assertExists(substr($path, strlen('uploads/')));

        // …and removing the item deletes the unused file.
        $this->put(route('admin.content.update', ['home', 'hero']), [
            'is_active' => 1,
            'content' => ['slides' => ['i0' => ['title' => 'Only slide']]],
        ])->assertRedirect();
        Storage::disk('uploads')->assertMissing(substr($path, strlen('uploads/')));
    }

    public function test_non_image_upload_is_rejected(): void
    {
        $this->actingAs($this->admin, 'admin');

        $this->put(route('admin.content.update', ['about', 'md_message']), [
            'is_active' => 1, 'content' => ['name' => 'D. Deepti', 'photo' => UploadedFile::fake()->create('cv.pdf', 20, 'application/pdf')],
        ])
            ->assertSessionHasErrors('content.photo');
    }

    public function test_header_logo_strip_replaces_individual_logos_in_header(): void
    {
        $this->actingAs($this->admin, 'admin');
        $png = UploadedFile::fake()->createWithContent('strip.png', base64_decode(self::PNG));

        $this->put(route('admin.settings.update', 'general'), [
            'settings' => ['site_name' => 'Wear Plates Synergy VE India', 'make_in_india' => 1, 'iso_certified' => 1, 'header_logo_strip' => $png],
        ])->assertSessionHasNoErrors()->assertRedirect();

        $strip = \App\Models\Setting::where('key', 'header_logo_strip')->value('value');
        $this->assertStringStartsWith('uploads/settings/', $strip);
        $this->get('/')->assertSee('class="cert-strip"', false)->assertSee($strip, false);
    }

    public function test_header_shows_uploaded_certification_logos(): void
    {
        $this->actingAs($this->admin, 'admin');
        $png = UploadedFile::fake()->createWithContent('mii.png', base64_decode(self::PNG));

        $this->put(route('admin.settings.update', 'general'), [
            'settings' => ['site_name' => 'Wear Plates Synergy VE India', 'make_in_india' => 1, 'iso_certified' => 1, 'make_in_india_logo' => $png],
        ])->assertSessionHasNoErrors()->assertRedirect();

        $logo = \App\Models\Setting::where('key', 'make_in_india_logo')->value('value');
        $this->assertStringStartsWith('uploads/settings/', $logo);
        $this->get('/')->assertSee($logo, false)->assertSee('alt="Make in India"', false);
    }
}
