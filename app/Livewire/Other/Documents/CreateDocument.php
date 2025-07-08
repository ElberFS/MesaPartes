<?php

namespace App\Livewire\Other\Documents;

use App\Models\Document;
use App\Models\Office;
use App\Models\Priority;
use App\Models\File as DocumentFile;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Http\Requests\StoreDocumentRequest; // Importa tu FormRequest
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException; // Importa esto para manejar excepciones

class CreateDocument extends Component
{
    use WithFileUploads;

    // Propiedades públicas del componente (enlazadas con wire:model en la vista)
    public $code = '';
    public $subject = '';
    public $origin_type = 'internal'; // Valor por defecto

    // Campos para origen interno
    public $origin_office_id = null; // Inicializa a null para relaciones, es más seguro

    // Nuevos campos para origen externo (organización y encargado), con nombres en inglés
    public $organization_name = '';
    public $external_contact_person = '';
    public $external_contact_role = '';

    // Nuevos campos para eventos/convenios (aplicables a origen externo), con nombres en inglés
    public $event_date = null; // Inicializa a null
    public $event_time = null; // Inicializa a null

    public $reference = '';
    public $origin_in_charge = '';
    public $summary = '';
    public $file; // Para la subida de archivos (¡sin .live!)
    public $priority_id = null; // Inicializa a null para que el selector no tenga una opción preseleccionada
    public $registration_date;
    public $status = 'en_proceso'; // Inicializa con el nuevo valor por defecto en español
    public $indication = null; // Nuevo campo para las indicaciones

    // Listas para poblar los selectores en la vista
    public $offices = [];
    public $priorities = [];

    /**
     * Monta el componente, inicializando las listas de datos
     * y estableciendo valores por defecto para el formulario.
     */
    public function mount()
    {
        $this->offices = Office::all();
        $this->priorities = Priority::all();
        $this->registration_date = now()->toDateString(); // Establece la fecha actual por defecto
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
     * Guarda el nuevo documento en la base de datos.
     * Este método es llamado cuando el formulario es enviado.
     */
    public function save()
    {
        // 1. Instanciar el Form Request para obtener sus reglas de validación y mensajes personalizados.
        $storeDocumentRequest = new StoreDocumentRequest();

        try {
            // 2. Realizar la validación de Livewire.
            // Livewire usa las propiedades públicas del componente ($this->code, $this->file, etc.)
            // para validar contra las reglas proporcionadas por el Form Request.
            $validatedData = $this->validate(
                $storeDocumentRequest->rules(),
                $storeDocumentRequest->messages()
            );

            // --- PUNTO DE DEPURACIÓN (Si la validación pasa, verás los datos aquí) ---
            // dd($validatedData);

            $documentFile = null;

            // 3. Manejar la subida del archivo adjunto.
            // $this->file es un objeto TemporaryUploadedFile de Livewire.
            if ($this->file) {
                // Guarda el archivo en el disco 'public' dentro de la carpeta 'documents'
                $filePath = $this->file->store('documents', 'public');

                // Crea un registro en la tabla 'files' para el archivo subido
                $documentFile = DocumentFile::create([
                    'original_name' => $this->file->getClientOriginalName(),
                    'path' => $filePath,
                    'file_type' => $this->file->getMimeType(),
                ]);
                // Asigna el ID del registro del archivo a los datos validados para la tabla 'documents'
                $validatedData['file_id'] = $documentFile->id;
            } else {
                // Si 'file' es requerido y no está presente, la validación debería haber fallado antes.
                // Esto es un fallback, asegurando que file_id sea null si por alguna razón no hay archivo.
                $validatedData['file_id'] = null;
            }

            // 4. Elimina el objeto 'file' del array de datos validados.
            // 'file' es solo para la subida, no es una columna directa en la tabla 'documents'.
            unset($validatedData['file']);

            // 5. Ajusta los IDs de las relaciones basadas en el tipo de origen.
            // Esto es redundante con `updatedOriginType` pero añade una capa de seguridad
            // justo antes de guardar en la base de datos.
            if ($validatedData['origin_type'] === 'internal') {
                // Si es interno, asegura que los campos externos sean nulos
                $validatedData['organization_name'] = null;
                $validatedData['external_contact_person'] = null;
                $validatedData['external_contact_role'] = null;
                $validatedData['event_date'] = null;
                $validatedData['event_time'] = null;
            } else { // 'external'
                // Si es externo, asegura que el ID de la oficina sea nulo
                $validatedData['origin_office_id'] = null;
            }

            // 6. Crea el registro del documento en la base de datos.
            Document::create($validatedData);

            // 7. Resetea las propiedades del formulario para limpiar los campos después de un guardado exitoso.
            $this->reset([
                'code', 'subject', 'origin_type', 'origin_office_id',
                'organization_name', 'external_contact_person', 'external_contact_role',
                'event_date', 'event_time',
                'reference', 'origin_in_charge', 'summary', 'file', 'priority_id',
                'status', 'indication' // Añadido el nuevo campo 'indication' y 'status'
            ]);
            // Restablece la fecha de registro a la fecha actual.
            $this->registration_date = now()->toDateString();

            // 8. Muestra un mensaje de éxito al usuario.
            session()->flash('status', 'Documento registrado exitosamente.');

            // 9. Redirige al usuario a la página de índice de documentos.
            // 'navigate: true' habilita la navegación suave de Livewire 3 para una mejor UX.
            return $this->redirect(route('documents.index'), navigate: true);

        } catch (ValidationException $e) {
            // Captura las excepciones de validación. Livewire automáticamente mostrará los mensajes de error
            // en la vista gracias a las directivas @error.
            // Puedes descomentar la siguiente línea si necesitas depurar los errores en el servidor.
            // dd($e->errors());
            throw $e; // Re-lanza la excepción para que Livewire la capture.
        } catch (\Exception $e) {
            // Captura cualquier otra excepción inesperada (ej. problemas de base de datos, archivo).
            session()->flash('error', 'Ocurrió un error inesperado al registrar el documento: ' . $e->getMessage());
            // Puedes descomentar la siguiente línea si necesitas depurar errores inesperados.
            // dd($e);
        }
    }

    /**
     * Renderiza la vista Blade asociada a este componente.
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

        return view('livewire.other.documents.create-document', [
            'indicationOptions' => $indicationOptions,
        ]);
    }
}
