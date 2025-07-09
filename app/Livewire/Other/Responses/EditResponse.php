<?php

namespace App\Livewire\Other\Responses; // ¡Namespace correcto: 'Responses' en plural!

use App\Models\Response;
use App\Models\Document;
use App\Models\Office;
use App\Models\File as DocumentFile;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Http\Requests\UpdateResponseRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class EditResponse extends Component
{
    use WithFileUploads;

    public Response $response; // Inyección del modelo Response
    public $subject = '';
    public $summary = '';
    public $newFile; // Para la subida del nuevo archivo
    public $currentFilePath = ''; // Para mostrar la ruta del archivo actual
    public $is_final_response = false;
    public $date;

    // Nuevas propiedades para el destino
    public $destination_type = 'internal';
    public $destination_office_id = null;
    public $destination_organization_name = '';
    public $destination_contact_person = '';
    public $destination_contact_role = '';

    public $offices = [];

    /**
     * Monta el componente, inicializando las propiedades con los datos de la respuesta.
     *
     * @param Response $response El modelo de respuesta inyectado por Livewire.
     */
    public function mount(Response $response)
    {
        $this->response = $response->load(['file', 'destinationOffice']); // Carga relaciones

        $this->subject = $response->subject;
        $this->summary = $response->summary;
        $this->is_final_response = $response->is_final_response;
        $this->date = $response->date->toDateString();

        // Carga los campos de destino
        $this->destination_type = $response->destination_type;
        $this->destination_office_id = $response->destination_office_id;
        $this->destination_organization_name = $response->destination_organization_name;
        $this->destination_contact_person = $response->destination_contact_person;
        $this->destination_contact_role = $response->destination_contact_role;

        // Si hay un archivo asociado, guarda su ruta para mostrarlo
        if ($response->file) {
            $this->currentFilePath = Storage::url($response->file->path);
        }

        $this->offices = Office::all();
    }

    /**
     * Resetea los campos específicos al cambiar el tipo de destino.
     */
    public function updatedDestinationType()
    {
        if ($this->destination_type === 'internal') {
            $this->destination_organization_name = '';
            $this->destination_contact_person = '';
            $this->destination_contact_role = '';
        } else { // external
            $this->destination_office_id = null;
        }
    }

    /**
     * Actualiza la respuesta en la base de datos.
     */
    public function update()
    {
        $updateResponseRequest = new UpdateResponseRequest();

        try {
            $validatedData = $this->validate(
                $updateResponseRequest->rules(),
                $updateResponseRequest->messages()
            );

            // Manejo de la subida del nuevo archivo
            if (isset($validatedData['newFile'])) {
                // Elimina el archivo antiguo si existe
                if ($this->response->file) {
                    Storage::disk('public')->delete($this->response->file->path);
                    $this->response->file->delete(); // Elimina el registro del archivo de la DB
                }

                // Guarda el nuevo archivo
                $filePath = $validatedData['newFile']->store('responses', 'public');
                $documentFile = DocumentFile::create([
                    'original_name' => $validatedData['newFile']->getClientOriginalName(),
                    'path' => $filePath,
                    'file_type' => $validatedData['newFile']->getMimeType(),
                ]);
                $validatedData['file_id'] = $documentFile->id;
            } else {
                // Si no se sube un nuevo archivo, mantiene el ID del archivo actual
                $validatedData['file_id'] = $this->response->file_id;
            }

            // Elimina el campo 'newFile' del array validado antes de actualizar la respuesta
            unset($validatedData['newFile']);

            // Actualiza la respuesta
            $this->response->update($validatedData);

            // Resetea la propiedad del nuevo archivo después de la actualización
            $this->reset('newFile');

            session()->flash('status', 'Respuesta actualizada exitosamente.');

            // Redirige de vuelta a la vista del documento al que pertenece la respuesta
            return $this->redirect(route('documents.show', $this->response->document_id), navigate: true);

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            session()->flash('error', 'Ocurrió un error inesperado al actualizar la respuesta: ' . $e->getMessage());
        }
    }

    /**
     * Renderiza la vista Blade asociada.
     */
    public function render()
    {
        return view('livewire.other.responses.edit-response'); // ¡CORREGIDO: 'responses' en plural!
    }
}
