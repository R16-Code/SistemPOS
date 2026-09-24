<?php

namespace App\Livewire\Setting;

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Pengaturan Sistem - BrewPOS')]
class SettingIndex extends Component
{
    use WithFileUploads;

    public string $activeTab = 'store';

    // Group 1: Store Profile & Receipt
    public string $store_name = '';

    public string $store_tagline = '';

    public string $store_address = '';

    public string $store_phone = '';

    public string $store_email = '';

    public mixed $store_logo = null;

    public ?string $existing_store_logo = null;

    public string $receipt_footer = '';

    // Group 2: Tax & Service Charge
    public bool $tax_enabled = true;

    public string $tax_name = 'PPN';

    public float $tax_percentage = 11;

    public bool $service_charge_enabled = false;

    public float $service_charge_percentage = 5;

    // Group 3: Payment & QRIS
    public string $bank_name = 'BCA';

    public string $bank_account_number = '';

    public string $bank_account_holder = '';

    public mixed $qris_image = null;

    public ?string $existing_qris_image = null;

    public string $successMessage = '';

    public function mount(): void
    {
        // Load Store Profile
        $this->store_name = (string) Setting::get('store_name', Setting::DEFAULTS['store_name']);
        $this->store_tagline = (string) Setting::get('store_tagline', Setting::DEFAULTS['store_tagline']);
        $this->store_address = (string) Setting::get('store_address', Setting::DEFAULTS['store_address']);
        $this->store_phone = (string) Setting::get('store_phone', Setting::DEFAULTS['store_phone']);
        $this->store_email = (string) Setting::get('store_email', Setting::DEFAULTS['store_email']);
        $this->existing_store_logo = Setting::get('store_logo');
        $this->receipt_footer = (string) Setting::get('receipt_footer', Setting::DEFAULTS['receipt_footer']);

        // Load Tax & Service Charge
        $this->tax_enabled = (bool) ((int) Setting::get('tax_enabled', '1'));
        $this->tax_name = (string) Setting::get('tax_name', 'PPN');
        $this->tax_percentage = (float) Setting::get('tax_percentage', 11);
        $this->service_charge_enabled = (bool) ((int) Setting::get('service_charge_enabled', '0'));
        $this->service_charge_percentage = (float) Setting::get('service_charge_percentage', 5);

        // Load Payment & QRIS
        $this->bank_name = (string) Setting::get('bank_name', 'BCA');
        $this->bank_account_number = (string) Setting::get('bank_account_number', '1234567890');
        $this->bank_account_holder = (string) Setting::get('bank_account_holder', 'BrewPOS Indonesia');
        $this->existing_qris_image = Setting::get('qris_image');
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->successMessage = '';
    }

    public function saveStoreSettings(): void
    {
        $this->validate([
            'store_name' => 'required|string|max:100',
            'store_tagline' => 'nullable|string|max:150',
            'store_address' => 'required|string|max:255',
            'store_phone' => 'required|string|max:30',
            'store_email' => 'nullable|email|max:100',
            'store_logo' => 'nullable|image|max:2048',
            'receipt_footer' => 'nullable|string|max:255',
        ], [
            'store_name.required' => 'Nama cafe / toko wajib diisi.',
            'store_address.required' => 'Alamat lengkap cafe wajib diisi.',
            'store_phone.required' => 'Nomor telepon / kontak wajib diisi.',
            'store_logo.image' => 'File logo harus berupa gambar (JPG, PNG, WebP).',
            'store_logo.max' => 'Ukuran file logo maksimal 2MB.',
        ]);

        if ($this->store_logo) {
            if ($this->existing_store_logo && Storage::disk('public')->exists($this->existing_store_logo)) {
                Storage::disk('public')->delete($this->existing_store_logo);
            }
            $this->existing_store_logo = $this->store_logo->store('settings', 'public');
            Setting::set('store_logo', $this->existing_store_logo, 'store');
            $this->store_logo = null;
        }

        Setting::set('store_name', $this->store_name, 'store');
        Setting::set('store_tagline', $this->store_tagline, 'store');
        Setting::set('store_address', $this->store_address, 'store');
        Setting::set('store_phone', $this->store_phone, 'store');
        Setting::set('store_email', $this->store_email, 'store');
        Setting::set('receipt_footer', $this->receipt_footer, 'store');

        $this->successMessage = 'Profil toko dan pengaturan struk berhasil disimpan!';
    }

