<?php

namespace App\Livewire\Other\Documents;

use App\Models\Document;
use App\Models\Office;
use App\Models\Priority;
use App\Models\File as DocumentFile;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Http\Requests\UpdateDocumentRequest; // Importa tu FormRequest de actualización
use Illuminate\Support\Facades\Storage; // Para gestionar archivos
use Illuminate\Validation\ValidationException; // Para manejar excepciones de validación

class EditDocument extends Component
{
    use WithFileUploads;

    public Document $document; // Inyección del modelo Document
    public $code = '';
    public $subject = '';
    public $origin_type = '';
    public $origin_office_id = null; // Inicializa a null

    // Nuevos campos para origen externo (organización y encargado)
    public $organization_name = '';
    public $external_contact_person = '';
    public $external_contact_role = '';

    // Nuevos campos para eventos/convenios (aplicables a origen externo)
    public $event_date = null; // Inicializa a null
    public $event_time = null; // Inicializa a null

    public $reference = '';
    public $origin_in_charge = '';
    public $summary = '';
    public $newFile; // Para el nuevo archivo subido
    public $currentFilePath = ''; // Para mostrar la ruta del archivo actual
    public $priority_id = null; // Inicializa a null
    public $registration_date;
    public $status = '';
    public $indication = null; // Nuevo campo para las indicaciones

    public $offices = [];
    public $priorities = [];

    /**
     * Monta el componente, inicializando las propiedades con los datos del documento.
     *
     * @param Document $document El modelo de documento inyectado por Livewire.
     */
    public function mount(Document $document)
    {
        $this->document = $document;
        $this->code = $document->code;
        $this->subject = $document->subject;
        $this->origin_type = $document->origin_type;
        $this->origin_office_id = $document->origin_office_id;

        // Asignar los nuevos campos de origen externo
        $this->organization_name = $document->organization_name;
        $this->external_contact_person = $document->external_contact_person;
        $this->external_contact_role = $document->external_contact_role;

        // Formatear fechas y horas para los inputs HTML
        $this->event_date = $document->event_date ? $document->event_date->toDateString() : null;
        $this->event_time = $document->event_time ? $document->event_time->format('H:i') : null;

        $this->reference = $document->reference;
        $this->origin_in_charge = $document->origin_in_charge;
        $this->summary = $document->summary;
        $this->priority_id = $document->priority_id;
        $this->registration_date = $document->registration_date->toDateString(); // Asegura formato de string para input date
        $this->status = $document->status;
        $this->indication = $document->indication; // Inicializa el nuevo campo 'indication'

        // Si hay un archivo asociado, guarda su ruta para mostrarlo
        if ($document->file) {
            $this->currentFilePath = Storage::url($document->file->path);
        }

        $this->offices = Office::all();
        $this->priorities = Priority::all();
    }

    /**
     * Se ejecuta automáticamente cuando la propiedad 'origin_type' cambia.
     * Limpia los IDs de oficina o campos de origen externo que no corresponden al tipo de origen seleccionado.
     * Esto es crucial para la validación 'required_if'.
     */
    public function updatedOriginType()
    {
        if ($this->origin_type === 'internal') {
            // Si es interno, limpia los campos de origen externo
            $this->organization_name = '';
            $this->external_contact_person = '';
            $this->external_contact_role = '';
            $this->event_date = null;
            $this->event_time = null;
        } else { // 'external'
            // Si es externo, limpia el ID de la oficina de origen
            $this->origin_office_id = null;
        }
    }

    /**
     * Actualiza el documento en la base de datos.
     */
    public function update()
    {
        // 1. Instanciar el Form Request para obtener sus reglas de validación y mensajes personalizados.
        $updateDocumentRequest = new UpdateDocumentRequest();

        try {
            // 2. Realizar la validación de Livewire.
            // Livewire usa las propiedades públicas del componente para validar.
            $validatedData = $this->validate(
                $updateDocumentRequest->rules($this->document->id), // Pasa el ID del documento para la regla unique
                $updateDocumentRequest->messages()
            );

            // --- PUNTO DE DEPURACIÓN (Si la validación pasa, verás los datos aquí) ---
            // dd($validatedData);

            // 3. Manejo de la subida del nuevo archivo
            if (isset($validatedData['newFile'])) {
                // Elimina el archivo antiguo si existe
                if ($this->document->file) {
                    Storage::disk('public')->delete($this->document->file->path);
                    $this->document->file->delete(); // Elimina el registro del archivo de la DB
                }

                // Guarda el nuevo archivo
                $filePath = $validatedData['newFile']->store('documents', 'public');
                $documentFile = DocumentFile::create([
                    'original_name' => $validatedData['newFile']->getClientOriginalName(),
                    'path' => $filePath,
                    'file_type' => $validatedData['newFile']->getMimeType(),
                ]);
                $validatedData['file_id'] = $documentFile->id;
            } else {
                // Si no se sube un nuevo archivo, mantiene el ID del archivo actual
                $validatedData['file_id'] = $this->document->file_id;
            }

            // 4. Elimina el campo 'newFile' del array validado antes de actualizar el documento
            unset($validatedData['newFile']);

            // 5. Ajusta los IDs de las relaciones y campos externos basados en el tipo de origen.
            // Esto es redundante con `updatedOriginType` pero añade una capa de seguridad.
            if ($validatedData['origin_type'] === 'internal') {
                $validatedData['organization_name'] = null;
                $validatedData['external_contact_person'] = null;
                $validatedData['external_contact_role'] = null;
                $validatedData['event_date'] = null;
                $validatedData['event_time'] = null;
            } else { // 'external'
                $validatedData['origin_office_id'] = null;
            }

            // 6. Actualiza el documento
            $this->document->update($validatedData);

            // 7. Resetea la propiedad del nuevo archivo después de la actualización
            $this->reset('newFile'); // Solo resetea 'newFile' si es lo único que se necesita

            // 8. Muestra mensaje de éxito
            session()->flash('status', 'Documento actualizado exitosamente.');

            // 9. Redirige al índice de documentos
            return $this->redirect(route('documents.index'), navigate: true);

        } catch (ValidationException $e) {
            // Livewire maneja la visualización de errores. Puedes depurar aquí si es necesario.
            // dd($e->errors());
            throw $e; // Re-lanza la excepción para que Livewire la capture.
        } catch (\Exception $e) {
            session()->flash('error', 'Ocurrió un error inesperado al actualizar el documento: ' . $e->getMessage());
            // dd($e); // Para depuración
        }
    }

    /**
     * Renderiza la vista Blade asociada.
     */
    public function render()
    {
        // Opciones para el campo 'indication'
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

        return view('livewire.other.documents.edit-document', [
            'indicationOptions' => $indicationOptions,
        ]);
    }
}
