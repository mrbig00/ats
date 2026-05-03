<x-layouts::app.sidebar :title="$title ?? null">
    <flux:main>
        {{ $slot }}
        <x-app-version-indicator />
    </flux:main>
</x-layouts::app.sidebar>
