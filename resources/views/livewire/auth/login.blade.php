<div class="w-full max-w-md">
    <div class="bg-white border border-slate-200/90 rounded-3xl p-8 shadow-xl shadow-slate-200/50 space-y-6">
        
        <!-- Logo & Header -->
        <div class="text-center space-y-2">
            <div class="inline-flex w-14 h-14 rounded-2xl bg-amber-700 text-white items-center justify-center shadow-lg shadow-amber-700/25 mb-1">
                <svg class="w-8 h-8 fill-current" viewBox="0 0 24 24">
                    <path d="M2,21V19H20V21H2M20,8V5H18V8H20M20,3A2,2 0 0,1 22,5V8A2,2 0 0,1 20,10H18V13A4,4 0 0,1 14,17H8A4,4 0 0,1 4,13V3H20M16,5H6V13A2,2 0 0,0 8,15H14A2,2 0 0,0 16,13V5Z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">BrewPOS Cafe</h1>
            <p class="text-xs text-slate-500">Silakan login untuk membuka kasir dan sistem POS</p>
        </div>

        <!-- Login Form -->
        <form wire:submit="login" class="space-y-4 text-xs">
            <div>
                <label for="email" class="block font-bold text-slate-700 mb-1.5">Alamat Email</label>
                <div class="relative">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                    </svg>
                    <input type="email" 
                           id="email" 
                           wire:model="email" 
                           placeholder="nama@pos.com" 
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-4 py-2.5 text-xs text-slate-900 font-medium placeholder-slate-400 focus:outline-none focus:border-amber-700 focus:bg-white transition-all @error('email') border-rose-400 @enderror">
                </div>
                @error('email') 
                    <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> 
                @enderror
            </div>

            <div>
                <label for="password" class="block font-bold text-slate-700 mb-1.5">Password</label>
                <div class="relative">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <input type="{{ $showPassword ? 'text' : 'password' }}" 
                           id="password" 
                           wire:model="password" 
                           placeholder="Masukkan password" 
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-10 py-2.5 text-xs text-slate-900 font-medium placeholder-slate-400 focus:outline-none focus:border-amber-700 focus:bg-white transition-all @error('password') border-rose-400 @enderror">
                    <button type="button" 
                            wire:click="toggleShowPassword" 
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                        @if($showPassword)
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                            </svg>
                        @else
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        @endif
                    </button>
                </div>
                @error('password') 
                    <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> 
                @enderror
            </div>

            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" wire:model="remember" class="w-4 h-4 rounded border-slate-300 text-amber-700 focus:ring-amber-700">
                    <span class="text-slate-600 font-medium text-xs">Ingat saya</span>
                </label>
            </div>

            <button type="submit" 
                    wire:loading.attr="disabled"
                    wire:target="login" 
                    class="w-full py-3 bg-amber-700 hover:bg-amber-800 disabled:opacity-75 text-white font-bold rounded-xl transition-all shadow-md shadow-amber-700/20 flex items-center justify-center gap-2 text-sm">
                <span wire:loading.remove wire:target="login">Masuk ke Sistem</span>
                <span wire:loading wire:target="login" class="flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Memproses...</span>
                </span>
            </button>
        </form>

        <!-- Quick Demo Accounts -->
        <div class="pt-4 border-t border-slate-100 space-y-2">
            <p class="text-[11px] font-bold text-slate-400 text-center uppercase tracking-wider">Pilih Akun Demo (1 Klik)</p>
            <div class="grid grid-cols-3 gap-2">
                <button type="button" 
                        wire:click="fillDemoCredentials('admin')" 
                        class="p-2 rounded-xl border border-slate-200 bg-slate-50 hover:bg-amber-50 hover:border-amber-200 text-slate-700 hover:text-amber-900 font-bold text-[11px] transition-all text-center">
                    <span class="block">Admin</span>
                    <span class="text-[9px] font-normal text-slate-400 block mt-0.5">Owner</span>
                </button>
                <button type="button" 
                        wire:click="fillDemoCredentials('cashier')" 
                        class="p-2 rounded-xl border border-slate-200 bg-slate-50 hover:bg-amber-50 hover:border-amber-200 text-slate-700 hover:text-amber-900 font-bold text-[11px] transition-all text-center">
                    <span class="block">Kasir</span>
                    <span class="text-[9px] font-normal text-slate-400 block mt-0.5">Shift POS</span>
                </button>
                <button type="button" 
                        wire:click="fillDemoCredentials('kitchen')" 
                        class="p-2 rounded-xl border border-slate-200 bg-slate-50 hover:bg-amber-50 hover:border-amber-200 text-slate-700 hover:text-amber-900 font-bold text-[11px] transition-all text-center">
                    <span class="block">Dapur</span>
                    <span class="text-[9px] font-normal text-slate-400 block mt-0.5">Barista</span>
                </button>
            </div>
        </div>

    </div>
</div>
