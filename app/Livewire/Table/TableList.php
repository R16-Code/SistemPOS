<?php

namespace App\Livewire\Table;

use App\Models\Table;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Manajemen Meja - BrewPOS')]
class TableList extends Component
{
    public function toggleStatus($tableId)
    {
        $table = Table::find($tableId);
        if ($table) {
            $newStatus = $table->status === 'available' ? 'occupied' : 'available';
            $table->update(['status' => $newStatus]);
        }
    }

    public function render()
    {
        $tables = Table::all();

        return view('livewire.table.table-list', [
            'tables' => $tables,
        ]);
    }
}
