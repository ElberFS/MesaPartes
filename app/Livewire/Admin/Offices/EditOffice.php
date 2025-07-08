<?php

namespace App\Livewire\Admin\Offices;

use App\Models\Office; // Importa el modelo Office
use Livewire\Component;
use App\Http\Requests\UpdateOfficeRequest; // Importa el Form Request para la actualización

class EditOffice extends Component
{
    public Office $office; // Propiedad para inyectar el modelo Office
    public $name = ''; // Propiedad para el nombre de la oficina en el formulario

    /**
     * Método de montaje del componente. Se ejecuta una vez al inicio.
     *
     * @param Office $office El modelo de oficina inyectado por Livewire.
     */
    public function mount(Office $office)
    {
        $this->office = $office; // Asigna el modelo inyectado a la propiedad
        $this->name = $office->name; // Inicializa el campo del formulario con el nombre actual de la oficina
    }

    /**
     * Define las reglas de validación para Livewire.
     * Estas reglas serán usadas por $this->validate()
     */
    protected function rules()
    {
        // Aquí obtienes las reglas de tu UpdateOfficeRequest,
        // pasando el ID de la oficina actual para que la regla 'unique' lo ignore.
        return (new UpdateOfficeRequest())->rules($this->office->id);
    }

    // ELIMINADO: protected function messages()
    // Los mensajes de validación ahora se definen exclusivamente en UpdateOfficeRequest.php

    /**
     * Actualiza la oficina en la base de datos.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update()
    {
        // Valida los datos. Livewire usará las reglas definidas en rules()
        // y los mensajes definidos en el UpdateOfficeRequest.
        $this->validate();

        // Actualiza el nombre de la oficina usando el valor validado de $this->name.
        $this->office->update([
            'name' => $this->name,
        ]);

        // Redirige al usuario a la lista de oficinas después de la actualización.
        return $this->redirect(route('offices.index'), navigate: true);
    }

    /**
     * Renderiza la vista Blade asociada a este componente.
     */
    public function render()
    {
        return view('livewire.admin.offices.edit-office');
    }
}
