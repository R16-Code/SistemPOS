<div class="p-4 sm:p-6 space-y-6 overflow-y-auto h-full bg-slate-50">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Pengaturan Sistem</h2>
            <p class="text-xs text-slate-500 mt-1">Konfigurasi profil cafe, format struk, tarif pajak & service, serta integrasi pembayaran QRIS</p>
        </div>

        @if($successMessage)
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 4000)" 
                 class="px-4 py-2.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2 shadow-xs animate-in fade-in slide-in-from-top-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ $successMessage }}</span>
            </div>
        @endif
    </div>

    <!-- Navigation Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-200 overflow-x-auto pb-px">
        <button type="button" 
                wire:click="setTab('store')"
                class="px-4 py-2.5 text-xs font-bold rounded-t-xl transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer {{ $activeTab === 'store' ? 'bg-white border-t border-x border-slate-200 text-amber-800 shadow-2xs' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-100/60' }}">
            <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0V8a2 2 0 012-2h2a2 2 0 012 2v3m-6 0h6"/></svg>
            <span>Profil Toko & Struk</span>
        </button>

        <button type="button" 
                wire:click="setTab('tax')"
                class="px-4 py-2.5 text-xs font-bold rounded-t-xl transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer {{ $activeTab === 'tax' ? 'bg-white border-t border-x border-slate-200 text-amber-800 shadow-2xs' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-100/60' }}">
            <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"/></svg>
            <span>Pajak & Biaya Layanan</span>
        </button>

        <button type="button" 
                wire:click="setTab('payment')"
                class="px-4 py-2.5 text-xs font-bold rounded-t-xl transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer {{ $activeTab === 'payment' ? 'bg-white border-t border-x border-slate-200 text-amber-800 shadow-2xs' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-100/60' }}">
            <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            <span>Rekening Bank & QRIS</span>
        </button>
    </div>

    <!-- TAB 1: PROFIL TOKO & STRUK -->
    @if($activeTab === 'store')
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Form Input (7 Cols) -->
            <div class="lg:col-span-7 bg-white border border-slate-200/80 rounded-2xl p-5 sm:p-6 shadow-sm space-y-5">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-extrabold text-slate-900">Identitas Usaha & Cetak Nota</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Informasi ini akan ditampilkan pada kop nota, struk kasir, dan laporan penjualan</p>
                </div>

                <form wire:submit.prevent="saveStoreSettings" class="space-y-4 text-xs">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Nama Cafe / Bisnis <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model.live="store_name" placeholder="Contoh: BrewPOS Coffee" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 font-bold text-slate-900 focus:outline-none focus:border-amber-700">
                            @error('store_name') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Slogan / Tagline</label>
                            <input type="text" wire:model.live="store_tagline" placeholder="Contoh: Artisan Coffee & Roastery" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-slate-900 focus:outline-none focus:border-amber-700">
                            @error('store_tagline') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Alamat Lengkap Cafe <span class="text-rose-500">*</span></label>
                        <textarea wire:model.live="store_address" rows="2" placeholder="Jl. Mawar No. 123, Jakarta" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-slate-900 focus:outline-none focus:border-amber-700 leading-relaxed"></textarea>
                        @error('store_address') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Nomor Telepon / WhatsApp <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model.live="store_phone" placeholder="0812-3456-7890" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 font-medium text-slate-900 focus:outline-none focus:border-amber-700">
                            @error('store_phone') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Email Resmi (Opsional)</label>
                            <input type="email" wire:model.live="store_email" placeholder="kontak@brewpos.com" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-slate-900 focus:outline-none focus:border-amber-700">
                            @error('store_email') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Upload Logo Cafe -->
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Logo Cafe</label>
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 p-3.5 bg-slate-50 border border-slate-200 rounded-2xl">
                            <!-- Logo Preview Box -->
                            <div class="w-20 h-20 rounded-2xl bg-white border border-slate-200/90 shadow-2xs overflow-hidden shrink-0 flex items-center justify-center relative aspect-square">
                                @if($store_logo)
                                    <img src="{{ $store_logo->temporaryUrl() }}" alt="Preview Logo" class="w-full h-full object-contain p-1.5">
                                @elseif($existing_store_logo)
                                    <img src="{{ asset('storage/' . $existing_store_logo) }}" alt="Logo Cafe" class="w-full h-full object-contain p-1.5">
                                @else
                                    <div class="text-center p-2 text-slate-400">
                                        <svg class="w-7 h-7 mx-auto text-amber-700/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <span class="text-[9px] font-bold text-slate-400 mt-0.5 block">Logo</span>
                                    </div>
                                @endif

                                <div wire:loading wire:target="store_logo" class="absolute inset-0 bg-slate-900/60 flex flex-col items-center justify-center text-white">
                                    <svg class="animate-spin w-4 h-4 text-white mb-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span class="text-[8px] font-bold">Upload</span>
                                </div>
                            </div>

                            <div class="flex-1 space-y-1.5 w-full">
                                <div class="flex items-center gap-2">
                                    <label class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white border border-slate-300 hover:border-amber-700 hover:bg-amber-50/50 text-slate-700 text-xs font-bold transition-all shadow-2xs">
                                        <svg class="w-3.5 h-3.5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                        <span>{{ $store_logo || $existing_store_logo ? 'Ganti Logo' : 'Upload Logo' }}</span>
                                        <input type="file" wire:model="store_logo" accept="image/png,image/jpeg,image/jpg,image/webp" class="hidden">
                                    </label>
                                    @if($existing_store_logo && !$store_logo)
                                        <button type="button" wire:click="removeLogo" class="px-2.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold transition-colors">
                                            Hapus Logo
                                        </button>
                                    @endif
                                </div>
                                <p class="text-[11px] text-slate-500 leading-tight">
                                    Format: PNG (transparan disarankan), JPG, WebP. Maksimal 2MB.
                                </p>
                                @error('store_logo') <span class="text-rose-600 text-[11px] font-bold block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Catatan Kaki Struk (Receipt Footer)</label>
                        <input type="text" wire:model.live="receipt_footer" placeholder="Terima kasih atas kunjungan Anda! Follow IG: @brewpos.id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-slate-900 focus:outline-none focus:border-amber-700">
                        @error('receipt_footer') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex justify-end">
                        <button type="submit" class="min-h-10.5 px-6 py-2.5 bg-amber-700 hover:bg-amber-800 text-white font-bold text-xs rounded-xl shadow-md shadow-amber-700/20 active:scale-98 transition-all flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Profil Toko</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Live Receipt Preview (5 Cols) -->
            <div class="lg:col-span-5 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-extrabold text-slate-700 uppercase tracking-wider">Preview Tampilan Struk Thermal</span>
                    <span class="text-[10px] font-bold text-amber-800 bg-amber-100 px-2 py-0.5 rounded-full">Kertas 58mm / 80mm</span>
                </div>

                <!-- Realistic Thermal Receipt Card -->
                <div class="bg-white border border-slate-300 rounded-2xl p-5 shadow-md font-mono text-xs text-slate-800 space-y-3 relative overflow-hidden">
                    <div class="absolute top-0 inset-x-0 h-1 bg-repeating-linear-to-r from-slate-200 to-slate-300"></div>

                    <!-- Header Struk -->
                    <div class="text-center space-y-0.5 pt-1">
                        @if($existing_store_logo)
                            <img src="{{ asset('storage/' . $existing_store_logo) }}" alt="Logo" class="w-12 h-12 mx-auto object-contain mb-1">
                        @endif
                        <h4 class="font-extrabold text-sm text-black uppercase tracking-wider">{{ $store_name ?: 'BREWPOS COFFEE' }}</h4>
                        @if($store_tagline)
                            <p class="text-[10px] text-slate-500 font-sans italic">{{ $store_tagline }}</p>
                        @endif
                        <p class="text-[10px] text-slate-600 leading-tight">{{ $store_address ?: 'Jl. Boulevard Raya No. 88, Jakarta Selatan' }}</p>
                        <p class="text-[10px] text-slate-600">Telp: {{ $store_phone ?: '0812-3456-7890' }}</p>
                    </div>

                    <div class="border-b border-dashed border-slate-300"></div>

                    <!-- Info Transaksi Dummy -->
                    <div class="text-[10px] space-y-0.5 text-slate-600">
                        <div class="flex justify-between">
                            <span>No: <strong class="text-slate-900">INV-20260924-001</strong></span>
                            <span>24/09/2026 14:30</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Kasir: Admin Cafe</span>
                            <span>Meja: 04 (Dine-In)</span>
                        </div>
                    </div>

                    <div class="border-b border-dashed border-slate-300"></div>

                    <!-- Items Dummy -->
                    <div class="text-[11px] space-y-1">
                        <div class="flex justify-between">
                            <span>Kopi Susu Aren x2</span>
                            <span>Rp 44.000</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Croissant Butter x1</span>
                            <span>Rp 28.000</span>
                        </div>
                    </div>

                    <div class="border-b border-dashed border-slate-300"></div>

                    <!-- Totals -->
                    <div class="text-[10px] space-y-1">
                        <div class="flex justify-between">
                            <span>Subtotal:</span>
                            <span>Rp 72.000</span>
                        </div>
                        @if($tax_enabled)
                            <div class="flex justify-between text-slate-700">
                                <span>{{ $tax_name }} ({{ $tax_percentage }}%):</span>
                                <span>Rp {{ number_format(72000 * ($tax_percentage / 100), 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if($service_charge_enabled)
                            <div class="flex justify-between text-slate-700">
                                <span>Service ({{ $service_charge_percentage }}%):</span>
                                <span>Rp {{ number_format(72000 * ($service_charge_percentage / 100), 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @php
                            $dummyTax = $tax_enabled ? 72000 * ($tax_percentage / 100) : 0;
                            $dummyService = $service_charge_enabled ? 72000 * ($service_charge_percentage / 100) : 0;
                            $dummyTotal = 72000 + $dummyTax + $dummyService;
                        @endphp
                        <div class="flex justify-between font-extrabold text-xs text-black border-t border-slate-300 pt-1">
                            <span>TOTAL:</span>
                            <span>Rp {{ number_format($dummyTotal, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="border-b border-dashed border-slate-300"></div>

                    <!-- Footer Struk -->
                    <div class="text-center text-[10px] text-slate-500 font-sans space-y-0.5">
                        <p class="font-medium text-slate-700">{{ $receipt_footer ?: 'Terima kasih atas kunjungan Anda!' }}</p>
                        <p class="text-[9px] text-slate-400">BrewPOS System</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- TAB 2: PAJAK & BIAYA LAYANAN -->
    @if($activeTab === 'tax')
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Form Input (7 Cols) -->
            <div class="lg:col-span-7 bg-white border border-slate-200/80 rounded-2xl p-5 sm:p-6 shadow-sm space-y-5">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-extrabold text-slate-900">Pengaturan Pajak (PPN/PB1) & Service Charge</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Atur pembebanan pajak resto dan biaya layanan pada setiap transaksi kasir dan pesanan mandiri</p>
                </div>

                <form wire:submit.prevent="saveTaxSettings" class="space-y-5 text-xs">
                    <!-- Section Pajak (PB1/PPN) -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-bold text-xs text-slate-900">Pajak Penjualan (PPN / PB1 Restoran)</h4>
                                <p class="text-[11px] text-slate-500">Otomatis ditambahkan pada subtotal pesanan di kasir</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model.live="tax_enabled" class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-700"></div>
                            </label>
                        </div>

                        @if($tax_enabled)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-200/80 animate-in fade-in">
                                <div>
                                    <label class="font-bold text-slate-700 block mb-1">Label / Nama Pajak</label>
                                    <input type="text" wire:model.live="tax_name" placeholder="Misal: PPN atau PB1" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 font-bold text-slate-900 focus:outline-none focus:border-amber-700">
                                    @error('tax_name') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="font-bold text-slate-700 block mb-1">Persentase Pajak (%)</label>
                                    <div class="relative">
                                        <input type="number" step="0.1" min="0" max="100" wire:model.live="tax_percentage" placeholder="11" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 font-bold text-slate-900 focus:outline-none focus:border-amber-700 pr-8">
                                        <span class="absolute right-3 top-2 font-bold text-slate-400">%</span>
                                    </div>
                                    @error('tax_percentage') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Section Service Charge -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-bold text-xs text-slate-900">Biaya Layanan (Service Charge)</h4>
                                <p class="text-[11px] text-slate-500">Biaya operasional / jasa pelayan dine-in</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model.live="service_charge_enabled" class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-700"></div>
                            </label>
                        </div>

                        @if($service_charge_enabled)
                            <div class="pt-2 border-t border-slate-200/80 animate-in fade-in">
                                <label class="font-bold text-slate-700 block mb-1">Persentase Service Charge (%)</label>
                                <div class="relative max-w-xs">
                                    <input type="number" step="0.1" min="0" max="100" wire:model.live="service_charge_percentage" placeholder="5" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 font-bold text-slate-900 focus:outline-none focus:border-amber-700 pr-8">
                                    <span class="absolute right-3 top-2 font-bold text-slate-400">%</span>
                                </div>
                                @error('service_charge_percentage') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                            </div>
                        @endif
                    </div>

                    <div class="pt-2 border-t border-slate-100 flex justify-end">
                        <button type="submit" class="min-h-10.5 px-6 py-2.5 bg-amber-700 hover:bg-amber-800 text-white font-bold text-xs rounded-xl shadow-md shadow-amber-700/20 active:scale-98 transition-all flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Pajak & Service</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Simulation Box (5 Cols) -->
            <div class="lg:col-span-5 space-y-3">
                <span class="text-xs font-extrabold text-slate-700 uppercase tracking-wider block">Simulasi Kalkulasi Transaksi Kasir</span>

                <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm space-y-4">
                    <div class="p-3 bg-amber-50/60 border border-amber-200/80 rounded-xl">
                        <p class="text-xs font-bold text-amber-950">Contoh Pembelian Menu:</p>
                        <p class="text-[11px] text-slate-600 mt-0.5">2x Kopi Susu (Rp 40.000) + 1x Toast (Rp 25.000)</p>
                    </div>

                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between text-slate-600">
                            <span>Subtotal Menu:</span>
                            <span class="font-bold text-slate-900">Rp 65.000</span>
                        </div>

                        <div class="flex justify-between items-center text-slate-600">
                            <span>{{ $tax_name }} ({{ $tax_enabled ? $tax_percentage : 0 }}%):</span>
                            @php
                                $simTax = $tax_enabled ? 65000 * ($tax_percentage / 100) : 0;
                            @endphp
                            <span class="font-bold {{ $tax_enabled ? 'text-slate-900' : 'text-slate-400' }}">
                                {{ $tax_enabled ? 'Rp ' . number_format($simTax, 0, ',', '.') : 'Nonaktif (Rp 0)' }}
                            </span>
                        </div>

                        <div class="flex justify-between items-center text-slate-600">
                            <span>Service Charge ({{ $service_charge_enabled ? $service_charge_percentage : 0 }}%):</span>
                            @php
                                $simService = $service_charge_enabled ? 65000 * ($service_charge_percentage / 100) : 0;
                            @endphp
                            <span class="font-bold {{ $service_charge_enabled ? 'text-slate-900' : 'text-slate-400' }}">
                                {{ $service_charge_enabled ? 'Rp ' . number_format($simService, 0, ',', '.') : 'Nonaktif (Rp 0)' }}
                            </span>
                        </div>

                        <div class="pt-2 border-t border-slate-100 flex justify-between items-center text-sm font-extrabold text-amber-900">
                            <span>Total Tagihan:</span>
                            <span>Rp {{ number_format(65000 + $simTax + $simService, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-[11px] text-slate-500 leading-relaxed">
                        💡 Nilai ini langsung terintegrasi otomatis pada perhitungan kasir POS dan total yang harus dibayar customer.
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- TAB 3: REKENING BANK & QRIS -->
    @if($activeTab === 'payment')
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Form Input (7 Cols) -->
            <div class="lg:col-span-7 bg-white border border-slate-200/80 rounded-2xl p-5 sm:p-6 shadow-sm space-y-5">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-extrabold text-slate-900">Metode Pembayaran Non-Tunai Toko</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola nomor rekening untuk transfer dan barcode QRIS statis cafe untuk scan bayar pelanggan</p>
                </div>

                <form wire:submit.prevent="savePaymentSettings" class="space-y-4 text-xs">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Nama Bank / E-Wallet <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model.live="bank_name" placeholder="BCA / Mandiri / BRI / GoPay" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 font-bold text-slate-900 focus:outline-none focus:border-amber-700">
                            @error('bank_name') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Nomor Rekening / HP <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model.live="bank_account_number" placeholder="1234567890" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 font-mono font-bold text-slate-900 focus:outline-none focus:border-amber-700">
                            @error('bank_account_number') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Atas Nama Rekening (Account Holder) <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model.live="bank_account_holder" placeholder="PT BrewPOS Indonesia" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 font-bold text-slate-900 focus:outline-none focus:border-amber-700">
                        @error('bank_account_holder') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Upload Gambar Barcode QRIS Toko -->
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Gambar Barcode QRIS Toko (Opsional)</label>
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 p-3.5 bg-slate-50 border border-slate-200 rounded-2xl">
                            <!-- QRIS Preview Box (Rasio 1:1) -->
                            <div class="w-28 h-28 rounded-2xl bg-white border border-slate-200/90 shadow-2xs overflow-hidden shrink-0 flex items-center justify-center relative aspect-square">
                                @if($qris_image)
                                    <img src="{{ $qris_image->temporaryUrl() }}" alt="Preview QRIS" class="w-full h-full object-contain p-1.5">
                                @elseif($existing_qris_image)
                                    <img src="{{ asset('storage/' . $existing_qris_image) }}" alt="QRIS Toko" class="w-full h-full object-contain p-1.5">
                                @else
                                    <div class="text-center p-2 text-slate-400">
                                        <svg class="w-8 h-8 mx-auto text-amber-700/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                        <span class="text-[9px] font-bold text-slate-400 mt-1 block">Rasio 1:1</span>
                                    </div>
                                @endif

                                <div wire:loading wire:target="qris_image" class="absolute inset-0 bg-slate-900/60 flex flex-col items-center justify-center text-white">
                                    <svg class="animate-spin w-5 h-5 text-white mb-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span class="text-[8px] font-bold">Upload</span>
                                </div>
                            </div>

                            <div class="flex-1 space-y-2 w-full">
                                <div class="flex items-center gap-2">
                                    <label class="cursor-pointer inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-slate-300 hover:border-amber-700 hover:bg-amber-50/50 text-slate-700 text-xs font-bold transition-all shadow-2xs">
                                        <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                        <span>{{ $qris_image || $existing_qris_image ? 'Ganti Gambar QRIS' : 'Upload QRIS Toko' }}</span>
                                        <input type="file" wire:model="qris_image" accept="image/png,image/jpeg,image/jpg,image/webp" class="hidden">
                                    </label>
                                    @if($existing_qris_image && !$qris_image)
                                        <button type="button" wire:click="removeQris" class="px-3 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold transition-colors">
                                            Hapus QRIS
                                        </button>
                                    @endif
                                </div>

                                <div class="p-2.5 rounded-xl bg-amber-50/80 border border-amber-200/70 text-[11px] text-amber-950 space-y-0.5">
                                    <p class="text-slate-600 leading-relaxed">
                                        • Gambar QRIS disarankan berasio <strong>1:1 (Persegi)</strong> agar mudah di-scan oleh kamera HP pelanggan.<br>
                                        • Format: <strong>JPG, PNG, WebP</strong> (Maks. <strong>2MB</strong>).
                                    </p>
                                </div>
                                @error('qris_image') <span class="text-rose-600 text-[11px] font-bold block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-100 flex justify-end">
                        <button type="submit" class="min-h-10.5 px-6 py-2.5 bg-amber-700 hover:bg-amber-800 text-white font-bold text-xs rounded-xl shadow-md shadow-amber-700/20 active:scale-98 transition-all flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Info Pembayaran</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Preview Tampilan Bayar QRIS (5 Cols) -->
            <div class="lg:col-span-5 space-y-3">
                <span class="text-xs font-extrabold text-slate-700 uppercase tracking-wider block">Preview Kartu Pembayaran QRIS Pelanggan</span>

                <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm space-y-4 text-center">
                    <div class="space-y-1">
                        <span class="inline-block px-3 py-1 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-900 border border-amber-200 uppercase tracking-wider">
                            Scan QRIS Pembayaran
                        </span>
                        <h4 class="font-extrabold text-base text-slate-900">{{ $store_name ?: 'BrewPOS Coffee' }}</h4>
                        <p class="text-xs text-slate-500">NMID / Operator: QRIS Nasional</p>
                    </div>

                    <!-- QRIS Container -->
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl inline-block mx-auto max-w-[220px]">
                        @if($existing_qris_image)
                            <img src="{{ asset('storage/' . $existing_qris_image) }}" alt="QRIS" class="w-44 h-44 object-contain mx-auto rounded-xl bg-white p-2 border border-slate-200">
                        @else
                            <div class="w-44 h-44 flex flex-col items-center justify-center bg-white rounded-xl border border-dashed border-slate-300 text-slate-400 p-3">
                                <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                <span class="text-[10px] font-bold mt-1 text-slate-400">Belum ada gambar QRIS</span>
                            </div>
                        @endif
                        <p class="text-[10px] text-slate-500 mt-2 font-medium">Bisa di-scan semua E-Wallet & M-Banking</p>
                    </div>

                    <!-- Info Bank Alternatif -->
                    <div class="pt-3 border-t border-slate-100 text-left text-xs space-y-1 bg-slate-50 p-3 rounded-xl">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Alternatif Transfer Bank</span>
                        <div class="flex justify-between font-bold text-slate-900">
                            <span>{{ $bank_name }}:</span>
                            <span class="font-mono text-amber-900">{{ $bank_account_number }}</span>
                        </div>
                        <p class="text-[11px] text-slate-500">a.n. {{ $bank_account_holder }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
