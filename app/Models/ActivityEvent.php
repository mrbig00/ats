<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityEvent extends Model
{
    public const TYPE_CANDIDATE_CREATED = 'candidate_created';

    public const TYPE_CANDIDATE_STAGE_CHANGED = 'candidate_stage_changed';

    public const TYPE_EMPLOYEE_HIRED = 'employee_hired';

    public const TYPE_EMPLOYEE_TERMINATED = 'employee_terminated';

    public const TYPE_MEETING_SCHEDULED = 'meeting_scheduled';

    public const TYPE_TASK_CREATED = 'task_created';

    protected $fillable = [
        'type',
        'subject_type',
        'subject_id',
        'occurred_at',
        'meta',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
