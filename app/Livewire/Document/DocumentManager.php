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

    public bool $isEditing = false;
    public bool $showModal = false;

    public function render(DocumentService $service)
    {
        return view('livewire.document.document-manager', [
            'documents' => $service->getAll(10, $this->search),
        ]);
    }

    public function create()
    {
        $this->reset([
            'documentId',
            'subject',
            'description',
            'expediente_id',
            'file',
            'isEditing',
        ]);

        $this->resetValidation();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $document = Document::findOrFail($id);

        $this->isEditing     = true;
        $this->documentId    = $document->id;
        $this->subject       = $document->subject;
        $this->description   = $document->description;
        $this->expediente_id = $document->expediente_id;

        $this->resetValidation();
        $this->showModal = true;
    }

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

        $this->reset([
            'documentId',
            'subject',
            'description',
            'expediente_id',
            'file',
            'isEditing',
        ]);

        $this->resetValidation();
        $this->showModal = false;

        session()->flash('status', 'Documento guardado correctamente.');
    }

    public function delete($id, DocumentService $service)
    {
        $document = Document::findOrFail($id);
        $service->delete($document);
    }
}
