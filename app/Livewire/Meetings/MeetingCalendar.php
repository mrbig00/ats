<?php

namespace App\Livewire\Meetings;

use App\Repositories\CalendarEventRepository;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Livewire\Component;

class MeetingCalendar extends Component
{
    public int $year;

    public int $month;

    public function mount(): void
    {
        $this->authorize('viewAny', \App\Models\CalendarEvent::class);
        $now = CarbonImmutable::now();
        $this->year = (int) $now->format('Y');
        $this->month = (int) $now->format('n');
    }

    public function previousMonth(): void
    {
        $date = CarbonImmutable::createFromDate($this->year, $this->month, 1)->subMonth();
        $this->year = (int) $date->format('Y');
        $this->month = (int) $date->format('n');
    }

    public function nextMonth(): void
    {
        $date = CarbonImmutable::createFromDate($this->year, $this->month, 1)->addMonth();
        $this->year = (int) $date->format('Y');
        $this->month = (int) $date->format('n');
    }

    public function getMonthLabelProperty(): string
    {
        return CarbonImmutable::createFromDate($this->year, $this->month, 1)->isoFormat('MMMM YYYY');
    }

    /**
     * @return Collection<string, \Illuminate\Support\Collection<int, \App\Models\CalendarEvent>>
     */
    public function getEventsByDateProperty(): Collection
    {
        $start = CarbonImmutable::createFromDate($this->year, $this->month, 1)->startOfMonth();
        $end = $start->endOfMonth();

        $events = app(CalendarEventRepository::class)->getEventsForCalendar($start, $end);

        return $events->groupBy(fn ($e) => $e->starts_at->toDateString());
    }

    public function render()
    {
        return view('livewire.meetings.meeting-calendar', [
            'monthLabel' => $this->monthLabel,
            'eventsByDate' => $this->eventsByDate,
        ])->title(__('nav.meetings'));
    }
}
