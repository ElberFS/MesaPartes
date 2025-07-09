<?php

namespace App\Livewire\Other\Documents;

use App\Models\Document;
use Livewire\Component;

class ShowDocument extends Component
{
    public Document $document;

    /**
     * Mount the component, injecting the Document model.
     * Monta el componente, inyectando el modelo Documento.
     *
     * @param Document $document El documento a mostrar.
     */
    public function mount(Document $document)
    {
        $this->document = $document->load(['originOffice', 'file', 'priority']); // Carga las relaciones necesarias
    }

    /**
     * Render the component's view.
     * Renderiza la vista del componente.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        // Opciones para el campo 'indication' (para mostrar el label en español)
        $indicationLabels = [
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

        return view('livewire.other.documents.show-document', [
            'indicationLabels' => $indicationLabels,
        ]);
    }
}
