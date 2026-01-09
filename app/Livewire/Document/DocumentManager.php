<?php

namespace App\Livewire\Document;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Document;
use App\Services\DocumentService;
use App\Http\Requests\DocumentRequest;

class DocumentManager extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $documentId = null;

    public $subject;
    public $description;
    public $expediente_id;
    public $file;

    // 👉 Código que se muestra antes de guardar
    public $previewCode = null;

    public bool $isEditing = false;
    public bool $showModal = false;

    public function render(DocumentService $service)
    {
        return view('livewire.document.document-manager', [
            'documents' => $service->getAll(10, $this->search),
        ]);
    }

    /**
     * Abrir modal para crear documento
     * Genera el código PREVIO real
     */
    public function create(DocumentService $service)
    {
        $this->reset([
            'documentId',
            'subject',
            'description',
            'expediente_id',
            'file',
            'isEditing',
        ]);

        $office = auth()->user()
            ->offices()
            ->where('is_active', true)
            ->first();

        if (! $office) {
            abort(403, 'El usuario no tiene oficina asignada');
        }

        // ✅ Generar código REAL para mostrar
        $this->previewCode = $service->previewCode($office);

        $this->resetValidation();
        $this->showModal = true;
    }

    /**
     * Abrir modal para editar
     */
    public function edit($id)
    {
        $document = Document::findOrFail($id);

        $this->isEditing     = true;
        $this->documentId    = $document->id;
        $this->subject       = $document->subject;
        $this->description   = $document->description;
        $this->expediente_id = $document->expediente_id;

        // 🔹 En editar NO usamos preview
        $this->previewCode = null;

        $this->resetValidation();
        $this->showModal = true;
    }

    /**
     * Guardar documento
     */
    public function save(DocumentService $service)
    {
        $request = new DocumentRequest();

        if ($this->documentId) {
            $request->document = $this->documentId;
        }

        $validated = $this->validate($request->rules());

        $office = auth()->user()
            ->offices()
            ->where('is_active', true)
            ->first();

        if (! $office) {
            abort(403, 'El usuario no tiene oficina asignada');
        }

        if ($this->isEditing) {
            $document = Document::findOrFail($this->documentId);
            $service->update($document, $validated, $this->file);
        } else {
            $service->create($validated, $this->file, $office);
        }

        // 🔄 Reset limpio
        $this->reset([
            'documentId',
            'subject',
            'description',
            'expediente_id',
            'file',
            'isEditing',
            'previewCode',
        ]);

        $this->resetValidation();
        $this->showModal = false;

        session()->flash('status', 'Documento guardado correctamente.');
    }

    /**
     * Eliminar documento
     */
    public function delete($id, DocumentService $service)
    {
        $document = Document::findOrFail($id);
        $service->delete($document);
    }
}
