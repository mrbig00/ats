<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'person_id',
        'position_id',
        'pipeline_stage_id',
        'source',
        'applied_at',
    ];

    protected function casts(): array
    {
        return [
            'applied_at' => 'datetime',
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
}
