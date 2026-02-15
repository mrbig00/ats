<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalendarEvent extends Model
{
    public const TYPE_INTERVIEW = 'interview';

    public const TYPE_INTERNAL_MEETING = 'internal_meeting';

    public const TYPE_ENTRY_DATE = 'entry_date';

    public const TYPE_EXIT_DATE = 'exit_date';

    public const TYPE_ROOM_FREE = 'room_free';

    protected $fillable = [
        'type',
        'title',
        'notes',
        'starts_at',
        'ends_at',
        'candidate_id',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function isInterview(): bool
    {
        return $this->type === self::TYPE_INTERVIEW;
    }
}
