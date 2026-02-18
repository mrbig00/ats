<?php

namespace App\Http\Resources\Api\V1;

use App\Models\CalendarEvent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin CalendarEvent */
class DashboardCalendarResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'title' => $this->title,
            'notes' => $this->notes,
            'starts_at' => $this->starts_at?->toIso8601String(),
            'ends_at' => $this->ends_at?->toIso8601String(),
            'candidate_id' => $this->candidate_id,
            'room_id' => $this->room_id,
            'url' => $this->eventUrl(),
        ];
    }

    private function eventUrl(): ?string
    {
        return match ($this->type) {
            CalendarEvent::TYPE_INTERVIEW, CalendarEvent::TYPE_INTERNAL_MEETING => route('meetings.show', $this->id),
            CalendarEvent::TYPE_ROOM_FREE => $this->room_id ? route('housing.apartments.show', $this->room?->apartment_id) : null,
            default => null,
        };
    }
}
