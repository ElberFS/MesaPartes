<div class="flex flex-col gap-6">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-neutral-900 dark:text-white">
            Documentos
        </h2>

        <button
            type="button"
            wire:click="create"
            class="rounded-lg bg-neutral-900 px-4 py-2 text-sm text-white">
            Nuevo documento
        </button>
    </div>

    {{-- TABLA --}}
    <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-900">

        <div class="border-b p-4">
            <input
                type="search"
                wire:model.debounce.300ms="search"
                placeholder="Buscar por código o asunto..."
                class="w-full rounded-lg border p-2.5">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-neutral-50 dark:bg-neutral-800">
                    <tr>
                        <th class="px-6 py-3 text-left">Código</th>
                        <th class="px-6 py-3 text-left">Asunto</th>
                        <th class="px-6 py-3 text-left">Oficina</th>
                        <th class="px-6 py-3 text-left">Fecha</th>
                        <th class="px-6 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($documents as $document)
                        <tr class="border-t dark:border-neutral-700">
                            <td class="px-6 py-4 font-mono text-sm">
                                {{ $document->code }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $document->subject }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $document->office?->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-neutral-500">
                                {{ $document->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-right space-x-3">

                                {{-- VER PDF --}}
                                <a
                                    href="{{ Storage::url($document->file_path) }}"
                                    target="_blank"
                                    class="text-emerald-600">
                                    Ver
                                </a>

                                {{-- EDITAR --}}
                                <button
                                    wire:click="edit({{ $document->id }})"
                                    class="text-blue-600">
                                    Editar
                                </button>

                                {{-- ELIMINAR --}}
                                <button
                                    wire:click="delete({{ $document->id }})"
                                    class="text-red-600"
                                    onclick="confirm('¿Eliminar documento?') || event.stopImmediatePropagation()">
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
            {{ $documents->links() }}
        </div>
    </div>

    {{-- MODAL --}}
    <x-modal wire:model="showModal" :title="$isEditing ? 'Editar documento' : 'Nuevo documento'">
        <form wire:submit.prevent="save" class="grid gap-4">

            @if ($isEditing)
                <div>
                    <label class="text-sm font-medium">Código</label>
                    <input
                        type="text"
                        value="{{ optional($documents->firstWhere('id', $documentId))->code }}"
                        disabled
                        readonly
                        class="w-full rounded-lg border bg-neutral-100 p-2.5 font-mono text-sm text-neutral-600 cursor-not-allowed">
                </div>
            @endif

            <div>
                <label class="text-sm font-medium">Oficina</label>
                <input
                    type="text"
                    value="{{ auth()->user()->offices->first()?->name }}"
                    disabled
                    readonly
                    class="w-full rounded-lg border bg-neutral-100 p-2.5 text-sm text-neutral-600 cursor-not-allowed">
            </div>

            <div>
                <label class="text-sm font-medium">Asunto</label>
                <input
                    type="text"
                    wire:model.defer="subject"
                    class="w-full rounded-lg border p-2.5">
                @error('subject') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="text-sm font-medium">Descripción</label>
                <textarea
                    wire:model.defer="description"
                    rows="3"
                    class="w-full rounded-lg border p-2.5"></textarea>
                @error('description') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="text-sm font-medium">Archivo (PDF)</label>
                <input
                    type="file"
                    wire:model="file"
                    accept="application/pdf"
                    class="w-full rounded-lg border p-2.5">
                @error('file') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
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
