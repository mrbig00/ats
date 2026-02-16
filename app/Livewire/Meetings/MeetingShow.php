<?php

namespace App\Livewire\Meetings;

use App\Actions\Meetings\DeleteMeetingAction;
use App\Models\CalendarEvent;
use Livewire\Component;

class MeetingShow extends Component
{
    public CalendarEvent $event;

    public bool $showDeleteConfirm = false;

    public function mount(CalendarEvent $calendarEvent): void
    {
        $this->event = $calendarEvent->load(['candidate.person']);
        $this->authorize('view', $this->event);

        if (! in_array($this->event->type, [CalendarEvent::TYPE_INTERVIEW, CalendarEvent::TYPE_INTERNAL_MEETING], true)) {
            abort(404);
        }
    }

    public function deleteMeeting(): mixed
    {
        $this->authorize('delete', $this->event);
        app(DeleteMeetingAction::class)->handle($this->event);
        $this->showDeleteConfirm = false;
        $this->dispatch('notify', __('meeting.deleted'));

        return $this->redirect(route('meetings.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.meetings.meeting-show', [
            'event' => $this->event->load(['candidate.person']),
        ])->title($this->event->title);
    }
}
