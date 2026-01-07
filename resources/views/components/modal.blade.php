@props([
    'title' => '',
])

<div
    x-data="{ open: @entangle($attributes->wire('model')) }"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center"
>
    {{-- Fondo --}}
    <div
        class="absolute inset-0 bg-black/50"
        @click="open = false"
    ></div>

    {{-- Contenido --}}
    <div
        x-show="open"
        x-transition
        class="relative w-full max-w-lg rounded-xl bg-white p-6 shadow-lg dark:bg-neutral-900"
    >
        {{-- Header --}}
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">
                {{ $title }}
            </h2>

            <button @click="open = false"
                class="text-neutral-400 hover:text-neutral-700 dark:hover:text-white">
                ✕
            </button>
        </div>

        {{-- Body --}}
        <div>
            {{ $slot }}
        </div>
    </div>
</div>
