<?php

namespace Tests\Feature;

use App\Models\Enquiry;
use App\Models\Product;
use App\Models\ProductCategory;
use Database\Seeders\CatalogueSeeder;
use Database\Seeders\LocationSeeder;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebsiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([SettingSeeder::class, CatalogueSeeder::class, LocationSeeder::class, \Database\Seeders\ContentSeeder::class]);
    }

    public function test_all_public_pages_render(): void
    {
        $category = ProductCategory::first();
        $product = $category->products()->first();

        foreach ([
            route('home'), route('about'), route('why'), route('presence'), route('contact'),
            route('leadership'), route('industries'), route('infrastructure'), route('quality'), route('projects'), route('resources'),
            route('products.index'), route('products.category', $category),
            route('products.show', [$category, $product]), route('sitemap'),
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_home_page_shows_company_details_from_settings(): void
    {
        $this->get('/')
            ->assertSee('The Leader in Wear Protection')
            ->assertSee('Since 2008')
            ->assertSee('ISO 9001:2015')
            ->assertSee('MSME Registered')
            ->assertSee('Our Products &amp; Solutions', false)
            ->assertSee('Make in India')
            ->assertSee('+91 7981795006')
            ->assertSee('24AGWPD5947K1ZT')
            ->assertSee('Blue Dart')
            ->assertSee('wa.me/917981795006', false);
    }

    public function test_presence_page_lists_all_offices(): void
    {
        $response = $this->get(route('presence'))->assertOk();
        foreach (['Mumbai', 'Ankleshwar', 'Ahmedabad', 'Kolkata', 'Hyderabad', 'Guwahati', 'Palampur', 'Jamshedpur', 'Noida', 'Prayagraj', 'Chittorgarh'] as $city) {
            $response->assertSee($city);
        }
    }

    public function test_product_under_wrong_category_returns_404(): void
    {
        $product = Product::first();
        $other = ProductCategory::whereKeyNot($product->product_category_id)->first();

        $this->get(route('products.show', [$other, $product]))->assertNotFound();
    }

    public function test_hidden_products_and_categories_are_not_public(): void
    {
        $product = Product::with('category')->first();
        $product->update(['is_active' => false]);
        $this->get(route('products.show', [$product->category, $product]))->assertNotFound();

        $product->category->update(['is_active' => false]);
        $this->get(route('products.category', $product->category))->assertNotFound();
    }

    public function test_pages_render_completely_when_optional_content_is_empty(): void
    {
        // Regression: a null @section value used to leave an output buffer open.
        \App\Models\Setting::query()->where('group', 'seo')->update(['value' => null]);
        \Illuminate\Support\Facades\Cache::flush();
        $category = ProductCategory::first();
        $category->update(['short_description' => null, 'description' => null, 'meta_description' => null]);
        $product = $category->products()->first();
        $product->update(['short_description' => null, 'description' => null, 'meta_description' => null]);

        foreach ([route('about'), route('why'), route('presence'), route('contact'), route('products.index'),
                  route('products.category', $category), route('products.show', [$category, $product])] as $url) {
            $level = ob_get_level();
            $html = $this->get($url)->assertOk()->getContent();
            $this->assertSame($level, ob_get_level(), "Output buffer left open on $url");
            $this->assertStringEndsWith('</html>', trim($html), "Incomplete page: $url");
        }
    }

    public function test_google_map_is_only_shown_when_configured(): void
    {
        $this->get(route('contact'))->assertDontSee('google.com/maps/embed', false);
    }

    public function test_enquiry_is_saved_to_database(): void
    {
        $product = Product::first();

        $this->post(route('contact.store'), [
            'name' => 'Ravi Kumar',
            'company_name' => 'Test Steel',
            'mobile' => '+91 98765 43210',
            'email' => 'ravi@example.com',
            'subject' => 'Wear liner enquiry',
            'message' => 'Please share details for our chute liners.',
            'product_id' => $product->id,
        ])->assertRedirect()->assertSessionHas('success');

        $this->assertDatabaseHas('enquiries', [
            'name' => 'Ravi Kumar',
            'mobile' => '+91 98765 43210',
            'status' => 'new',
            'product_id' => $product->id,
        ]);
    }

    public function test_enquiry_validation(): void
    {
        $this->from(route('contact'))->post(route('contact.store'), [
            'name' => '', 'mobile' => 'abc', 'email' => 'not-an-email', 'subject' => '', 'message' => 'short',
        ])->assertRedirect(route('contact'))
            ->assertSessionHasErrors(['name', 'mobile', 'email', 'subject', 'message']);

        $this->assertSame(0, Enquiry::count());
    }

    public function test_honeypot_blocks_bots(): void
    {
        $this->post(route('contact.store'), [
            'name' => 'Bot', 'mobile' => '9876543210', 'subject' => 'Spam', 'message' => 'Buy cheap things now', 'website' => 'http://spam.test',
        ])->assertSessionHasErrors('website');

        $this->assertSame(0, Enquiry::count());
    }
}
