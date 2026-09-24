<?php

namespace Tests\Feature;

use App\Livewire\Setting\SettingIndex;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class SettingManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_settings_page(): void
    {
        $response = $this->get('/settings');
        $response->assertRedirect('/login');
    }

    public function test_cashier_cannot_access_settings_page(): void
    {
        $cashier = User::factory()->create([
            'role' => 'cashier',
            'is_active' => true,
        ]);

        $response = $this->actingAs($cashier)->get('/settings');
        $response->assertStatus(403);
    }

    public function test_admin_can_view_settings_page(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get('/settings');
        $response->assertStatus(200);
        $response->assertSee('Pengaturan Sistem');
        $response->assertSee('Profil Toko & Struk', false);
    }

    public function test_admin_can_update_store_profile_and_logo(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $logo = UploadedFile::fake()->image('store_logo.png', 200, 200);

        Livewire::actingAs($admin)
            ->test(SettingIndex::class)
            ->set('store_name', 'Cafe Nusantara')
            ->set('store_tagline', 'Kopi Asli Indonesia')
            ->set('store_address', 'Jl. Sudirman No. 45, Bandung')
            ->set('store_phone', '0811-2233-4455')
            ->set('store_logo', $logo)
            ->set('receipt_footer', 'Sampai Jumpa Kembali!')
            ->call('saveStoreSettings')
            ->assertHasNoErrors();

        $this->assertEquals('Cafe Nusantara', Setting::get('store_name'));
        $this->assertEquals('Kopi Asli Indonesia', Setting::get('store_tagline'));
        $this->assertEquals('Jl. Sudirman No. 45, Bandung', Setting::get('store_address'));
        $this->assertEquals('0811-2233-4455', Setting::get('store_phone'));
        $this->assertEquals('Sampai Jumpa Kembali!', Setting::get('receipt_footer'));

        $savedLogo = Setting::get('store_logo');
        $this->assertNotNull($savedLogo);
        Storage::disk('public')->assertExists($savedLogo);
    }

    public function test_admin_can_update_tax_and_service_charge_settings(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(SettingIndex::class)
            ->set('tax_enabled', true)
            ->set('tax_name', 'PB1')
            ->set('tax_percentage', 10)
            ->set('service_charge_enabled', true)
            ->set('service_charge_percentage', 5)
            ->call('saveTaxSettings')
            ->assertHasNoErrors();

        $this->assertEquals('1', Setting::get('tax_enabled'));
        $this->assertEquals('PB1', Setting::get('tax_name'));
        $this->assertEquals('10', Setting::get('tax_percentage'));
        $this->assertEquals('1', Setting::get('service_charge_enabled'));
        $this->assertEquals('5', Setting::get('service_charge_percentage'));
    }

    public function test_admin_can_update_payment_and_qris_image(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $qris = UploadedFile::fake()->image('qris_banner.png', 500, 500);

        Livewire::actingAs($admin)
            ->test(SettingIndex::class)
            ->set('bank_name', 'Bank Mandiri')
            ->set('bank_account_number', '9876543210')
            ->set('bank_account_holder', 'CV Cafe Nusantara')
            ->set('qris_image', $qris)
            ->call('savePaymentSettings')
            ->assertHasNoErrors();

        $this->assertEquals('Bank Mandiri', Setting::get('bank_name'));
        $this->assertEquals('9876543210', Setting::get('bank_account_number'));
        $this->assertEquals('CV Cafe Nusantara', Setting::get('bank_account_holder'));

        $savedQris = Setting::get('qris_image');
        $this->assertNotNull($savedQris);
        Storage::disk('public')->assertExists($savedQris);
    }
}
