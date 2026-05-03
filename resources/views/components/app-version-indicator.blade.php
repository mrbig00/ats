@php
    $version = trim((string) config('app.version'));
@endphp
@if ($version !== '')
    <div {{ $attributes->merge(['class' => 'pt-10']) }}>
        <flux:text class="select-none text-xs text-zinc-400 dark:text-zinc-600">
            {{ __('common.version', ['version' => $version]) }}
        </flux:text>
    </div>
@endif
