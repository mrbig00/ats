<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CalendarItem extends Model
{
    use SoftDeletes;

    public const TYPE_MEETING = 'meeting';

    public const TYPE_TASK = 'task';

    public const TYPE_EVENT = 'event';

    protected $fillable = [
        'type',
        'title',
        'description',
        'start_at',
        'end_at',
        'all_day',
        'status',
        'owner_id',
        'color',
        'calendar_itemable_type',
        'calendar_itemable_id',
    ];

    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'all_day' => 'boolean',
        ];
    }

    public function calendarItemable(): MorphTo
    {
        return $this->morphTo();
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function recurrence(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(CalendarItemRecurrence::class);
    }

    public function exceptions(): HasMany
    {
        return $this->hasMany(CalendarItemException::class);
    }

    public function overrides(): HasMany
    {
        return $this->hasMany(CalendarItemOverride::class);
    }
}
