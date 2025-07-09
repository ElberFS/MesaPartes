<?php

namespace App\Livewire\Other\Responses; // ¡CORREGIDO: 'Responses' en plural!

use App\Models\Document;
use App\Models\Office;
use App\Models\File as DocumentFile;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Http\Requests\StoreResponseRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CreateResponse extends Component
{
    use WithFileUploads;

    public ?Document $document = null; // El documento al que se responde (opcional)

    // Propiedades públicas del formulario
    public $subject = '';
    public $summary = '';
    public $file;
    public $is_final_response = false;
    public $date;

    // Nuevas propiedades para el destino
    public $destination_type = 'internal'; // Por defecto, interno
    public $destination_office_id = null;
    public $destination_organization_name = '';
    public $destination_contact_person = '';
    public $destination_contact_role = '';

    public $offices = [];

    /**
     * Monta el componente, inicializando propiedades.
     * @param Document|null $document El documento al que se va a responder.
     */
    public function mount(?Document $document = null)
    {
        $this->document = $document;
        $this->offices = Office::all();
        $this->date = now()->toDateString(); // Fecha actual por defecto
    }

    /**
     * Resetea la paginación cuando cambia el tipo de destino.
     */
    public function updatedDestinationType()
    {
        // Resetea los campos específicos al cambiar el tipo de destino
        if ($this->destination_type === 'internal') {
            $this->destination_organization_name = '';
            $this->destination_contact_person = '';
            $this->destination_contact_role = '';
        } else { // external
            $this->destination_office_id = null;
        }
    }

    /**
     * Guarda la nueva respuesta en la base de datos.
     */
    public function save()
    {
        $storeResponseRequest = new StoreResponseRequest();

        try {
            $validatedData = $this->validate(
                $storeResponseRequest->rules(),
                $storeResponseRequest->messages()
            );

            $responseFile = null;
            if ($this->file) {
                $filePath = $this->file->store('responses', 'public');
                $responseFile = DocumentFile::create([
                    'original_name' => $this->file->getClientOriginalName(),
                    'path' => $filePath,
                    'file_type' => $this->file->getMimeType(),
                ]);
                $validatedData['file_id'] = $responseFile->id;
            } else {
                $validatedData['file_id'] = null;
            }
            unset($validatedData['file']);

            if ($this->document) {
                $validatedData['document_id'] = $this->document->id;
            } else {
                $validatedData['document_id'] = null;
            }

            \App\Models\Response::create($validatedData);

            $this->reset([
                'subject', 'summary', 'file', 'is_final_response',
                'destination_type', 'destination_office_id', 'destination_organization_name',
                'destination_contact_person', 'destination_contact_role'
            ]);
            $this->date = now()->toDateString();
            $this->destination_type = 'internal';

            session()->flash('status', 'Respuesta registrada exitosamente.');

            if ($this->document) {
                return $this->redirect(route('documents.show', $this->document->id), navigate: true);
            } else {
                return $this->redirect(route('responses.index'), navigate: true);
            }

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            session()->flash('error', 'Ocurrió un error inesperado al registrar la respuesta: ' . $e->getMessage());
        }
    }

    /**
     * Renderiza la vista Blade asociada.
     */
    public function render()
    {
        return view('livewire.other.responses.create-response'); // ¡CORREGIDO: 'responses' en plural!
    }
}
