<?php

namespace App\Actions\Meetings;

use App\Data\Meetings\MeetingFilterData;
use App\Repositories\CalendarEventRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListMeetingsAction
{
    public function __construct(
        private CalendarEventRepository $calendarEventRepository,
    ) {}

    /**
     * @return LengthAwarePaginator<\App\Models\CalendarEvent>
     */
    public function handle(MeetingFilterData $filters): LengthAwarePaginator
    {
        return $this->calendarEventRepository->paginateMeetings($filters);
    }
}
