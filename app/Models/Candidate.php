<?php

namespace App\Models;

use App\Enums\GermanLanguageLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\HasActivity;
use Spatie\Activitylog\Support\LogOptions;

class Candidate extends Model
{
    use HasActivity;
    use HasFactory;

    protected $fillable = [
        'person_id',
        'position_id',
        'pipeline_stage_id',
        'source',
        'applied_at',
        'nationality',
        'driving_license_category',
        'has_own_car',
        'german_level',
        'available_from',
        'housing_needed',
    ];

    protected function casts(): array
    {
        return [
            'applied_at' => 'datetime',
            'has_own_car' => 'boolean',
            'housing_needed' => 'boolean',
            'available_from' => 'date',
            'german_level' => GermanLanguageLevel::class,
        ];
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function pipelineStage(): BelongsTo
    {
        return $this->belongsTo(PipelineStage::class, 'pipeline_stage_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(CandidateNote::class, 'candidate_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(CandidateDocument::class, 'candidate_id');
    }

    public function calendarEvents(): HasMany
    {
        return $this->hasMany(CalendarEvent::class, 'candidate_id');
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(CalendarEvent::class, 'candidate_id')->where('type', CalendarEvent::TYPE_INTERVIEW);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('candidate')
            ->setDescriptionForEvent(fn (string $eventName) => match ($eventName) {
                'created' => 'candidate.activity.created',
                'updated' => 'candidate.activity.updated',
                default => 'candidate.activity.'.$eventName,
            });
    }
}
