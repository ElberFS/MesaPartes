<div class="min-h-screen bg-gray-100 p-6">
    <div class="w-full mx-auto bg-white shadow-lg rounded-lg p-6 space-y-6">
        <h2 class="text-2xl font-bold text-gray-900 text-center mb-6">Listado de Documentos</h2>

        {{-- Mensajes de estado --}}
        @if (session('status'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">¡Éxito!</strong>
                <span class="block sm:inline">{{ session('status') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">¡Error!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        {{-- Controles de búsqueda y filtrado --}}
        <div class="mb-4 grid grid-cols-1 md:grid-cols-4 lg:grid-cols-4 gap-4 items-center">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar por código, asunto, referencia, organización, origen..."
                   class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-full col-span-2">

            {{-- Aquí puedes decidir si mantener el selector de filterOriginType o también quitarlo para depender solo del ordenamiento y búsqueda --}}
            {{-- Si quieres quitarlo, solo borra este select: --}}
            <select wire:model.live="filterOriginType" class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-full">
                <option value="">Todos los Orígenes</option>
                <option value="internal">Interno</option>
                <option value="external">Externo</option>
            </select>



            <select wire:model.live="perPage" class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-full">
                <option value="5">5 por página</option>
                <option value="10">10 por página</option>
                <option value="20">20 por página</option>
                <option value="50">50 por página</option>
            </select>

            <div class="md:col-span-1 flex justify-end">
                <a href="{{ route('documents.create') }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm
                          text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Registrar Nuevo Documento
                </a>
            </div>
        </div>

        @if ($documents->isEmpty())
            <p class="text-gray-700 text-center">No se encontraron documentos.</p>
        @else
            <div class="overflow-x-auto rounded-lg shadow-md">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asunto</th>
                            {{-- Cabecera clickeable para ordenar por Origen --}}
                            <th wire:click="sortBy('origin')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                Origen
                                @if ($sortColumn === 'origin')
                                    <span class="ml-1 text-sm @if($sortDirection === 'asc') rotate-180 @endif">&#9650;</span>
                                @endif
                            </th>
                            {{-- Nueva columna para detalles externos si el filtro es 'external' --}}
                            @if ($filterOriginType === 'external' || empty($filterOriginType))
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detalles Externos</th>
                            @endif

                            {{-- Cabeceras clickeables para ordenar --}}
                            <th wire:click="sortBy('priority_id')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                Prioridad
                                @if ($sortColumn === 'priority_id')
                                    <span class="ml-1 text-sm @if($sortDirection === 'asc') rotate-180 @endif">&#9650;</span>
                                @endif
                            </th>
                            <th wire:click="sortBy('registration_date')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                Fecha Reg.
                                @if ($sortColumn === 'registration_date')
                                    <span class="ml-1 text-sm @if($sortDirection === 'asc') rotate-180 @endif">&#9650;</span>
                                @endif
                            </th>
                            <th wire:click="sortBy('status')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                Estado
                                @if ($sortColumn === 'status')
                                    <span class="ml-1 text-sm @if($sortDirection === 'asc') rotate-180 @endif">&#9650;</span>
                                @endif
                            </th>
                            <th wire:click="sortBy('indication')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                Indicación
                                @if ($sortColumn === 'indication')
                                    <span class="ml-1 text-sm @if($sortDirection === 'asc') rotate-180 @endif">&#9650;</span>
                                @endif
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($documents as $document)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $document->code }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $document->subject }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{-- Aquí se muestra solo el nombre de la oficina o la organización, sin (Interno/Externo) --}}
                                    @if ($document->origin_type === 'internal')
                                        {{ $document->originOffice->name ?? 'N/A' }}
                                    @else
                                        {{ $document->organization_name ?? 'N/A' }}
                                    @endif
                                </td>
                                {{-- Celda para detalles externos --}}
                                @if ($filterOriginType === 'external' || empty($filterOriginType))
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        @if ($document->origin_type === 'external')
                                            <div>Encargado: {{ $document->external_contact_person ?? 'N/A' }}</div>
                                            <div>Cargo: {{ $document->external_contact_role ?? 'N/A' }}</div>
                                            @if ($document->event_date)
                                                <div>Fecha Evento: {{ $document->event_date->format('d/m/Y') }}</div>
                                            @endif
                                            @if ($document->event_time)
                                                <div>Hora Evento: {{ $document->event_time->format('H:i') }}</div>
                                            @endif
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                @endif
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{-- Clases de prioridad construidas dinámicamente --}}
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @php
                                            $priorityClasses = '';
                                            if ($document->priority->level === 'Alta') {
                                                $priorityClasses = 'bg-red-100 text-red-800';
                                            } elseif ($document->priority->level === 'Media') {
                                                $priorityClasses = 'bg-yellow-100 text-yellow-800';
                                            } else { // Baja
                                                $priorityClasses = 'bg-green-100 text-green-800';
                                            }
                                        @endphp
                                        {{ $priorityClasses }}">
                                        {{ $document->priority->level ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $document->registration_date->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{-- Clases de estado construidas dinámicamente --}}
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @php
                                            $statusClasses = '';
                                            if ($document->status === 'en_proceso') {
                                                $statusClasses = 'bg-blue-100 text-blue-800';
                                            } elseif ($document->status === 'respondido') {
                                                $statusClasses = 'bg-purple-100 text-purple-800';
                                            } else { // archivado
                                                $statusClasses = 'bg-gray-100 text-gray-800';
                                            }
                                        @endphp
                                        {{ $statusClasses }}">
                                        {{ $statusLabels[$document->status] ?? ucfirst(str_replace('_', ' ', $document->status)) }}
                                    </span>
                                </td>
                                {{-- Celda para Indicación --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $indicationOptions[$document->indication] ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    {{-- Botón "Ver Documento" --}}
                                    @if ($document->file)
                                        <a href="{{ route('documents.view-file', $document->id) }}" target="_blank"
                                           class="text-blue-600 hover:text-blue-900 mr-3">Ver Documento</a>
                                    @endif
                                    <a href="{{ route('documents.edit', $document->id) }}"
                                       class="text-indigo-600 hover:text-indigo-900 mr-3" wire:navigate>Editar</a>
                                    <a href="{{ route('responses.create', ['document' => $document->id]) }}"
                                       class="text-green-600 hover:text-green-900 mr-3" wire:navigate>Responder</a>
                                    {{-- Botón "Ver Respuestas" --}}
                                    <a href="{{ route('responses.index', ['documentId' => $document->id]) }}"
                                       class="text-purple-600 hover:text-purple-900 mr-3" wire:navigate>Ver Respuestas</a>
                                    <button wire:click="confirmDocumentDeletion({{ $document->id }})"
                                            class="text-red-600 hover:text-red-900">Eliminar</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $documents->links() }}
            </div>
        @endif
    </div>

    {{-- Modal de Confirmación de Eliminación --}}
    @if ($confirmingDocumentDeletion)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-sm space-y-4">
                <h3 class="text-lg font-bold text-gray-900">Confirmar Eliminación</h3>
                <p class="text-gray-700">¿Estás seguro de que deseas eliminar este documento? Esta acción eliminará también el archivo adjunto y no se puede deshacer.</p>
                <div class="flex justify-end space-x-3">
                    <button wire:click="$set('confirmingDocumentDeletion', false)"
                            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium
                                   text-gray-700 bg-white hover:bg-gray-50
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancelar
                    </button>
                    <button wire:click="deleteDocument"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium
                                   text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
