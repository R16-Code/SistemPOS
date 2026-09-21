<?php

namespace App\Livewire\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Kelola Pengguna & Karyawan - BrewPOS')]
class UserList extends Component
{
    use WithPagination;

    public $search = '';

    public $roleFilter = '';

    // Modal state
    public $showFormModal = false;

    public $isEditMode = false;

    public $selectedUserId = null;

    // Form inputs
    public $name = '';

    public $email = '';

    public $password = '';

    public $role = 'cashier';

    public $is_active = true;

    protected function rules()
    {
        $passwordRule = $this->isEditMode ? 'nullable|string|min:6' : 'required|string|min:6';

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$this->selectedUserId,
            'password' => $passwordRule,
            'role' => 'required|in:admin,cashier,kitchen',
            'is_active' => 'boolean',
        ];
    }

    protected function messages()
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan oleh akun lain.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'role.required' => 'Peran (role) wajib dipilih.',
        ];
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->showFormModal = true;
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        $this->selectedUserId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->role = $user->role;
        $this->is_active = (bool) $user->is_active;

        $this->isEditMode = true;
        $this->showFormModal = true;
    }

    public function saveUser()
    {
        $this->validate();

        if ($this->isEditMode) {
            $user = User::findOrFail($this->selectedUserId);
            $data = [
                'name' => $this->name,
                'email' => $this->email,
                'role' => $this->role,
                'is_active' => $this->is_active,
            ];

            if (! empty($this->password)) {
                $data['password'] = Hash::make($this->password);
            }

            $user->update($data);
        } else {
            User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'role' => $this->role,
                'is_active' => $this->is_active,
            ]);
        }

        $this->closeModal();
    }

    public function toggleActive($id)
    {
        if ($id === auth()->id()) {
            return;
        }

        $user = User::findOrFail($id);
        $user->update([
            'is_active' => ! $user->is_active,
        ]);
    }

    public function deleteUser($id)
    {
        if ($id === auth()->id()) {
            return;
        }

        $user = User::findOrFail($id);
        // If user has orders, we can deactivate instead of hard delete to preserve invoice relations
        if ($user->orders()->exists()) {
            $user->update(['is_active' => false]);

            return;
        }

        $user->delete();
    }

    public function closeModal()
    {
        $this->showFormModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->selectedUserId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->role = 'cashier';
        $this->is_active = true;
        $this->resetValidation();
    }

    public function render()
    {
        $usersQuery = User::latest();

        if (! empty($this->search)) {
            $usersQuery->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%');
            });
        }

        if (! empty($this->roleFilter)) {
            $usersQuery->where('role', $this->roleFilter);
        }

        return view('livewire.user.user-list', [
            'users' => $usersQuery->paginate(10),
        ]);
    }
}
