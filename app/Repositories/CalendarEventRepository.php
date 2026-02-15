<?php

namespace App\Repositories;

use App\Data\Candidates\InterviewData;
use App\Models\CalendarEvent;

class CalendarEventRepository
{
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

    public function find(int $id): ?CalendarEvent
    {
        return CalendarEvent::query()->with('candidate')->find($id);
    }
}
