<div class="flex flex-col gap-6">

    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-neutral-900 dark:text-white">
            Usuarios
        </h2>

        <button
            type="button"
            wire:click="create"
            class="rounded-lg bg-neutral-900 px-4 py-2 text-sm text-white">
            Nuevo usuario
        </button>
    </div>

    <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-900">

        <div class="border-b p-4">
            <input
                type="search"
                wire:model.debounce.300ms="search"
                placeholder="Buscar usuario..."
                class="w-full rounded-lg border p-2.5">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-neutral-50 dark:bg-neutral-800">
                    <tr>
                        <th class="px-6 py-3 text-left">Nombre</th>
                        <th class="px-6 py-3 text-left">Email</th>
                        <th class="px-6 py-3 text-left">Rol</th>
                        <th class="px-6 py-3 text-left">Oficina</th>
                        <th class="px-6 py-3 text-right">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($users as $user)
                        <tr class="border-t dark:border-neutral-700">
                            <td class="px-6 py-4">{{ $user->name }}</td>
                            <td class="px-6 py-4">{{ $user->email }}</td>
                            <td class="px-6 py-4 capitalize">
                                {{ $user->getRoleNames()->first() ?? '—' }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $user->offices->first()->name ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-right space-x-3">
                                <button
                                    wire:click="edit({{ $user->id }})"
                                    class="text-blue-600">
                                    Editar
                                </button>

                                <button
                                    wire:click="delete({{ $user->id }})"
                                    class="text-red-600">
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-neutral-500">
                                Sin resultados
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t p-4">
            {{ $users->links() }}
        </div>
    </div>

    <x-modal wire:model="showModal" :title="$isEditing ? 'Editar usuario' : 'Nuevo usuario'">
        <form wire:submit.prevent="save" class="grid gap-4">

            <div>
                <label class="text-sm font-medium">Nombre</label>
                <input type="text" wire:model.defer="name" class="w-full rounded-lg border p-2.5">
                @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="text-sm font-medium">Email</label>
                <input type="email" wire:model.defer="email" class="w-full rounded-lg border p-2.5">
                @error('email') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="text-sm font-medium">
                    Contraseña
                    @if($isEditing)
                        <span class="text-xs text-neutral-500">(opcional)</span>
                    @endif
                </label>
                <input type="password" wire:model.defer="password" class="w-full rounded-lg border p-2.5">
                @error('password') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="text-sm font-medium">Confirmar contraseña</label>
                <input type="password" wire:model.defer="password_confirmation" class="w-full rounded-lg border p-2.5">
            </div>

            <div>
                <label class="text-sm font-medium">Rol</label>
                <select wire:model.defer="role" class="w-full rounded-lg border p-2.5">
                    <option value="">Seleccionar rol</option>
                    <option value="admin">Admin</option>
                    <option value="jefe">Jefe</option>
                    <option value="secretaria">Secretaria</option>
                </select>
                @error('role') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="text-sm font-medium">Oficina</label>
                <select wire:model.defer="office_id" class="w-full rounded-lg border p-2.5">
                    <option value="">— Sin oficina —</option>
                    @foreach ($offices as $office)
                        <option value="{{ $office->id }}">{{ $office->name }}</option>
                    @endforeach
                </select>
                @error('office_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            @if($isEditing && $office_id)
                <p class="text-xs text-neutral-500">
                    Oficina actual: <strong>{{ $offices->firstWhere('id', $office_id)?->name }}</strong>
                </p>
            @endif

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" @click="$wire.set('showModal', false)" class="rounded-lg border px-4 py-2">
                    Cancelar
                </button>

                <button type="submit" class="rounded-lg bg-neutral-900 px-6 py-2 text-white">
                    {{ $isEditing ? 'Actualizar' : 'Guardar' }}
                </button>
            </div>

        </form>
    </x-modal>


</div>
