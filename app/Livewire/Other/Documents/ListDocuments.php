<?php

namespace App\Livewire\Other\Documents;

use App\Models\Document;
use App\Models\Office;
use App\Models\Priority;
use App\Models\File as DocumentFile;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;

class ListDocuments extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    public $filterOriginType = '';
    public $filterIndication = '';

    // Propiedades para el ordenamiento
    public $sortColumn = 'registration_date';
    public $sortDirection = 'desc';

    public $confirmingDocumentDeletion = false;
    public $documentToDeleteId = null;

    /**
     * Renderiza la vista Blade asociada.
     * Obtiene los documentos aplicando filtros, búsqueda y paginación.
     */
    public function render()
    {
        $documents = Document::query()
            ->with(['originOffice', 'file', 'priority'])
            ->when($this->search, function ($query) {
                $query->where(function ($subQuery) { // Envuelve las condiciones de búsqueda en un grupo
                    $subQuery->where('code', 'like', '%' . $this->search . '%')
                             ->orWhere('subject', 'like', '%' . $this->search . '%')
                             ->orWhere('reference', 'like', '%' . $this->search . '%')
                             // Campos de origen externo
                             ->orWhere('organization_name', 'like', '%' . $this->search . '%')
                             ->orWhere('external_contact_person', 'like', '%' . $this->search . '%')
                             ->orWhere('external_contact_role', 'like', '%' . $this->search . '%')
                             ->orWhere('indication', 'like', '%' . $this->search . '%')
                             // Búsqueda por nombre de oficina de origen (relacionado)
                             ->orWhereHas('originOffice', function ($officeQuery) {
                                 $officeQuery->where('name', 'like', '%' . $this->search . '%');
                             });
                });
            })
            ->when($this->filterOriginType, function ($query) {
                $query->where('origin_type', $this->filterOriginType);
            })
            ->when($this->filterIndication, function ($query) {
                $query->where('indication', $this->filterIndication);
            });

        // Aplicar ordenamiento dinámico
        // Para la columna de origen, necesitamos un manejo especial
        if ($this->sortColumn === 'origin') {
            // Ordenar por tipo de origen y luego por el nombre de la oficina o la organización
            $documents->orderBy('origin_type', $this->sortDirection)
                      ->orderBy(
                          Document::select('name')
                              ->from('offices')
                              ->whereColumn('offices.id', 'documents.origin_office_id'),
                          $this->sortDirection
                      )
                      ->orderBy('organization_name', $this->sortDirection);
        } else {
            $documents->orderBy($this->sortColumn, $this->sortDirection);
        }

        $documents = $documents->paginate($this->perPage);


        $priorities = Priority::all();

        $indicationOptions = [
            'tomar_conocimiento' => 'Tomar Conocimiento',
            'acciones_necesarias' => 'Acciones Necesarias',
            'opinar' => 'Opinar',
            'preparar_respuesta' => 'Preparar Respuesta',
            'informar' => 'Informar',
            'coordinar_accion' => 'Coordinar Acción',
            'difundir' => 'Difundir',
            'preparar_resolucion' => 'Preparar Resolución',
            'remitir_antecedentes' => 'Remitir Antecedentes',
            'archivo_provisional' => 'Archivo Provisional',
            'devolver_oficina_origen' => 'Devolver Oficina de Origen',
            'atender' => 'Atender',
            'acumular_respuestas' => 'Acumular Respuestas',
            'archivo' => 'Archivo',
            'acumular_al_expediente' => 'Acumular al Expediente',
        ];

        $statusLabels = [
            'en_proceso' => 'En Proceso',
            'respondido' => 'Respondido',
            'archivado' => 'Archivado',
        ];

        return view('livewire.other.documents.list-documents', [
            'documents' => $documents,
            'priorities' => $priorities,
            'indicationOptions' => $indicationOptions,
            'statusLabels' => $statusLabels,
        ]);
    }

    /**
     * Alterna la columna y dirección de ordenamiento.
     *
     * @param string $column La columna por la que ordenar.
     */
    public function sortBy($column)
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterOriginType()
    {
        $this->resetPage();
    }

    public function updatingFilterIndication()
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
                        Storage::disk('public')->delete($document->file->path);
                        $document->file->delete();
                    }
                    $document->delete();
                    session()->flash('status', 'Documento eliminado exitosamente.');
                } catch (QueryException $e) {
                    session()->flash('error', 'No se puede eliminar el documento debido a elementos asociados (ej. respuestas, seguimientos).');
                }
            }
        }

        $this->confirmingDocumentDeletion = false;
        $this->documentToDeleteId = null;
    }
}
