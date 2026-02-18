<div class="flex flex-col gap-3">
    <div class="flex h-64 flex-col gap-2 overflow-y-auto rounded border border-zinc-200 bg-zinc-50 p-2 dark:border-zinc-700 dark:bg-zinc-800/50">
        @forelse($messages as $message)
            <div class="flex flex-col gap-0.5 rounded-lg px-2 py-1">
                <flux:text class="text-xs font-medium text-zinc-500 dark:text-zinc-400">
                    {{ $message->user->name ?? $message->user->email }} · {{ $message->created_at->format('d.m. H:i') }}
                </flux:text>
                <flux:text class="text-sm">{{ $message->body }}</flux:text>
            </div>
        @empty
            <flux:text class="py-4 text-center text-sm text-zinc-500">{{ __('dashboard.chat_empty') }}</flux:text>
        @endforelse
    </div>
    <form wire:submit="sendMessage" class="flex gap-2">
        <flux:input wire:model="body" placeholder="{{ __('dashboard.chat_placeholder') }}" class="min-w-0 flex-1" />
        <flux:button type="submit" size="sm">{{ __('common.save') }}</flux:button>
    </form>
</div>
