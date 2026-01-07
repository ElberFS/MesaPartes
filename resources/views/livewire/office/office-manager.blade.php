<div class="flex flex-col gap-6">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-neutral-900 dark:text-white">
            Oficinas
        </h2>

        <button
            type="button"
            wire:click="create"
            wire:click="$set('isEditing', false)"
            wire:click="$set('officeId', null)"
            wire:click="$set('showModal', true)"
            class="rounded-lg bg-neutral-900 px-4 py-2 text-sm text-white">
            Nueva oficina
        </button>
    </div>

    {{-- TABLA --}}
    <div
        class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-900">

        <div class="border-b p-4">
            <input
                type="search"
                wire:model.debounce.300ms="search"
                placeholder="Buscar oficina..."
                class="w-full rounded-lg border p-2.5">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-neutral-50">
                    <tr>
                        <th class="px-6 py-3 text-left">Nombre</th>
                        <th class="px-6 py-3 text-left">Acrónimo</th>
                        <th class="px-6 py-3 text-center">Estado</th>
                        <th class="px-6 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($offices as $office)
                        <tr class="border-t">
                            <td class="px-6 py-4">{{ $office->name }}</td>
                            <td class="px-6 py-4">{{ $office->acronym }}</td>
                            <td class="px-6 py-4 text-center">
                                <button
                                    wire:click="toggleStatus({{ $office->id }})"
                                    class="{{ $office->is_active ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $office->is_active ? 'Activo' : 'Inactivo' }}
                                </button>
                            </td>
                            <td class="px-6 py-4 text-right space-x-3">
                                <button
                                    wire:click="edit({{ $office->id }})"
                                    wire:click="$set('showModal', true)"
                                    class="text-blue-600">
                                    Editar
                                </button>
                                <button
                                    wire:click="delete({{ $office->id }})"
                                    class="text-red-600">
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-6 text-center text-neutral-500">
                                Sin resultados
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t p-4">
            {{ $offices->links() }}
        </div>
    </div>

    {{-- MODAL --}}
    <x-modal wire:model="showModal" :title="$isEditing ? 'Editar oficina' : 'Nueva oficina'">
        <form wire:submit.prevent="save" class="grid gap-4">

            <div>
                <label class="text-sm font-medium">Nombre</label>
                <input
                    type="text"
                    wire:model.defer="name"
                    class="w-full rounded-lg border p-2.5">
                @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="text-sm font-medium">Acrónimo</label>
                <input
                    type="text"
                    wire:model.defer="acronym"
                    class="w-full rounded-lg border p-2.5">
                @error('acronym') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center gap-3">
                <input type="checkbox" wire:model.defer="is_active">
                <span>Activa</span>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button
                    type="button"
                    @click="$wire.set('showModal', false)"
                    class="rounded-lg border px-4 py-2">
                    Cancelar
                </button>

                <button
                    type="submit"
                    class="rounded-lg bg-neutral-900 px-6 py-2 text-white">
                    {{ $isEditing ? 'Actualizar' : 'Guardar' }}
                </button>
            </div>

        </form>
    </x-modal>

</div>
