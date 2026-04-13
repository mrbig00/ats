<?php

namespace App\Models;

use Carbon\CarbonImmutable;
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
        'opens_at',
        'closes_at',
    ];

    protected function casts(): array
    {
        return [
            'opens_at' => 'date',
            'closes_at' => 'date',
        ];
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class, 'position_id');
    }

    /**
     * Postings that are still open for applications: status open and not past closes_at.
     */
    public function scopeActiveRecruitmentSession(Builder $query): void
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
    public function scopeExpiredRecruitmentSession(Builder $query): void
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
}
