<?php

namespace App\Livewire\Meetings;

use Livewire\Component;

class MeetingCalendar extends Component
{
    public function mount(): void
    {
        $this->authorize('viewAny', \App\Models\CalendarEvent::class);
    }

    public function render()
    {
        return view('livewire.meetings.meeting-calendar')->title(__('meeting.view_calendar'));
    }
}
