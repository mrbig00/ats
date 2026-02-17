<?php

namespace App\Livewire\Meetings;

use App\Actions\Meetings\UpdateMeetingAction;
use App\Data\Meetings\MeetingData;
use App\Models\CalendarEvent;
use Carbon\CarbonImmutable;
use Livewire\Component;

class EditMeeting extends Component
{
    public CalendarEvent $event;

    public string $title = '';

    public string $startsAt = '';

    public string $endsAt = '';

    public string $notes = '';

    public function mount(CalendarEvent $calendarEvent): void
    {
        $this->event = $calendarEvent;
        $this->authorize('update', $calendarEvent);

        if (! in_array($calendarEvent->type, [CalendarEvent::TYPE_INTERVIEW, CalendarEvent::TYPE_INTERNAL_MEETING], true)) {
            abort(404);
        }

        $this->title = $calendarEvent->title;
        $this->startsAt = $calendarEvent->starts_at->format('Y-m-d\TH:i');
        $this->endsAt = $calendarEvent->ends_at?->format('Y-m-d\TH:i') ?? '';
        $this->notes = $calendarEvent->notes ?? '';
    }

    public function save(): mixed
    {
        $this->authorize('update', $this->event);

        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'startsAt' => ['required', 'date'],
            'endsAt' => ['nullable', 'date', 'after:startsAt'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ], [], [
            'title' => __('meeting.title'),
            'startsAt' => __('meeting.starts_at'),
            'endsAt' => __('meeting.ends_at'),
            'notes' => __('meeting.notes'),
        ]);

        $data = new MeetingData(
            title: $validated['title'],
            startsAt: CarbonImmutable::parse($validated['startsAt']),
            endsAt: ! empty($validated['endsAt']) ? CarbonImmutable::parse($validated['endsAt']) : null,
            notes: ! empty($validated['notes']) ? $validated['notes'] : null,
        );

        app(UpdateMeetingAction::class)->handle($this->event, $data);

        $this->dispatch('notify', __('meeting.updated'));

        return $this->redirect(route('meetings.calendar'), navigate: true);
    }

    public function render()
    {
        return view('livewire.meetings.edit-meeting')->title(__('meeting.edit'));
    }
}
