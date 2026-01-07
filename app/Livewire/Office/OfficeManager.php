<?php

namespace App\Livewire\Office;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Office;
use App\Services\OfficeService;
use App\Http\Requests\OfficeRequest;


class OfficeManager extends Component
{
    use WithPagination;

    public $search = '';
    public $officeId = null;

    public $name;
    public $acronym;
    public $is_active = true;

    public $isEditing = false;
    public bool $showModal = false;

    public function render(OfficeService $service)
    {
        return view('livewire.office.office-manager', [
            'offices' => $service->getAll(10, $this->search)
        ]);
    }

    public function create()
    {
        $this->reset(['name', 'acronym', 'officeId', 'isEditing']);
        $this->is_active = true;
        $this->resetValidation();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $office = Office::findOrFail($id);

        $this->isEditing = true;
        $this->officeId = $office->id;
        $this->name = $office->name;
        $this->acronym = $office->acronym;
        $this->is_active = (bool) $office->is_active;

        $this->resetValidation();
        $this->showModal = true;
    }

    public function save(OfficeService $service)
    {
        $request = new OfficeRequest();

        if ($this->officeId) {
            $request->office = $this->officeId;
        }

        $validated = $this->validate($request->rules());

        if ($this->isEditing) {
            $office = Office::findOrFail($this->officeId);
            $service->update($office, $validated);
        } else {
            $service->create($validated);
        }

        $this->reset(['name', 'acronym', 'officeId', 'isEditing']);
        $this->is_active = true;

        $this->resetValidation();
        $this->showModal = false;

        session()->flash('status', 'Operación realizada con éxito.');
    }


    public function delete($id, OfficeService $service)
    {
        $office = Office::findOrFail($id);
        $service->delete($office);
    }

    public function toggleStatus($id, OfficeService $service)
    {
        $office = Office::findOrFail($id);
        $service->toggleStatus($office);
    }
}
