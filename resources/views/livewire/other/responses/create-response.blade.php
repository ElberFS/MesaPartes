<div class="min-h-screen bg-gray-100 p-4 flex items-center justify-center">
    <div class="w-full max-w-2xl bg-white shadow-lg rounded-lg p-6 space-y-6">
        <h2 class="text-2xl font-bold text-gray-900 text-center">
            Registrar Respuesta @if($document) para Documento: {{ $document->code }} @endif
        </h2>

        {{-- Mensajes de estado --}}
        @if (session('status'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">¡Éxito!</strong>
                <span class="block sm:inline">{{ session('status') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">¡Error!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <form wire:submit.prevent="save" class="space-y-6">
            <div>
                <label for="subject" class="block text-sm font-medium text-gray-700">Asunto de la Respuesta</label>
                <input type="text" id="subject" wire:model.live="subject"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                @error('subject') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="summary" class="block text-sm font-medium text-gray-700">Resumen de la Respuesta (Opcional)</label>
                <textarea id="summary" wire:model.live="summary" rows="3"
                          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                          placeholder="Breve resumen del contenido de la respuesta"></textarea>
                @error('summary') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="file" class="block text-sm font-medium text-gray-700">Archivo de Respuesta (PDF, DOC, DOCX - Max 10MB)</label>
                <input type="file" id="file" wire:model="file"
                       class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                @error('file') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                <div wire:loading wire:target="file" class="mt-2 text-sm text-indigo-600">Subiendo archivo...</div>
            </div>

            {{-- Tipo de Destino --}}
            <div>
                <label for="destination_type" class="block text-sm font-medium text-gray-700">Tipo de Destino</label>
                <select id="destination_type" wire:model.live="destination_type"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="internal">Interno (Oficina)</option>
                    <option value="external">Externo (Organización/Persona)</option>
                </select>
                @error('destination_type') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Campos condicionales para Destino Interno --}}
            @if ($destination_type === 'internal')
                <div>
                    <label for="destination_office_id" class="block text-sm font-medium text-gray-700">Oficina de Destino</label>
                    <select id="destination_office_id" wire:model.live="destination_office_id"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">Selecciona una oficina</option>
                        @foreach ($offices as $office)
                            <option value="{{ $office->id }}">{{ $office->name }}</option>
                        @endforeach
                    </select>
                    @error('destination_office_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            @endif

            {{-- Campos condicionales para Destino Externo --}}
            @if ($destination_type === 'external')
                <div>
                    <label for="destination_organization_name" class="block text-sm font-medium text-gray-700">Nombre de la Organización de Destino</label>
                    <input type="text" id="destination_organization_name" wire:model.live="destination_organization_name"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('destination_organization_name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="destination_contact_person" class="block text-sm font-medium text-gray-700">Persona de Contacto de Destino</label>
                    <input type="text" id="destination_contact_person" wire:model.live="destination_contact_person"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('destination_contact_person') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="destination_contact_role" class="block text-sm font-medium text-gray-700">Cargo de la Persona de Contacto (Opcional)</label>
                    <input type="text" id="destination_contact_role" wire:model.live="destination_contact_role"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('destination_contact_role') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            @endif

            <div class="flex items-center">
                <input type="checkbox" id="is_final_response" wire:model.live="is_final_response"
                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="is_final_response" class="ml-2 block text-sm text-gray-900">¿Es la respuesta final?</label>
                @error('is_final_response') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="date" class="block text-sm font-medium text-gray-700">Fecha de la Respuesta</label>
                <input type="date" id="date" wire:model.live="date"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                @error('date') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                @if($document)
                    <a href="{{ route('documents.show', $document->id) }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium
                               text-gray-700 bg-white hover:bg-gray-50
                               focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                       wire:navigate>
                        Cancelar
                    </a>
                @else
                    <a href="{{ route('responses.index') }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium
                               text-gray-700 bg-white hover:bg-gray-50
                               focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                       wire:navigate>
                        Cancelar
                    </a>
                @endif
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm
                               text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Registrar Respuesta
                </button>
            </div>
        </form>
    </div>
</div>
