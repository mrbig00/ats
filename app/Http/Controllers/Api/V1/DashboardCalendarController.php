<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Dashboard\GetDashboardCalendarEventsAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\DashboardCalendarResource;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DashboardCalendarController extends Controller
{
    public function __construct(
        private GetDashboardCalendarEventsAction $getDashboardCalendarEventsAction,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'start' => ['required', 'date'],
            'end' => ['required', 'date', 'after_or_equal:start'],
        ]);

        $this->authorize('viewAny', \App\Models\CalendarEvent::class);

        $start = CarbonImmutable::parse($request->input('start'))->startOfDay();
        $end = CarbonImmutable::parse($request->input('end'))->endOfDay();

        $events = $this->getDashboardCalendarEventsAction->handle($start, $end);

        return DashboardCalendarResource::collection($events);
    }
}
