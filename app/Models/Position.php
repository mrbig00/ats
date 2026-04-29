<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use App\Enums\PositionUrgency;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'urgency',
        'opens_at',
        'closes_at',
    ];

    protected function casts(): array
    {
        return [
            'opens_at' => 'date',
            'closes_at' => 'date',
            'urgency' => PositionUrgency::class,
        ];
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class, 'position_id');
    }

    /**
     * Postings that are still open for applications: status open and not past closes_at.
     *
     * Use this static helper (e.g. with {@see \Illuminate\Database\Eloquent\Builder::tap})
     * wherever local scope magic on the query builder is unreliable on the running stack.
     */
    public static function applyActiveRecruitmentSessionFilter(Builder $query): void
    {
        $today = CarbonImmutable::today()->toDateString();

        $query->where('status', 'open')
            ->where(function (Builder $q) use ($today) {
                $q->whereNull('closes_at')
                    ->orWhereDate('closes_at', '>=', $today);
            });
    }

    /**
     * Expired posting session: manually closed or application end date in the past.
     */
    public static function applyExpiredRecruitmentSessionFilter(Builder $query): void
    {
        $today = CarbonImmutable::today()->toDateString();

        $query->where(function (Builder $q) use ($today) {
            $q->where('status', 'closed')
                ->orWhere(function (Builder $q2) use ($today) {
                    $q2->whereNotNull('closes_at')
                        ->whereDate('closes_at', '<', $today);
                });
        });
    }

    public function scopeActiveRecruitmentSession(Builder $query): void
    {
        static::applyActiveRecruitmentSessionFilter($query);
    }

    public function scopeExpiredRecruitmentSession(Builder $query): void
    {
        static::applyExpiredRecruitmentSessionFilter($query);
    }

    public function hasExpiredRecruitmentSession(): bool
    {
        if ($this->status === 'closed') {
            return true;
        }

        if ($this->closes_at === null) {
            return false;
        }

        return $this->closes_at->toDateString() < CarbonImmutable::today()->toDateString();
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function statusLabel(): string
    {
        return $this->status === 'open' ? __('job.status_open') : __('job.status_closed');
    }

    public function openForDays(): int
    {
        $start = $this->opens_at !== null
            ? CarbonImmutable::instance($this->opens_at)->startOfDay()
            : CarbonImmutable::instance($this->created_at)->startOfDay();

        $end = $this->hasExpiredRecruitmentSession()
            ? ($this->closes_at !== null
                ? CarbonImmutable::instance($this->closes_at)->startOfDay()
                : CarbonImmutable::instance($this->updated_at)->startOfDay())
            : CarbonImmutable::today()->startOfDay();

        if ($end->lessThan($start)) {
            return 0;
        }

        return $start->diffInDays($end);
    }

    public function closesInDays(): ?int
    {
        if ($this->closes_at === null) {
            return null;
        }

        $today = CarbonImmutable::today()->startOfDay();
        $closesAt = CarbonImmutable::instance($this->closes_at)->startOfDay();

        return $today->diffInDays($closesAt, false);
    }

    public function closesInDaysForDisplay(): ?int
    {
        $days = $this->closesInDays();
        if ($days === null) {
            return null;
        }

        return max(0, $days);
    }
}
