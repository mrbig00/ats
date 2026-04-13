<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalendarItemException extends Model
{
    protected $fillable = [
        'calendar_item_id',
        'exception_date',
    ];

    protected function casts(): array
    {
        return [
            'exception_date' => 'datetime',
        ];
    }

    public function calendarItem(): BelongsTo
    {
        return $this->belongsTo(CalendarItem::class);
    }
}
