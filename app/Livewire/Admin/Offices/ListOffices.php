<?php

namespace App\Livewire\Admin\Offices;

use App\Models\Office; // Importa el modelo Office
use Livewire\Component;
use Livewire\WithPagination; // Trait para paginación

class ListOffices extends Component
{
    use WithPagination; // Habilita la paginación en el componente

    public $confirmingOfficeDeletion = false; // Estado para el modal de confirmación
    public $officeToDeleteId = null; // ID de la oficina a eliminar

    /**
     * Renderiza la vista Blade asociada a este componente.
     * Obtiene todas las oficinas paginadas.
     */
    public function render()
    {
        return view('livewire.admin.offices.list-offices', [
            'offices' => Office::paginate(10), // Pagina 10 oficinas por página
        ]);
    }

    /**
     * Muestra el modal de confirmación para eliminar una oficina.
     *
     * @param int $officeId El ID de la oficina a eliminar.
     */
    public function confirmOfficeDeletion($officeId)
    {
        $this->confirmingOfficeDeletion = true;
        $this->officeToDeleteId = $officeId;
    }

    /**
     * Elimina la oficina confirmada.
     */
    public function deleteOffice()
    {
        if ($this->officeToDeleteId) {
            $office = Office::find($this->officeToDeleteId);
            if ($office) {
                try {
                    $office->delete();
                    session()->flash('status', 'Oficina eliminada exitosamente.');
                } catch (\Illuminate\Database\QueryException $e) {
                    // Manejo de errores si la oficina tiene relaciones (ej. documentos, usuarios)
                    session()->flash('error', 'No se puede eliminar la oficina porque tiene elementos asociados.');
                }
            }
        }

        $this->confirmingOfficeDeletion = false; // Cierra el modal
        $this->officeToDeleteId = null; // Resetea el ID
    }
}
