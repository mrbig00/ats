<?php

namespace App\Http\Requests\Api\V1;

use App\Data\Meetings\MeetingData;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;

class StoreMeetingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\CalendarEvent::class);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function toMeetingData(): MeetingData
    {
        $validated = $this->validated();

        return new MeetingData(
            title: $validated['title'],
            startsAt: CarbonImmutable::parse($validated['starts_at']),
            endsAt: isset($validated['ends_at']) ? CarbonImmutable::parse($validated['ends_at']) : null,
            notes: $validated['notes'] ?? null,
        );
    }
}