    public function removeLogo(): void
    {
        if ($this->existing_store_logo && Storage::disk('public')->exists($this->existing_store_logo)) {
            Storage::disk('public')->delete($this->existing_store_logo);
        }

        $this->existing_store_logo = null;
        $this->store_logo = null;
        Setting::set('store_logo', null, 'store');

        $this->successMessage = 'Logo toko berhasil dihapus.';
    }

    public function saveTaxSettings(): void
    {
        $this->validate([
            'tax_enabled' => 'boolean',
            'tax_name' => 'required|string|max:50',
            'tax_percentage' => 'required|numeric|min:0|max:100',
            'service_charge_enabled' => 'boolean',
            'service_charge_percentage' => 'required|numeric|min:0|max:100',
        ], [
            'tax_name.required' => 'Nama label pajak wajib diisi (misal: PPN atau PB1).',
            'tax_percentage.required' => 'Persentase pajak wajib diisi.',
            'tax_percentage.min' => 'Persentase pajak minimal 0%.',
            'tax_percentage.max' => 'Persentase pajak maksimal 100%.',
            'service_charge_percentage.min' => 'Biaya layanan minimal 0%.',
            'service_charge_percentage.max' => 'Biaya layanan maksimal 100%.',
        ]);

        Setting::set('tax_enabled', $this->tax_enabled ? '1' : '0', 'tax');
        Setting::set('tax_name', $this->tax_name, 'tax');
        Setting::set('tax_percentage', (string) $this->tax_percentage, 'tax');
        Setting::set('service_charge_enabled', $this->service_charge_enabled ? '1' : '0', 'tax');
        Setting::set('service_charge_percentage', (string) $this->service_charge_percentage, 'tax');

        $this->successMessage = 'Pengaturan pajak dan biaya layanan berhasil diperbarui!';
    }

    public function savePaymentSettings(): void
    {
        $this->validate([
            'bank_name' => 'required|string|max:50',
            'bank_account_number' => 'required|string|max:50',
            'bank_account_holder' => 'required|string|max:100',
            'qris_image' => 'nullable|image|max:2048',
        ], [
            'bank_name.required' => 'Nama bank wajib diisi.',
            'bank_account_number.required' => 'Nomor rekening wajib diisi.',
            'bank_account_holder.required' => 'Atas nama rekening wajib diisi.',
            'qris_image.image' => 'File QRIS harus berupa gambar (JPG, PNG, WebP).',
            'qris_image.max' => 'Ukuran file gambar QRIS maksimal 2MB.',
        ]);

        if ($this->qris_image) {
            if ($this->existing_qris_image && Storage::disk('public')->exists($this->existing_qris_image)) {
                Storage::disk('public')->delete($this->existing_qris_image);
            }
            $this->existing_qris_image = $this->qris_image->store('settings', 'public');
            Setting::set('qris_image', $this->existing_qris_image, 'payment');
            $this->qris_image = null;
        }

        Setting::set('bank_name', $this->bank_name, 'payment');
        Setting::set('bank_account_number', $this->bank_account_number, 'payment');
        Setting::set('bank_account_holder', $this->bank_account_holder, 'payment');

        $this->successMessage = 'Info rekening dan gambar QRIS toko berhasil disimpan!';
    }

    public function removeQris(): void
    {
        if ($this->existing_qris_image && Storage::disk('public')->exists($this->existing_qris_image)) {
            Storage::disk('public')->delete($this->existing_qris_image);
        }

        $this->existing_qris_image = null;
        $this->qris_image = null;
        Setting::set('qris_image', null, 'payment');

        $this->successMessage = 'Gambar QRIS toko berhasil dihapus.';
    }

    public function render()
    {
        return view('livewire.setting.setting-index');
    }
}
