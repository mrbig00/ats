<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Occupancy extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'employee_id',
        'starts_at',
        'ends_at',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'date',
            'ends_at' => 'date',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function isActive(): bool
    {
        return $this->ends_at === null;
    }

    /**
     * Check if this occupancy overlaps with a date range (for validation).
     */
    public function overlaps(CarbonImmutable $startsAt, ?CarbonImmutable $endsAt): bool
    {
        $occupancyStart = CarbonImmutable::parse($this->starts_at);
        $occupancyEnd = $this->ends_at !== null ? CarbonImmutable::parse($this->ends_at) : null;

        if ($endsAt === null && $occupancyEnd === null) {
            return true;
        }
        if ($endsAt === null) {
            return $startsAt->lte($occupancyEnd);
        }
        if ($occupancyEnd === null) {
            return $endsAt->gte($occupancyStart);
        }

        return $startsAt->lte($occupancyEnd) && $endsAt->gte($occupancyStart);
    }
}
