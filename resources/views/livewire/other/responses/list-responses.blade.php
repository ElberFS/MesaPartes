<div class="min-h-screen bg-gray-100 p-4">
        <div class="w-full max-w-6xl mx-auto bg-white shadow-lg rounded-lg p-6 space-y-6">
            <h2 class="text-2xl font-bold text-gray-900 text-center">
                Respuestas @if($document) para Documento: {{ $document->code }} @else del Sistema @endif
            </h2>

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

            <div class="flex justify-between items-center mb-4">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar respuestas..."
                        class="flex-grow mr-4 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">

                {{-- BOTÓN: Registrar Nueva Respuesta (condicional si hay un documento) --}}
                @if ($document)
                    <a href="{{ route('responses.create', ['document' => $document->id]) }}"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm
                                    text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        wire:navigate>
                        Registrar Nueva Respuesta
                    </a>
                @endif
            </div>

            @if ($responses->isEmpty())
                <p class="text-center text-gray-500">No hay respuestas registradas para este documento.</p>
            @else
                <div class="overflow-x-auto shadow-sm  md:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Asunto</th>
                                {{-- Cambiado a 'Destino' para ser más genérico --}}
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Destino</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Fecha</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Final</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                    <span class="sr-only">Acciones</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach ($responses as $response)
                                <tr>
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{ $response->subject }}</td>
                                    {{-- Lógica para mostrar el destino según el tipo --}}
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        @if ($response->destination_type === 'internal')
                                            {{ $response->destinationOffice->name ?? 'Oficina Desconocida' }}
                                        @else
                                            {{ $response->destination_organization_name ?? 'Organización Externa Desconocida' }}
                                            @if ($response->destination_contact_person)
                                                <br> <small class="text-gray-400">({{ $response->destination_contact_person }})</small>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $response->date->format('d/m/Y') }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        @if ($response->is_final_response)
                                            <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">Sí</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-800">No</span>
                                        @endif
                                    </td>
                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                        @if ($response->file)
                                            <a href="{{ Storage::url($response->file->path) }}" target="_blank" class="text-blue-600 hover:text-blue-900 mr-3">Ver Archivo</a>
                                        @endif
                                        <a href="{{ route('responses.edit', $response->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3" wire:navigate>Editar</a>
                                        <button wire:click="confirmResponseDeletion({{ $response->id }})"
                                                class="text-red-600 hover:text-red-900">Eliminar</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $responses->links() }}
                </div>
            @endif

            <div class="mt-6 flex justify-end">
                <a href="{{ route('documents.index') }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium
                                    text-gray-700 bg-white hover:bg-gray-50
                                    focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    wire:navigate>
                    Volver a Documentos
                </a>
            </div>
        </div>

        {{-- Modal de Confirmación de Eliminación --}}
        @if ($confirmingResponseDeletion)
            <div class="fixed inset-0 bg-white bg-opacity-75 flex items-center justify-center p-4 z-50">
                <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-sm space-y-4">
                    <h3 class="text-lg font-bold text-gray-900">Confirmar Eliminación</h3>
                    <p class="text-gray-700">¿Estás seguro de que deseas eliminar esta respuesta? Esta acción eliminará también el archivo adjunto y no se puede deshacer.</p>
                    <div class="flex justify-end space-x-3">
                        <button wire:click="$set('confirmingResponseDeletion', false)"
                                class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium
                                            text-gray-700 bg-white hover:bg-gray-50
                                            focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancelar
                        </button>
                        <button wire:click="deleteResponse"
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium
                                            text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Eliminar
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
