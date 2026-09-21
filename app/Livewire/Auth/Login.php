<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.guest')]
#[Title('Login - BrewPOS')]
class Login extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    public bool $showPassword = false;

    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ];
    }

    public function toggleShowPassword(): void
    {
        $this->showPassword = ! $this->showPassword;
    }

    public function fillDemoCredentials(string $role): void
    {
        if ($role === 'admin') {
            $this->email = 'admin@pos.com';
            $this->password = 'password';
        } elseif ($role === 'cashier') {
            $this->email = 'kasir@pos.com';
            $this->password = 'password';
        } elseif ($role === 'kitchen') {
            $this->email = 'kitchen@pos.com';
            $this->password = 'password';
        }
    }

    public function login()
    {
        $this->validate();

        $throttleKey = Str::transliterate(Str::lower($this->email).'|'.request()->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('email', "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.");

            return;
        }

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($throttleKey);
            $this->addError('email', 'Email atau password yang Anda masukkan salah.');

            return;
        }

        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();
            $this->addError('email', 'Akun Anda berstatus nonaktif. Silakan hubungi admin.');

            return;
        }

        RateLimiter::clear($throttleKey);
        session()->regenerate();

        if ($user->role === 'kitchen') {
            return $this->redirectRoute('kitchen');
        }

        return $this->redirectRoute('pos');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
