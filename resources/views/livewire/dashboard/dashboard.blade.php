<div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
    {{-- Top KPI cards: Active candidates, Interviews this week, Hired this month, Active employees, Free rooms --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <flux:card class="flex flex-col gap-1">
            <flux:subheading class="text-zinc-500 dark:text-zinc-400">{{ __('dashboard.kpi_active_candidates') }}</flux:subheading>
            <flux:heading size="2xl" level="2">{{ $kpis->activeCandidatesCount }}</flux:heading>
        </flux:card>
        <flux:card class="flex flex-col gap-1">
            <flux:subheading class="text-zinc-500 dark:text-zinc-400">{{ __('dashboard.kpi_interviews_this_week') }}</flux:subheading>
            <flux:heading size="2xl" level="2">{{ $kpis->interviewsThisWeekCount }}</flux:heading>
        </flux:card>
        <flux:card class="flex flex-col gap-1">
            <flux:subheading class="text-zinc-500 dark:text-zinc-400">{{ __('dashboard.kpi_hired_this_month') }}</flux:subheading>
            <flux:heading size="2xl" level="2">{{ $kpis->hiredThisMonthCount }}</flux:heading>
        </flux:card>
        <flux:card class="flex flex-col gap-1">
            <flux:subheading class="text-zinc-500 dark:text-zinc-400">{{ __('dashboard.kpi_active_employees') }}</flux:subheading>
            <flux:heading size="2xl" level="2">{{ $kpis->activeEmployeesCount }}</flux:heading>
        </flux:card>
        <flux:card class="flex flex-col gap-1">
            <flux:subheading class="text-zinc-500 dark:text-zinc-400">{{ __('dashboard.kpi_free_rooms') }}</flux:subheading>
            <flux:heading size="2xl" level="2">{{ $kpis->freeRoomsCount }}</flux:heading>
        </flux:card>
    </div>

    {{-- Chart: Hires per month / Candidate activity (fake data) --}}
    <flux:card class="overflow-hidden">
        <flux:heading size="lg" level="2" class="mb-4">{{ __('dashboard.chart_hires_activity') }}</flux:heading>
        <div class="flex flex-wrap items-end gap-2 sm:gap-1 sm:flex-nowrap">
            @php
                $maxHires = $hiresActivityChartData->isEmpty() ? 1 : $hiresActivityChartData->max('hires');
                $maxActivity = $hiresActivityChartData->isEmpty() ? 1 : $hiresActivityChartData->max('candidateActivity');
                $maxVal = max($maxHires, $maxActivity);
            @endphp
            @foreach($hiresActivityChartData as $row)
                <div class="flex min-w-0 flex-1 flex-col items-center gap-1">
                    <div class="flex h-32 w-full min-w-0 items-end justify-center gap-0.5 sm:gap-1" style="min-height: 8rem;">
                        <div
                            class="h-full min-w-[6px] max-w-[20px] flex-1 rounded-t bg-emerald-500 dark:bg-emerald-600"
                            style="height: {{ $maxVal > 0 ? round(($row->hires / $maxVal) * 100) : 0 }}%;"
                            title="{{ __('dashboard.chart_hires') }}: {{ $row->hires }}"
                        ></div>
                        <div
                            class="h-full min-w-[6px] max-w-[20px] flex-1 rounded-t bg-blue-500 dark:bg-blue-600"
                            style="height: {{ $maxVal > 0 ? round(($row->candidateActivity / $maxVal) * 100) : 0 }}%;"
                            title="{{ __('dashboard.chart_candidate_activity') }}: {{ $row->candidateActivity }}"
                        ></div>
                    </div>
                    <flux:text class="truncate text-xs text-zinc-500 dark:text-zinc-400" title="{{ $row->month }}">{{ $row->monthShort }}</flux:text>
                </div>
            @endforeach
        </div>
        <div class="mt-4 flex flex-wrap gap-4 border-t border-zinc-200 pt-4 dark:border-zinc-700">
            <span class="flex items-center gap-2 text-sm">
                <span class="h-3 w-3 rounded bg-emerald-500 dark:bg-emerald-600"></span>
                <flux:text class="text-zinc-600 dark:text-zinc-300">{{ __('dashboard.chart_hires') }}</flux:text>
            </span>
            <span class="flex items-center gap-2 text-sm">
                <span class="h-3 w-3 rounded bg-blue-500 dark:bg-blue-600"></span>
                <flux:text class="text-zinc-600 dark:text-zinc-300">{{ __('dashboard.chart_candidate_activity') }}</flux:text>
            </span>
        </div>
    </flux:card>

    {{-- Secondary KPIs (new applicants, upcoming departures, open positions) --}}
    <div class="grid gap-4 sm:grid-cols-3">
        <flux:card class="flex flex-col gap-1">
            <flux:subheading class="text-zinc-500 dark:text-zinc-400">{{ __('dashboard.kpi_new_applicants') }}</flux:subheading>
            <flux:heading size="xl" level="2">{{ $kpis->newApplicantsCount }}</flux:heading>
            <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('dashboard.kpi_new_applicants_period', ['days' => 30]) }}</flux:text>
        </flux:card>
        <flux:card class="flex flex-col gap-1">
            <flux:subheading class="text-zinc-500 dark:text-zinc-400">{{ __('dashboard.kpi_upcoming_departures') }}</flux:subheading>
            <flux:heading size="xl" level="2">{{ $kpis->upcomingDeparturesCount }}</flux:heading>
            <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('dashboard.kpi_next_30_days') }}</flux:text>
        </flux:card>
        <flux:card class="flex flex-col gap-1">
            <flux:subheading class="text-zinc-500 dark:text-zinc-400">{{ __('dashboard.kpi_open_positions') }}</flux:subheading>
            <flux:heading size="xl" level="2">{{ $kpis->openPositionsCount }}</flux:heading>
        </flux:card>
    </div>

    {{-- Activity chart + Calendar row --}}
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-1">
            <flux:card>
                <flux:heading size="lg" level="2" class="mb-4">{{ __('dashboard.activity_chart') }}</flux:heading>
                <div class="flex gap-2 mb-4">
                    <flux:button size="sm" variant="{{ $chartPeriod === '7' ? 'primary' : 'ghost' }}" wire:click="setChartPeriod('7')">{{ __('dashboard.period_7_days') }}</flux:button>
                    <flux:button size="sm" variant="{{ $chartPeriod === '30' ? 'primary' : 'ghost' }}" wire:click="setChartPeriod('30')">{{ __('dashboard.period_30_days') }}</flux:button>
                </div>
                <div class="flex flex-col gap-1" style="min-height: 200px;">
                    @php
                        $maxCount = $chartData->isEmpty() ? 1 : max(array_map(fn ($row) => $row->candidate_created + $row->candidate_stage_changed + $row->employee_hired + $row->employee_terminated + $row->meeting_scheduled + $row->task_created, $chartData->all()));
                    @endphp
                    @foreach($chartData as $row)
                        @php
                            $total = $row->candidate_created + $row->candidate_stage_changed + $row->employee_hired + $row->employee_terminated + $row->meeting_scheduled + $row->task_created;
                            $pct = $maxCount > 0 ? round(($total / $maxCount) * 100) : 0;
                        @endphp
                        <div class="flex items-center gap-2 text-sm">
                            <span class="w-20 shrink-0 text-zinc-500 dark:text-zinc-400">{{ \Carbon\Carbon::parse($row->date)->format('d.m.') }}</span>
                            <div class="h-6 min-w-[80px] flex-1 overflow-hidden rounded bg-zinc-200 dark:bg-zinc-700" title="{{ $total }} {{ __('dashboard.events') }}">
                                <div class="h-full bg-zinc-600 dark:bg-zinc-500 rounded" style="width: {{ max($pct, $total > 0 ? 5 : 0) }}%"></div>
                            </div>
                            <span class="w-8 text-right tabular-nums">{{ $total }}</span>
                        </div>
                    @endforeach
                    @if($chartData->isEmpty())
                        <flux:text class="py-8 text-center text-zinc-500">{{ __('dashboard.no_activity_yet') }}</flux:text>
                    @endif
                </div>
            </flux:card>
        </div>
        <div class="lg:col-span-2">
            <flux:card class="overflow-hidden">
                <flux:heading size="lg" level="2" class="mb-4">{{ __('dashboard.calendar') }}</flux:heading>
                <div
                    id="dashboard-calendar"
                    class="fc p-4 min-h-[320px]"
                    data-api-url="{{ route('dashboard.calendar.events') }}"
                    data-locale="{{ str_replace('_', '-', app()->getLocale()) }}"
                    wire:ignore
                ></div>
            </flux:card>
        </div>
    </div>

    {{-- To Do block + Chat --}}
    <div class="grid gap-6 lg:grid-cols-2">
        <flux:card>
            <flux:heading size="lg" level="2" class="mb-4">{{ __('dashboard.todo_block') }}</flux:heading>
            @if($tasks->isEmpty())
                <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('dashboard.no_tasks') }}</flux:text>
            @else
                <ul class="flex flex-col gap-2">
                    @foreach($tasks as $task)
                        <li class="flex items-center justify-between gap-2 rounded-lg border border-zinc-200 px-3 py-2 dark:border-zinc-700">
                            <div class="min-w-0 flex-1">
                                <flux:text class="font-medium {{ $task->isOverdue() ? 'text-red-600 dark:text-red-400' : '' }}">{{ $task->title }}</flux:text>
                                <flux:text class="text-sm text-zinc-500">{{ $task->due_date->format('d.m.Y') }} · {{ __('dashboard.priority_' . $task->priority) }}</flux:text>
                            </div>
                            @if($task->isOverdue())
                                <flux:badge color="red" size="sm">{{ __('dashboard.overdue') }}</flux:badge>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
            <flux:button variant="ghost" size="sm" :href="route('todo.index')" wire:navigate class="mt-4">
                {{ __('dashboard.view_all_tasks') }}
            </flux:button>
        </flux:card>
        <flux:card>
            <flux:heading size="lg" level="2" class="mb-4">{{ __('dashboard.internal_chat') }}</flux:heading>
            <livewire:dashboard.dashboard-chat wire:poll.5s />
        </flux:card>
    </div>

    @if($kpis->upcomingDeparturesList->isNotEmpty())
        <flux:card>
            <flux:heading size="lg" level="2" class="mb-4">{{ __('dashboard.upcoming_departures_list') }}</flux:heading>
            <ul class="flex flex-col gap-2">
                @foreach($kpis->upcomingDeparturesList as $employee)
                    <li>
                        <flux:link :href="route('employees.show', $employee)" wire:navigate>
                            {{ $employee->person->first_name }} {{ $employee->person->last_name }}
                        </flux:link>
                        <flux:text class="text-sm text-zinc-500"> — {{ __('employee.exit_date') }}: {{ $employee->exit_date?->format('d.m.Y') }}</flux:text>
                    </li>
                @endforeach
            </ul>
        </flux:card>
    @endif
</div>

@script
<script>
    (function() {
        function tryInit() {
            if (document.getElementById('dashboard-calendar') && window.initDashboardCalendar) {
                window.initDashboardCalendar();
            }
        }
        setTimeout(tryInit, 150);
        setTimeout(tryInit, 500);
    })();
</script>
@endscript
