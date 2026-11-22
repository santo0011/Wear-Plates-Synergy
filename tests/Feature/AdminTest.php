<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Enquiry;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Setting;
use Database\Seeders\CatalogueSeeder;
use Database\Seeders\LocationSeeder;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([SettingSeeder::class, CatalogueSeeder::class, LocationSeeder::class]);
        $this->admin = Admin::create(['name' => 'Admin', 'email' => 'admin@test.local', 'password' => 'Secret-Pass-123']);
        Storage::fake('uploads');
    }

    public function test_guests_are_redirected_from_admin_routes(): void
    {
        foreach (['admin.dashboard', 'admin.categories.index', 'admin.products.index', 'admin.enquiries.index', 'admin.locations.index', 'admin.profile.edit'] as $name) {
            $this->get(route($name))->assertRedirect(route('admin.login'));
        }
        $this->put(route('admin.settings.update', 'contact'), [])->assertRedirect(route('admin.login'));
    }

    public function test_admin_password_is_hashed(): void
    {
        $this->assertNotSame('Secret-Pass-123', $this->admin->getRawOriginal('password'));
        $this->assertTrue(password_verify('Secret-Pass-123', $this->admin->getRawOriginal('password')));
    }

    public function test_login_and_logout(): void
    {
        $this->post(route('admin.login.attempt'), ['email' => 'admin@test.local', 'password' => 'wrong'])
            ->assertSessionHasErrors('email');
        $this->assertGuest('admin');

        $this->post(route('admin.login.attempt'), ['email' => 'admin@test.local', 'password' => 'Secret-Pass-123'])
            ->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($this->admin, 'admin');

        $this->post(route('admin.logout'))->assertRedirect(route('admin.login'));
        $this->assertGuest('admin');
    }

    public function test_inactive_admin_cannot_log_in(): void
    {
        $this->admin->update(['is_active' => false]);
        $this->post(route('admin.login.attempt'), ['email' => 'admin@test.local', 'password' => 'Secret-Pass-123'])
            ->assertSessionHasErrors('email');
        $this->assertGuest('admin');
    }

    public function test_login_is_rate_limited(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('admin.login.attempt'), ['email' => 'admin@test.local', 'password' => 'wrong']);
        }
        $this->post(route('admin.login.attempt'), ['email' => 'admin@test.local', 'password' => 'Secret-Pass-123'])
            ->assertSessionHasErrors('email');
        $this->assertGuest('admin');
    }

    public function test_all_admin_pages_render(): void
    {
        $this->actingAs($this->admin, 'admin');
        $category = ProductCategory::first();
        $product = Product::first();

        $urls = [
            route('admin.dashboard'), route('admin.categories.index'), route('admin.categories.create'),
            route('admin.categories.edit', $category), route('admin.products.index'), route('admin.products.create'),
            route('admin.products.edit', $product), route('admin.locations.index'), route('admin.locations.create'),
            route('admin.enquiries.index'), route('admin.profile.edit'),
        ];
        foreach (array_keys(Setting::GROUPS) as $group) {
            $urls[] = route('admin.settings.edit', $group);
        }
        foreach ($urls as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_category_and_product_crud_with_image_upload(): void
    {
        $this->actingAs($this->admin, 'admin');

        $this->post(route('admin.categories.store'), [
            'name' => 'Test Category', 'is_active' => 1, 'icon' => 'fa-solid fa-gear',
            'image' => UploadedFile::fake()->create('cat.png', 10, 'image/png'),
        ])->assertSessionHasErrors('image'); // fake non-image content is rejected

        $this->post(route('admin.categories.store'), ['name' => 'Test Category', 'is_active' => 1, 'icon' => 'fa-solid fa-gear'])
            ->assertRedirect(route('admin.categories.index'));
        $category = ProductCategory::where('slug', 'test-category')->firstOrFail();

        $png = UploadedFile::fake()->createWithContent('p.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='));
        $this->post(route('admin.products.store'), [
            'product_category_id' => $category->id, 'name' => 'Test Product', 'is_active' => 1, 'is_featured' => 1, 'image' => $png,
        ])->assertRedirect(route('admin.products.index'));

        $product = Product::where('slug', 'test-product')->firstOrFail();
        $this->assertStringStartsWith('uploads/products/', $product->image);
        Storage::disk('uploads')->assertExists(substr($product->image, strlen('uploads/')));
        $this->get(route('products.show', [$category, $product]))->assertOk()->assertSee('Test Product');

        // Category with products cannot be deleted
        $this->delete(route('admin.categories.destroy', $category))->assertSessionHas('error');
        $this->assertModelExists($category);

        $this->put(route('admin.products.update', $product), [
            'product_category_id' => $category->id, 'name' => 'Renamed Product', 'slug' => 'test-product', 'remove_image' => 1,
        ])->assertRedirect(route('admin.products.index'));
        $product->refresh();
        $this->assertSame('Renamed Product', $product->name);
        $this->assertNull($product->image);
        $this->assertFalse($product->is_active);

        $this->delete(route('admin.products.destroy', $product))->assertRedirect();
        $this->delete(route('admin.categories.destroy', $category))->assertRedirect(route('admin.categories.index'));
        $this->assertModelMissing($category);
    }

    public function test_enquiry_status_management(): void
    {
        $this->actingAs($this->admin, 'admin');
        $enquiry = Enquiry::create(['name' => 'A', 'mobile' => '9876543210', 'subject' => 'Test', 'message' => 'Test message here']);

        $this->get(route('admin.enquiries.show', $enquiry))->assertOk()->assertSee('Test message here');
        $this->assertNotNull($enquiry->fresh()->read_at);

        $this->patch(route('admin.enquiries.update', $enquiry), ['status' => 'invalid'])->assertSessionHasErrors('status');
        $this->patch(route('admin.enquiries.update', $enquiry), ['status' => 'quoted', 'admin_notes' => 'Sent'])->assertRedirect();
        $this->assertSame('quoted', $enquiry->fresh()->status);

        $this->get(route('admin.enquiries.index', ['status' => 'quoted']))->assertSee('Test');
        $this->delete(route('admin.enquiries.destroy', $enquiry))->assertRedirect(route('admin.enquiries.index'));
        $this->assertModelMissing($enquiry);
    }

    public function test_settings_update_is_reflected_on_website(): void
    {
        $this->actingAs($this->admin, 'admin');

        $this->put(route('admin.settings.update', 'contact'), ['settings' => [
            'phone' => '+91 7981795006', 'whatsapp_number' => '+91 7981795006', 'email' => 'sales@example.com',
            'gstin' => '24AGWPD5947K1ZT', 'corporate_office' => 'Mumbai', 'plant_location' => 'GIDC, Ankleshwar, Gujarat',
        ]])->assertRedirect(route('admin.settings.edit', 'contact'));

        $this->get(route('contact'))->assertSee('sales@example.com');

        $this->put(route('admin.settings.update', 'contact'), ['settings' => ['google_map_embed' => 'https://evil.test/map']])
            ->assertSessionHasErrors('settings.google_map_embed');
    }

    public function test_profile_requires_current_password(): void
    {
        $this->actingAs($this->admin, 'admin');

        $this->put(route('admin.profile.update'), ['name' => 'X', 'email' => 'admin@test.local', 'current_password' => 'wrong'])
            ->assertSessionHasErrors('current_password');

        $this->put(route('admin.profile.update'), [
            'name' => 'New Name', 'email' => 'admin@test.local', 'current_password' => 'Secret-Pass-123',
            'password' => 'Another-Pass-456', 'password_confirmation' => 'Another-Pass-456',
        ])->assertSessionHasNoErrors();

        $this->assertTrue(password_verify('Another-Pass-456', $this->admin->fresh()->getRawOriginal('password')));
    }
}
