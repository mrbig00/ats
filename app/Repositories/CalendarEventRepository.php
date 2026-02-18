<?php

namespace App\Repositories;

use App\Data\Candidates\InterviewData;
use App\Data\Meetings\MeetingData;
use App\Data\Meetings\MeetingFilterData;
use App\Models\CalendarEvent;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class CalendarEventRepository
{
    public function createInternalMeeting(MeetingData $data): CalendarEvent
    {
        return CalendarEvent::query()->create([
            'type' => CalendarEvent::TYPE_INTERNAL_MEETING,
            'title' => $data->title,
            'notes' => $data->notes,
            'starts_at' => $data->startsAt->toDateTimeString(),
            'ends_at' => $data->endsAt?->toDateTimeString(),
            'candidate_id' => null,
        ]);
    }

    public function update(CalendarEvent $event, MeetingData $data): CalendarEvent
    {
        $event->update([
            'title' => $data->title,
            'notes' => $data->notes,
            'starts_at' => $data->startsAt->toDateTimeString(),
            'ends_at' => $data->endsAt?->toDateTimeString(),
        ]);

        return $event->fresh(['candidate']);
    }

    public function delete(CalendarEvent $event): void
    {
        $event->delete();
    }

    /**
     * @return LengthAwarePaginator<CalendarEvent>
     */
    public function paginateMeetings(MeetingFilterData $filters): LengthAwarePaginator
    {
        $query = CalendarEvent::query()
            ->whereIn('type', [CalendarEvent::TYPE_INTERNAL_MEETING, CalendarEvent::TYPE_INTERVIEW])
            ->with('candidate.person');

        if ($filters->type !== null && $filters->type !== '') {
            $query->where('type', $filters->type);
        }

        if ($filters->search !== null && $filters->search !== '') {
            $search = '%'.addcslashes($filters->search, '%_').'%';
            $query->where(function (Builder $q) use ($search) {
                $q->where('title', 'ilike', $search)
                    ->orWhere('notes', 'ilike', $search)
                    ->orWhereHas('candidate.person', function (Builder $personQ) use ($search) {
                        $personQ->where('first_name', 'ilike', $search)
                            ->orWhere('last_name', 'ilike', $search);
                    });
            });
        }

        $direction = strtolower($filters->sortDirection) === 'desc' ? 'desc' : 'asc';
        $query->orderBy($this->sortFieldColumn($filters->sortField), $direction);

        return $query->paginate($filters->perPage);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, CalendarEvent>
     */
    public function getEventsForCalendar(CarbonImmutable $start, CarbonImmutable $end): \Illuminate\Database\Eloquent\Collection
    {
        return CalendarEvent::query()
            ->whereIn('type', [CalendarEvent::TYPE_INTERNAL_MEETING, CalendarEvent::TYPE_INTERVIEW])
            ->where('starts_at', '<', $end)
            ->where(function (Builder $q) use ($start) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>', $start);
            })
            ->with('candidate.person')
            ->orderBy('starts_at')
            ->get();
    }

    /**
     * All event types for dashboard calendar (interviews, meetings, entry/exit dates, room free).
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, CalendarEvent>
     */
    public function getEventsForDashboardCalendar(CarbonImmutable $start, CarbonImmutable $end): \Illuminate\Database\Eloquent\Collection
    {
        return CalendarEvent::query()
            ->where('starts_at', '<', $end)
            ->where(function (Builder $q) use ($start) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>', $start);
            })
            ->with(['candidate.person', 'room.apartment'])
            ->orderBy('starts_at')
            ->get();
    }

    private function sortFieldColumn(string $field): string
    {
        return match ($field) {
            'title' => 'title',
            'type' => 'type',
            'starts_at' => 'starts_at',
            'ends_at' => 'ends_at',
            default => 'starts_at',
        };
    }

    public function createInterview(InterviewData $data): CalendarEvent
    {
        return CalendarEvent::query()->create([
            'type' => CalendarEvent::TYPE_INTERVIEW,
            'title' => $data->title,
            'notes' => $data->notes,
            'starts_at' => $data->startsAt->toDateTimeString(),
            'ends_at' => $data->endsAt?->toDateTimeString(),
            'candidate_id' => $data->candidateId,
        ]);
    }

    public function createEntryDateEvent(string $title, \Carbon\CarbonImmutable $entryDate): CalendarEvent
    {
        return CalendarEvent::query()->create([
            'type' => CalendarEvent::TYPE_ENTRY_DATE,
            'title' => $title,
            'starts_at' => $entryDate->startOfDay()->toDateTimeString(),
            'ends_at' => null,
            'candidate_id' => null,
        ]);
    }

    public function createRoomFreeEvent(string $title, int $roomId, CarbonImmutable $startsAt): CalendarEvent
    {
        return CalendarEvent::query()->create([
            'type' => CalendarEvent::TYPE_ROOM_FREE,
            'title' => $title,
            'notes' => null,
            'starts_at' => $startsAt->startOfDay()->toDateTimeString(),
            'ends_at' => null,
            'candidate_id' => null,
            'room_id' => $roomId,
        ]);
    }

    public function createExitDateEvent(string $title, CarbonImmutable $exitDate): CalendarEvent
    {
        return CalendarEvent::query()->create([
            'type' => CalendarEvent::TYPE_EXIT_DATE,
            'title' => $title,
            'notes' => null,
            'starts_at' => $exitDate->startOfDay()->toDateTimeString(),
            'ends_at' => null,
            'candidate_id' => null,
            'room_id' => null,
        ]);
    }

    public function find(int $id): ?CalendarEvent
    {
        return CalendarEvent::query()->with('candidate')->find($id);
    }

    /**
     * Count interviews (calendar events type interview) in the given period.
     */
    public function countInterviewsInPeriod(CarbonImmutable $start, CarbonImmutable $end): int
    {
        return CalendarEvent::query()
            ->where('type', CalendarEvent::TYPE_INTERVIEW)
            ->where('starts_at', '>=', $start)
            ->where('starts_at', '<=', $end)
            ->count();
    }
}
