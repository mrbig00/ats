<?php

namespace App\Livewire\Dashboard;

use App\Actions\Dashboard\GetDashboardChartDataAction;
use App\Actions\Dashboard\GetDashboardKpisAction;
use App\Actions\Dashboard\GetDashboardTasksAction;
use App\Data\Dashboard\DashboardKpisData;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Livewire\Component;

class Dashboard extends Component
{
    public string $chartPeriod = '7';

    public function mount(): void
    {
        $this->authorize('viewAny', \App\Models\CalendarEvent::class);
    }

    public function getKpis(): DashboardKpisData
    {
        return app(GetDashboardKpisAction::class)->handle(30);
    }

    /**
     * @return Collection<int, object{date: string, candidate_created: int, candidate_stage_changed: int, employee_hired: int, employee_terminated: int, meeting_scheduled: int, task_created: int}>
     */
    public function getChartData(): Collection
    {
        $days = (int) $this->chartPeriod;
        $to = CarbonImmutable::today()->endOfDay();
        $from = CarbonImmutable::today()->subDays($days)->startOfDay();

        return app(GetDashboardChartDataAction::class)->handle($from, $to);
    }

    /**
     * @return Collection<int, \App\Models\Task>
     */
    public function getTasks(): Collection
    {
        $user = auth()->user();
        if (! $user) {
            return collect();
        }

        return app(GetDashboardTasksAction::class)->handle($user->id, 15);
    }

    public function setChartPeriod(string $period): void
    {
        $this->chartPeriod = $period;
    }

    /**
     * Fake monthly data for "Hires per month / Candidate activity" chart (last 12 months).
     *
     * @return Collection<int, object{month: string, monthShort: string, hires: int, candidateActivity: int}>
     */
    public function getHiresActivityChartData(): Collection
    {
        $months = collect();
        $today = CarbonImmutable::today();
        $baseHires = [2, 1, 3, 2, 4, 3, 2, 5, 4, 3, 4, 5];
        $baseActivity = [8, 12, 10, 15, 14, 18, 16, 20, 22, 19, 24, 26];
        foreach (range(11, 0) as $i) {
            $date = $today->subMonths($i);
            $months->push((object) [
                'month' => $date->translatedFormat('F Y'),
                'monthShort' => $date->translatedFormat('M'),
                'hires' => $baseHires[11 - $i],
                'candidateActivity' => $baseActivity[11 - $i],
            ]);
        }

        return $months;
    }

    public function render()
    {
        return view('livewire.dashboard.dashboard', [
            'kpis' => $this->getKpis(),
            'chartData' => $this->getChartData(),
            'hiresActivityChartData' => $this->getHiresActivityChartData(),
            'tasks' => $this->getTasks(),
        ])
            ->title(__('nav.dashboard'))
            ->layout('layouts.app');
    }
}
