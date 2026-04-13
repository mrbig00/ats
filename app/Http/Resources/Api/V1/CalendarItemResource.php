<?php

namespace App\Http\Resources\Api\V1;

use App\Data\Calendar\CalendarItemOccurrenceData;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin CalendarItemOccurrenceData */
class CalendarItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var CalendarItemOccurrenceData $data */
        $data = $this->resource;

        return [
            'id' => $data->id,
            'title' => $data->title,
            'start' => $data->start->toIso8601String(),
            'end' => $data->end?->toIso8601String(),
            'allDay' => $data->allDay,
            'extendedProps' => $data->extendedProps,
        ];
    }
}
