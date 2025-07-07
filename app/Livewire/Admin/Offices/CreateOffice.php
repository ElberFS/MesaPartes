<?php

namespace App\Livewire\Admin\Offices;

use App\Models\Office;
use Livewire\Component;
use App\Http\Requests\StoreOfficeRequest; // Importa el Form Request

class CreateOffice extends Component
{
    public $name = '';

    public function save()
    {
        // Instancia tu Form Request para obtener sus reglas Y sus mensajes
        $storeOfficeRequest = new StoreOfficeRequest();

        // Pasa las reglas y los mensajes directamente a $this->validate()
        $this->validate(
            $storeOfficeRequest->rules(),
            $storeOfficeRequest->messages()
        );

        Office::create([
            'name' => $this->name,
        ]);

        $this->reset('name');

        return $this->redirect(route('offices.index'));
    }

    public function cancel()
    {
        return $this->redirect(route('offices.index'));
    }

    public function render()
    {
        return view('livewire.admin.offices.create-office');
    }
}
