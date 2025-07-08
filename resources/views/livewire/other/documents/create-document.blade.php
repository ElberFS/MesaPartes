<div class="min-h-screen bg-gray-100 p-4 flex items-center justify-center">
    <div class="w-full max-w-4xl bg-white shadow-lg rounded-lg p-6 space-y-6">
        <h2 class="text-2xl font-bold text-gray-900 text-center">Registrar Nuevo Documento</h2>

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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Columna Izquierda --}}
                <div class="space-y-4">
                    {{-- N° de Expediente --}}
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700">N° de Expediente</label>
                        <input type="text" id="code" wire:model.live="code"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('code') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Referencia de expediente --}}
                    <div>
                        <label for="reference" class="block text-sm font-medium text-gray-700">Referencia de expediente</label>
                        <input type="text" id="reference" wire:model.live="reference"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               placeholder="Referencia del documento">
                        @error('reference') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Prioridad --}}
                    <div>
                        <label for="priority_id" class="block text-sm font-medium text-gray-700">Prioridad</label>
                        <select id="priority_id" wire:model.live="priority_id"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">Selecciona una prioridad</option>
                            @foreach ($priorities as $priority)
                                <option value="{{ $priority->id }}">{{ $priority->level }}</option>
                            @endforeach
                        </select>
                        @error('priority_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Asunto --}}
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700">Asunto</label>
                        <input type="text" id="subject" wire:model.live="subject"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('subject') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Columna Derecha --}}
                <div class="space-y-4">
                    {{-- Dependencia (Tipo de Origen) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Dependencia</label>
                        <div class="mt-1 flex items-center space-x-4">
                            <label class="inline-flex items-center">
                                <input type="radio" wire:model.live="origin_type" value="internal" class="form-radio text-indigo-600">
                                <span class="ml-2 text-gray-700">Interna</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" wire:model.live="origin_type" value="external" class="form-radio text-indigo-600">
                                <span class="ml-2 text-gray-700">Externa</span>
                            </label>
                        </div>
                        @error('origin_type') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Encargado de dependencia (Encargado de Origen) - Ahora condicional --}}
                    @if ($origin_type === 'internal')
                        <div>
                            <label for="origin_in_charge" class="block text-sm font-medium text-gray-700">Encargado de dependencia</label>
                            <input type="text" id="origin_in_charge" wire:model.live="origin_in_charge"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                   placeholder="Nombre de la persona encargada">
                            @error('origin_in_charge') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    @endif

                    {{-- Selector de Oficina de Origen (si es interno) --}}
                    @if ($origin_type === 'internal')
                        <div>
                            <label for="origin_office_id" class="block text-sm font-medium text-gray-700">Oficina de Origen</label>
                            <select id="origin_office_id" wire:model.live="origin_office_id"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Selecciona una oficina</option>
                                @foreach ($offices as $office)
                                    <option value="{{ $office->id }}">{{ $office->name }}</option>
                                @endforeach
                            </select>
                            @error('origin_office_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    @endif

                    {{-- Campos de Origen Externo (si es externo) --}}
                    @if ($origin_type === 'external')
                        <div class="space-y-4 border border-gray-200 p-4 rounded-md bg-gray-50">

                            <div>
                                <label for="organization_name" class="block text-sm font-medium text-gray-700">Nombre de la Organización</label>
                                <input type="text" id="organization_name" wire:model.live="organization_name"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                       placeholder="Ej: Universidad Nacional">
                                @error('organization_name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="external_contact_person" class="block text-sm font-medium text-gray-700">Persona  Externa</label>
                                <input type="text" id="external_contact_person" wire:model.live="external_contact_person"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                       placeholder="Ej: Juan Pérez">
                                @error('external_contact_person') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="external_contact_role" class="block text-sm font-medium text-gray-700">Cargo de Personas</label>
                                <input type="text" id="external_contact_role" wire:model.live="external_contact_role"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                       placeholder="Ej: Gerente de Proyecto">
                                @error('external_contact_role') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="event_date" class="block text-sm font-medium text-gray-700">Fecha del Evento/Convenio (Opcional)</label>
                                    <input type="date" id="event_date" wire:model.live="event_date"
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @error('event_date') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="event_time" class="block text-sm font-medium text-gray-700">Hora del Evento/Convenio (Opcional)</label>
                                    <input type="time" id="event_time" wire:model.live="event_time"
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @error('event_time') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Resumen (campo de ancho completo) --}}
            <div>
                <label for="summary" class="block text-sm font-medium text-gray-700">Resumen</label>
                <textarea id="summary" wire:model.live="summary" rows="3"
                          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                          placeholder="Breve resumen del contenido del documento"></textarea>
                @error('summary') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Archivo Adjunto --}}
            <div>
                <label for="file" class="block text-sm font-medium text-gray-700">Archivo Adjunto (PDF, DOC, DOCX - Max 10MB)</label>
                <input type="file" id="file" wire:model="file"
                       class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                @error('file') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                <div wire:loading wire:target="file" class="mt-2 text-sm text-indigo-600">Subiendo archivo...</div>
            </div>

            {{-- Fecha de Registro y Botones --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                <div>
                    <label for="registration_date" class="block text-sm font-medium text-gray-700">Fecha de Registro</label>
                    <input type="date" id="registration_date" wire:model.live="registration_date"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('registration_date') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('documents.index') }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium
                              text-gray-700 bg-white hover:bg-gray-50
                              focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                       wire:navigate>
                        Cancelar
                    </a>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm
                                   text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Registrar Documento
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
