<?php

namespace App\Livewire\Other\Responses;

use App\Models\Document;
use App\Models\Response;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; // Importar para eliminar archivos

class ListResponses extends Component
{
    use WithPagination;

    public ?Document $document = null;

    public $search = '';
    public $perPage = 10;

    // Propiedades para el modal de confirmación de eliminación
    public $confirmingResponseDeletion = false;
    public $responseToDeleteId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    /**
     * Monta el componente.
     * Ahora recibe documentId como un parámetro de ruta opcional.
     */
    public function mount(?int $documentId = null)
    {
        Log::info('ListResponses mount method called. documentId received: ' . ($documentId ?? 'null'));

        if ($documentId) {
            $this->document = Document::find($documentId);
            // Si el documento no se encuentra, redirige a la lista de documentos
            if (!$this->document) {
                Log::warning('Document with ID ' . $documentId . ' not found. Redirecting to documents index.');
                session()->flash('error', 'El documento especificado no fue encontrado.');
                return $this->redirect(route('documents.index'), navigate: true);
            }
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    /**
     * Muestra el modal de confirmación para eliminar una respuesta.
     *
     * @param int $responseId El ID de la respuesta a eliminar.
     */
    public function confirmResponseDeletion($responseId)
    {
        $this->confirmingResponseDeletion = true;
        $this->responseToDeleteId = $responseId;
    }

    /**
     * Elimina la respuesta confirmada.
     */
    public function deleteResponse()
    {
        if ($this->responseToDeleteId) {
            $response = Response::find($this->responseToDeleteId);
            if ($response) {
                try {
                    // Eliminar el archivo asociado si existe
                    if ($response->file) {
                        Storage::disk('public')->delete($response->file->path); // Usar la fachada Storage
                        $response->file->delete(); // Elimina el registro del archivo de la DB
                    }
                    $response->delete();
                    session()->flash('status', 'Respuesta eliminada exitosamente.');
                } catch (\Illuminate\Database\QueryException $e) {
                    session()->flash('error', 'No se puede eliminar la respuesta debido a elementos asociados (ej. seguimientos).');
                } catch (\Exception $e) {
                    session()->flash('error', 'Ocurrió un error inesperado al eliminar la respuesta: ' . $e->getMessage());
                }
            }
        }

        $this->confirmingResponseDeletion = false;
        $this->responseToDeleteId = null;

        // Refresca la lista de respuestas
        $this->resetPage(); // Opcional: para asegurar que la paginación se reinicia si se elimina de la primera página
    }

    public function render()
    {
        $responsesQuery = Response::query()
                            ->with(['document', 'file', 'destinationOffice']);

        // Filtra por document_id si el documento está cargado
        if ($this->document) {
            $responsesQuery->where('document_id', $this->document->id);
            Log::info('Filtering responses for document ID: ' . $this->document->id);
        } else {
            Log::info('No document associated, showing all responses (or none if no document is expected).');
        }


        $responses = $responsesQuery
                            ->when($this->search, function ($query) {
                                $query->where('subject', 'like', '%' . $this->search . '%')
                                      ->orWhere('summary', 'like', '%' . $this->search . '%');
                            })
                            ->orderBy('date', 'desc')
                            ->paginate($this->perPage);

        Log::info('Respuestas obtenidas para documentId: ' . ($this->document ? $this->document->id : 'N/A'), [
            'count' => $responses->total(),
            'data' => $responses->items(),
            'document_code' => $this->document ? $this->document->code : 'No Documento Asociado'
        ]);

        return view('livewire.other.responses.list-responses', [
            'responses' => $responses,
        ]);
    }
}
