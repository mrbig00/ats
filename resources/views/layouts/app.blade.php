<x-layouts::app.sidebar :title="$title ?? null">
    <flux:main>
        {{-- Block wrapper: shells use flex-1; prevents them stealing flex height so the version line sits below page content instead of at the viewport bottom. --}}
        <div class="w-full min-w-0 shrink-0 grow-0 basis-auto">
            {{ $slot }}
        </div>
        <x-app-version-indicator />
    </flux:main>
</x-layouts::app.sidebar>
