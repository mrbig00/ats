<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class CandidateDocument extends Model
{
    protected $fillable = [
        'candidate_id',
        'name',
        'path',
        'mime_type',
        'size',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
        ];
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function getStoragePath(): string
    {
        return $this->path;
    }

    public function delete(): ?bool
    {
        Storage::disk('local')->delete($this->path);

        return parent::delete();
    }
}
