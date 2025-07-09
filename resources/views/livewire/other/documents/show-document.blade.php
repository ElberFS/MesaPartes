<div class="min-h-screen bg-gray-100 p-4 flex items-center justify-center">
    <div class="w-full max-w-4xl bg-white shadow-lg rounded-lg p-6 space-y-6">
        <h2 class="text-2xl font-bold text-gray-900 text-center">Detalles del Documento: {{ $document->code }}</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-gray-700">
            <div>
                <p><strong class="font-semibold">Código:</strong> {{ $document->code }}</p>
                <p><strong class="font-semibold">Asunto:</strong> {{ $document->subject }}</p>
                <p><strong class="font-semibold">Referencia:</strong> {{ $document->reference ?? 'N/A' }}</p>
                <p><strong class="font-semibold">Encargado de Origen:</strong> {{ $document->origin_in_charge ?? 'N/A' }}</p>
                <p><strong class="font-semibold">Prioridad:</strong> {{ $document->priority->level ?? 'N/A' }}</p>
                <p><strong class="font-semibold">Fecha de Registro:</strong> {{ $document->registration_date->format('d/m/Y') }}</p>
                <p><strong class="font-semibold">Estado:</strong>
                    @php
                        $statusLabels = [
                            'en_proceso' => 'En Proceso',
                            'respondido' => 'Respondido',
                            'archivado' => 'Archivado',
                        ];
                        echo $statusLabels[$document->status] ?? ucfirst(str_replace('_', ' ', $document->status));
                    @endphp
                </p>
                <p><strong class="font-semibold">Indicación:</strong> {{ $indicationLabels[$document->indication] ?? 'N/A' }}</p>
            </div>

            <div>
                <p><strong class="font-semibold">Tipo de Origen:</strong> {{ ucfirst($document->origin_type) }}</p>
                @if ($document->origin_type === 'internal')
                    <p><strong class="font-semibold">Oficina de Origen:</strong> {{ $document->originOffice->name ?? 'N/A' }}</p>
                @else
                    <p><strong class="font-semibold">Organización Externa:</strong> {{ $document->organization_name ?? 'N/A' }}</p>
                    <p><strong class="font-semibold">Persona de Contacto:</strong> {{ $document->external_contact_person ?? 'N/A' }}</p>
                    <p><strong class="font-semibold">Cargo de Contacto:</strong> {{ $document->external_contact_role ?? 'N/A' }}</p>
                    @if ($document->event_date)
                        <p><strong class="font-semibold">Fecha de Evento/Convenio:</strong> {{ $document->event_date->format('d/m/Y') }}</p>
                    @endif
                    @if ($document->event_time)
                        <p><strong class="font-semibold">Hora de Evento/Convenio:</strong> {{ $document->event_time->format('H:i') }}</p>
                    @endif
                @endif

                <p class="mt-4"><strong class="font-semibold">Resumen:</strong></p>
                <p class="whitespace-pre-wrap">{{ $document->summary ?? 'N/A' }}</p>

                <p class="mt-4"><strong class="font-semibold">Archivo Adjunto:</strong>
                    @if ($document->file)
                        <a href="{{ Storage::url($document->file->path) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 ml-2">Ver Archivo</a>
                    @else
                        Ninguno
                    @endif
                </p>
            </div>
        </div>

        <div class="flex justify-end space-x-3 mt-6">
            <a href="{{ route('documents.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium
                       text-gray-700 bg-white hover:bg-gray-50
                       focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
               wire:navigate>
                Volver a la Lista
            </a>
            <a href="{{ route('documents.edit', $document->id) }}"
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm
                       text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
               wire:navigate>
                Editar Documento
            </a>
            <a href="{{ route('responses.create', ['document' => $document->id]) }}"
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm
                       text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
               wire:navigate>
                Registrar Respuesta
            </a>
            <a href="{{ route('responses.index', ['documentId' => $document->id]) }}"
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm
                       text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
               wire:navigate>
                Ver Respuestas
            </a>
        </div>
    </div>
</div>
