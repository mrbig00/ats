<x-layouts::app :title="__('nav.candidates')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <flux:heading size="xl" level="1">{{ __('nav.candidates') }}</flux:heading>
        <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('dashboard.placeholder') }}</flux:text>
    </div>
</x-layouts::app>
