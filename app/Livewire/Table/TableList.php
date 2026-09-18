<?php

namespace App\Livewire\Table;

use App\Models\Table;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Manajemen Meja - BrewPOS')]
class TableList extends Component
{
    // Modal state
    public $showModal = false;
    public $isEditMode = false;
    public $selectedTableId = null;

    // Form inputs
    public $table_number = '';
    public $capacity = 4;
    public $status = 'available';

    protected function rules()
    {
        return [
            'table_number' => 'required|string|max:50|unique:tables,table_number,' . $this->selectedTableId,
            'capacity' => 'required|integer|min:1|max:50',
            'status' => 'required|in:available,occupied,reserved',
        ];
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->isEditMode = false;

        // Auto suggest next table number
        $nextNum = Table::count() + 1;
        $this->table_number = 'Meja ' . str_pad($nextNum, 2, '0', STR_PAD_LEFT);
        $this->capacity = 4;
        $this->status = 'available';

        $this->showModal = true;
    }

    public function editTable($id)
    {
        $table = Table::findOrFail($id);
        $this->selectedTableId = $table->id;
        $this->table_number = $table->table_number;
        $this->capacity = $table->capacity;
        $this->status = $table->status;

        $this->isEditMode = true;
        $this->showModal = true;
    }

    public function saveTable()
    {
        $this->validate();

        if ($this->isEditMode) {
            $table = Table::findOrFail($this->selectedTableId);
            $table->update([
                'table_number' => $this->table_number,
                'capacity' => $this->capacity,
                'status' => $this->status,
            ]);
        } else {
            Table::create([
                'table_number' => $this->table_number,
                'capacity' => $this->capacity,
                'status' => $this->status,
            ]);
        }

        $this->closeModal();
    }

    public function toggleStatus($tableId)
    {
        $table = Table::find($tableId);
        if ($table) {
            $newStatus = $table->status === 'available' ? 'occupied' : 'available';
            $table->update(['status' => $newStatus]);
        }
    }

    public function deleteTable($id)
    {
        $table = Table::findOrFail($id);
        $table->delete();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->selectedTableId = null;
        $this->table_number = '';
        $this->capacity = 4;
        $this->status = 'available';
        $this->resetValidation();
    }

    public function render()
    {
        $tables = Table::all();

        return view('livewire.table.table-list', [
            'tables' => $tables,
        ]);
    }
}
