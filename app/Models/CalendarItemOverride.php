<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalendarItemOverride extends Model
{
    protected $fillable = [
        'calendar_item_id',
        'occurrence_date',
        'start_at',
        'end_at',
        'title',
        'description',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'occurrence_date' => 'datetime',
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    public function calendarItem(): BelongsTo
    {
        return $this->belongsTo(CalendarItem::class);
    }
}
