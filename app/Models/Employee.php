<?php

namespace App\Models;

use App\Enums\GermanLanguageLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_LEAVING = 'leaving';

    public const STATUS_LEFT = 'left';

    protected $fillable = [
        'person_id',
        'status',
        'entry_date',
        'exit_date',
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
            'entry_date' => 'date',
            'exit_date' => 'date',
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

    public function occupancies(): HasMany
    {
        return $this->hasMany(Occupancy::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }
}
