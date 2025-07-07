<div class="min-h-screen bg-gray-100 flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white shadow-lg rounded-lg p-6 space-y-6">
        <h2 class="text-2xl font-bold text-gray-900 text-center">Crear Nuevo Usuario</h2>

        {{-- Mensaje de éxito --}}
        @if (session('status'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">¡Éxito!</strong>
                <span class="block sm:inline">{{ session('status') }}</span>
            </div>
        @endif

        <form wire:submit.prevent="save" class="space-y-4">
            {{-- Campo Nombre --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
                <input type="text" id="name" wire:model.live="name"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm
                              focus:outline-none focus:ring-indigo-500 focus:border-indigo-500
                              sm:text-sm bg-white text-gray-900 placeholder-gray-400"
                       placeholder="Nombre del usuario">
                @error('name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Campo Correo Electrónico --}}
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                <input type="email" id="email" wire:model.live="email"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm
                              focus:outline-none focus:ring-indigo-500 focus:border-indigo-500
                              sm:text-sm bg-white text-gray-900 placeholder-gray-400"
                       placeholder="ejemplo@dominio.com">
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Campo Contraseña --}}
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                <input type="password" id="password" wire:model.live="password"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm
                              focus:outline-none focus:ring-indigo-500 focus:border-indigo-500
                              sm:text-sm bg-white text-gray-900 placeholder-gray-400">
                @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Campo Confirmar Contraseña --}}
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Contraseña</label>
                <input type="password" id="password_confirmation" wire:model.live="password_confirmation"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm
                              focus:outline-none focus:ring-indigo-500 focus:border-indigo-500
                              sm:text-sm bg-white text-gray-900 placeholder-gray-400">
            </div>

            {{-- Selector de Rol --}}
            <div>
                <label for="selectedRole" class="block text-sm font-medium text-gray-700">Rol</label>
                <select id="selectedRole" wire:model.live="selectedRole"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm
                               focus:outline-none focus:ring-indigo-500 focus:border-indigo-500
                               sm:text-sm bg-white text-gray-900">
                    <option value="">Selecciona un rol</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                    @endforeach
                </select>
                @error('selectedRole')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Selector de Oficina --}}
            <div>
                <label for="selectedOfficeId" class="block text-sm font-medium text-gray-700">Oficina</label>
                <select id="selectedOfficeId" wire:model.live="selectedOfficeId"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm
                               focus:outline-none focus:ring-indigo-500 focus:border-indigo-500
                               sm:text-sm bg-white text-gray-900">
                    <option value="">Selecciona una oficina</option>
                    @foreach ($offices as $office)
                        <option value="{{ $office->id }}">{{ $office->name }}</option>
                    @endforeach
                </select>
                @error('selectedOfficeId')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('users.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium
                          text-gray-700 bg-white hover:bg-gray-50
                          focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                   wire:navigate>
                    Cancelar
                </a>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm
                               text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Crear Usuario
                </button>
            </div>
        </form>
    </div>
</div>
