<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Calendar\GetCalendarItemsAction;
use App\Data\Calendar\CalendarItemFilterData;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CalendarItemResource;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CalendarItemsController extends Controller
{
    public function __construct(
        private GetCalendarItemsAction $getCalendarItemsAction,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'start' => ['required', 'date'],
            'end' => ['required', 'date', 'after_or_equal:start'],
            'type' => ['sometimes', 'array'],
            'type.*' => ['string', 'in:meeting,task,event'],
        ]);

        $this->authorize('viewAny', \App\Models\CalendarItem::class);

        $start = CarbonImmutable::parse($request->input('start'))->startOfDay();
        $end = CarbonImmutable::parse($request->input('end'))->endOfDay();
        $types = $request->input('type');

        $filter = new CalendarItemFilterData($start, $end, $types);
        $occurrences = $this->getCalendarItemsAction->handle($filter);

        return CalendarItemResource::collection($occurrences->values());
    }
}
