<?php

namespace App\Livewire\Other\Documents;

use App\Models\Document; // Modelo Document
use App\Models\Office; // Asegúrate de importar Office si lo usas para filtros o detalles
use App\Models\Priority; // Asegúrate de importar Priority si lo usas para filtros
use App\Models\File as DocumentFile; // Asegúrate de importar File si lo usas para eliminar archivos
use Livewire\Component;
use Livewire\WithPagination; // Trait para paginación
use Illuminate\Support\Facades\Storage; // Para eliminar archivos del almacenamiento

class ListDocuments extends Component
{
    use WithPagination; // Habilita la paginación

    public $search = ''; // Término de búsqueda general
    public $perPage = 10; // Elementos por página
    public $filterStatus = ''; // Filtro por estado
    public $filterPriority = ''; // Filtro por prioridad
    public $filterOriginType = ''; // Filtro por tipo de origen
    public $filterIndication = ''; // Nuevo filtro por indicación (opcional, si se desea añadir un selector)

    public $confirmingDocumentDeletion = false; // Estado para el modal de confirmación
    public $documentToDeleteId = null; // ID del documento a eliminar

    /**
     * Renderiza la vista Blade asociada.
     * Obtiene los documentos aplicando filtros, búsqueda y paginación.
     */
    public function render()
    {
        $documents = Document::query()
            // Carga relaciones necesarias.
            ->with(['originOffice', 'file', 'priority'])
            ->when($this->search, function ($query) {
                $query->where('code', 'like', '%' . $this->search . '%')
                      ->orWhere('subject', 'like', '%' . $this->search . '%')
                      ->orWhere('reference', 'like', '%' . $this->search . '%')
                      // Nuevos campos de búsqueda para origen externo
                      ->orWhere('organization_name', 'like', '%' . $this->search . '%')
                      ->orWhere('external_contact_person', 'like', '%' . $this->search . '%')
                      ->orWhere('external_contact_role', 'like', '%' . $this->search . '%')
                      ->orWhere('indication', 'like', '%' . $this->search . '%'); // Añadido el campo 'indication' a la búsqueda
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->filterPriority, function ($query) {
                $query->where('priority_id', $this->filterPriority);
            })
            ->when($this->filterOriginType, function ($query) {
                $query->where('origin_type', $this->filterOriginType);
            })
            ->when($this->filterIndication, function ($query) { // Nuevo filtro si se decide usar
                $query->where('indication', $this->filterIndication);
            })
            ->orderBy('registration_date', 'desc') // Ordena por fecha de registro
            ->paginate($this->perPage);

        // Se pasan las prioridades para el filtro en la vista
        $priorities = Priority::all(); // Asegúrate de que este modelo esté importado

        // Opciones para el campo 'indication' (si se desea un filtro select)
        $indicationOptions = [
            'tomar_conocimiento',
            'acciones_necesarias',
            'opinar',
            'preparar_respuesta',
            'informar',
            'coordinar_accion',
            'difundir',
            'preparar_resolucion',
            'remitir_antecedentes',
            'archivo_provisional',
            'devolver_oficina_origen',
            'atender',
            'acumular_respuestas',
            'archivo',
            'acumular_al_expediente',
        ];

        return view('livewire.other.documents.list-documents', [
            'documents' => $documents,
            'priorities' => $priorities,
            'indicationOptions' => $indicationOptions, // Pasa las opciones al Blade
        ]);
    }

    /**
     * Reinicia la paginación cuando cambia el término de búsqueda o filtros.
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterPriority()
    {
        $this->resetPage();
    }

    public function updatingFilterOriginType()
    {
        $this->resetPage();
    }

    public function updatingFilterIndication() // Nuevo método para resetear paginación si se usa el filtro de indicación
    {
        $this->resetPage();
    }

    /**
     * Muestra el modal de confirmación para eliminar un documento.
     *
     * @param int $documentId El ID del documento a eliminar.
     */
    public function confirmDocumentDeletion($documentId)
    {
        $this->confirmingDocumentDeletion = true;
        $this->documentToDeleteId = $documentId;
    }

    /**
     * Elimina el documento confirmado.
     */
    public function deleteDocument()
    {
        if ($this->documentToDeleteId) {
            $document = Document::find($this->documentToDeleteId);
            if ($document) {
                try {
                    // Eliminar el archivo asociado si existe
                    if ($document->file) {
                        Storage::disk('public')->delete($document->file->path); // Usar la fachada Storage
                        $document->file->delete(); // Elimina el registro del archivo de la DB
                    }
                    $document->delete();
                    session()->flash('status', 'Documento eliminado exitosamente.');
                } catch (\Illuminate\Database\QueryException $e) {
                    session()->flash('error', 'No se puede eliminar el documento debido a elementos asociados (ej. respuestas, seguimientos).');
                }
            }
        }

        $this->confirmingDocumentDeletion = false;
        $this->documentToDeleteId = null;
    }
}

