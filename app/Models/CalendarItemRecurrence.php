<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalendarItemRecurrence extends Model
{
    protected $fillable = [
        'calendar_item_id',
        'rrule',
        'timezone',
        'dtstart',
        'until',
        'count',
        'exceptions',
    ];

    protected function casts(): array
    {
        return [
            'dtstart' => 'datetime',
            'until' => 'datetime',
            'exceptions' => 'array',
        ];
    }

    public function calendarItem(): BelongsTo
    {
        return $this->belongsTo(CalendarItem::class);
    }
}
