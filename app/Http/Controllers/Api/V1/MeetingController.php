<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Meetings\CreateInternalMeetingAction;
use App\Actions\Meetings\DeleteMeetingAction;
use App\Actions\Meetings\ListMeetingsAction;
use App\Actions\Meetings\UpdateMeetingAction;
use App\Data\Meetings\MeetingFilterData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreMeetingRequest;
use App\Http\Requests\Api\V1\UpdateMeetingRequest;
use App\Http\Resources\Api\V1\MeetingResource;
use App\Models\CalendarEvent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;

class MeetingController extends Controller
{
    public function __construct(
        private ListMeetingsAction $listMeetingsAction,
        private CreateInternalMeetingAction $createMeetingAction,
        private UpdateMeetingAction $updateMeetingAction,
        private DeleteMeetingAction $deleteMeetingAction,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = new MeetingFilterData(
            type: $request->input('type'),
            search: $request->input('search'),
            sortField: $request->input('sort_field', 'starts_at'),
            sortDirection: $request->input('sort_direction', 'asc'),
            perPage: (int) $request->input('per_page', 15),
        );

        $paginator = $this->listMeetingsAction->handle($filters);

        return MeetingResource::collection($paginator);
    }

    public function show(CalendarEvent $meeting): MeetingResource
    {
        if (! $this->isMeetingType($meeting)) {
            abort(404);
        }

        $this->authorize('view', $meeting);

        return new MeetingResource($meeting->load('candidate'));
    }

    public function store(StoreMeetingRequest $request): MeetingResource
    {
        $event = $this->createMeetingAction->handle($request->toMeetingData());

        return new MeetingResource($event->load('candidate'));
    }

    public function update(UpdateMeetingRequest $request, CalendarEvent $meeting): MeetingResource
    {
        if (! $this->isMeetingType($meeting)) {
            abort(404);
        }

        $event = $this->updateMeetingAction->handle($meeting, $request->toMeetingData());

        return new MeetingResource($event);
    }

    public function destroy(CalendarEvent $meeting): JsonResponse
    {
        if (! $this->isMeetingType($meeting)) {
            abort(404);
        }

        $this->authorize('delete', $meeting);

        $this->deleteMeetingAction->handle($meeting);

        return response()->json(null, 204);
    }

    private function isMeetingType(CalendarEvent $event): bool
    {
        return in_array($event->type, [
            CalendarEvent::TYPE_INTERNAL_MEETING,
            CalendarEvent::TYPE_INTERVIEW,
        ], true);
    }
}
