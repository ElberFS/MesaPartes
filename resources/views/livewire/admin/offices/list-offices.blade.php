<div class="min-h-screen bg-gray-100 p-6"> {{-- Fondo general ligeramente gris --}}
    {{-- Contenedor principal blanco, ahora ocupa todo el ancho disponible con padding horizontal --}}
    <div class="w-full mx-auto bg-white shadow-lg rounded-lg p-6 space-y-6">
        <h2 class="text-2xl font-bold text-gray-900 text-center mb-6">Listado de Oficinas</h2>

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

        <div class="flex justify-end mb-4">
            <a href="{{ route('offices.create') }}"
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm
                      text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Crear Nueva Oficina
            </a>
        </div>

        @if ($offices->isEmpty())
            <p class="text-gray-700 text-center">No hay oficinas registradas.</p>
        @else
            <div class="overflow-x-auto rounded-lg shadow-md">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider rounded-tl-lg">ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider rounded-tr-lg">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($offices as $office)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $office->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $office->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('offices.edit', $office->id) }}"
                                       class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</a>
                                    <button wire:click="confirmOfficeDeletion({{ $office->id }})"
                                            class="text-red-600 hover:text-red-900">Eliminar</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $offices->links() }} {{-- Muestra los enlaces de paginación --}}
            </div>
        @endif
    </div>

    {{-- Modal de Confirmación de Eliminación --}}
    @if ($confirmingOfficeDeletion)
        <div class="fixed inset-0  bg-opacity-75 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-sm space-y-4">
                <h3 class="text-lg font-bold text-gray-900">Confirmar Eliminación</h3>
                <p class="text-gray-700">¿Estás seguro de que deseas eliminar esta oficina? Esta acción no se puede deshacer.</p>
                <div class="flex justify-end space-x-3">
                    <button wire:click="$set('confirmingOfficeDeletion', false)"
                            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium
                                   text-gray-700 bg-white hover:bg-gray-50
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancelar
                    </button>
                    <button wire:click="deleteOffice"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium
                                   text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
