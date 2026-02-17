<?php

namespace App\Livewire\Meetings;

use App\Actions\Meetings\CreateInternalMeetingAction;
use App\Data\Meetings\MeetingData;
use Carbon\CarbonImmutable;
use Livewire\Component;

class CreateMeeting extends Component
{
    public string $title = '';

    public string $startsAt = '';

    public string $endsAt = '';

    public string $notes = '';

    public function mount(): void
    {
        $this->authorize('create', \App\Models\CalendarEvent::class);
        $startsAtQuery = request()->query('starts_at');
        $endsAtQuery = request()->query('ends_at');
        if ($startsAtQuery && $endsAtQuery) {
            $this->startsAt = CarbonImmutable::parse($startsAtQuery)->format('Y-m-d\TH:i');
            $this->endsAt = CarbonImmutable::parse($endsAtQuery)->format('Y-m-d\TH:i');
        } else {
            $defaultStart = now()->addDay()->setHour(10)->setMinute(0)->setSecond(0);
            $this->startsAt = $defaultStart->format('Y-m-d\TH:i');
            $this->endsAt = $defaultStart->addHour()->format('Y-m-d\TH:i');
        }
    }

    public function save(): mixed
    {
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

        $event = app(CreateInternalMeetingAction::class)->handle($data);

        $this->dispatch('notify', __('meeting.created'));

        return $this->redirect(route('meetings.calendar'), navigate: true);
    }

    public function render()
    {
        return view('livewire.meetings.create-meeting')->title(__('meeting.create'));
    }
}
