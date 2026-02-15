<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:header container class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden mr-2" icon="bars-2" inset="left" />

            <x-app-logo href="{{ route('dashboard') }}" wire:navigate />

            <flux:navbar class="-mb-px max-lg:hidden">
                <flux:navbar.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                    {{ __('nav.dashboard') }}
                </flux:navbar.item>
                <flux:navbar.item icon="user-plus" :href="route('candidates.index')" :current="request()->routeIs('candidates.*')" wire:navigate>
                    {{ __('nav.candidates') }}
                </flux:navbar.item>
                <flux:navbar.item icon="briefcase" :href="route('jobs.index')" :current="request()->routeIs('jobs.*')" wire:navigate>
                    {{ __('nav.jobs') }}
                </flux:navbar.item>
                <flux:navbar.item icon="users" :href="route('employees.index')" :current="request()->routeIs('employees.*')" wire:navigate>
                    {{ __('nav.employees') }}
                </flux:navbar.item>
                <flux:navbar.item icon="building-office-2" :href="route('housing.index')" :current="request()->routeIs('housing.*')" wire:navigate>
                    {{ __('nav.housing') }}
                </flux:navbar.item>
                <flux:navbar.item icon="clipboard-document-check" :href="route('todo.index')" :current="request()->routeIs('todo.*')" wire:navigate>
                    {{ __('nav.todo') }}
                </flux:navbar.item>
                <flux:navbar.item icon="calendar-days" :href="route('meetings.index')" :current="request()->routeIs('meetings.*')" wire:navigate>
                    {{ __('nav.meetings') }}
                </flux:navbar.item>
                <flux:navbar.item icon="cog-6-tooth" :href="route('settings.index')" :current="request()->routeIs('settings.*')" wire:navigate>
                    {{ __('nav.settings') }}
                </flux:navbar.item>
            </flux:navbar>

            <flux:spacer />

            <flux:navbar class="me-1.5 space-x-0.5 rtl:space-x-reverse py-0!">
                <flux:tooltip :content="__('common.search')" position="bottom">
                    <flux:navbar.item class="!h-10 [&>div>svg]:size-5" icon="magnifying-glass" href="#" :label="__('common.search')" />
                </flux:tooltip>
                <flux:tooltip :content="__('nav.repository')" position="bottom">
                    <flux:navbar.item
                        class="h-10 max-lg:hidden [&>div>svg]:size-5"
                        icon="folder-git-2"
                        href="https://github.com/laravel/livewire-starter-kit"
                        target="_blank"
                        :label="__('nav.repository')"
                    />
                </flux:tooltip>
                <flux:tooltip :content="__('nav.documentation')" position="bottom">
                    <flux:navbar.item
                        class="h-10 max-lg:hidden [&>div>svg]:size-5"
                        icon="book-open-text"
                        href="https://laravel.com/docs/starter-kits#livewire"
                        target="_blank"
                        :label="__('nav.documentation')"
                    />
                </flux:tooltip>
            </flux:navbar>

            <x-desktop-user-menu />
        </flux:header>

        <!-- Mobile Menu -->
        <flux:sidebar collapsible="mobile" sticky class="lg:hidden border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('nav.platform')">
                    <flux:sidebar.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('nav.dashboard') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="user-plus" :href="route('candidates.index')" :current="request()->routeIs('candidates.*')" wire:navigate>
                        {{ __('nav.candidates') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="briefcase" :href="route('jobs.index')" :current="request()->routeIs('jobs.*')" wire:navigate>
                        {{ __('nav.jobs') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="users" :href="route('employees.index')" :current="request()->routeIs('employees.*')" wire:navigate>
                        {{ __('nav.employees') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="building-office-2" :href="route('housing.index')" :current="request()->routeIs('housing.*')" wire:navigate>
                        {{ __('nav.housing') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="clipboard-document-check" :href="route('todo.index')" :current="request()->routeIs('todo.*')" wire:navigate>
                        {{ __('nav.todo') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="calendar-days" :href="route('meetings.index')" :current="request()->routeIs('meetings.*')" wire:navigate>
                        {{ __('nav.meetings') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="cog-6-tooth" :href="route('settings.index')" :current="request()->routeIs('settings.*')" wire:navigate>
                        {{ __('nav.settings') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <flux:sidebar.nav>
                <flux:sidebar.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                    {{ __('nav.repository') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                    {{ __('nav.documentation') }}
                </flux:sidebar.item>
            </flux:sidebar.nav>
        </flux:sidebar>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
