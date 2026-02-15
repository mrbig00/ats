<?php

namespace App\Repositories;

use App\Data\Candidates\CandidateNoteData;
use App\Models\CandidateNote;

class CandidateNoteRepository
{
    public function create(CandidateNoteData $data): CandidateNote
    {
        return CandidateNote::query()->create([
            'candidate_id' => $data->candidateId,
            'user_id' => $data->userId,
            'content' => $data->content,
        ]);
    }

    public function find(int $id): ?CandidateNote
    {
        return CandidateNote::query()->with(['user'])->find($id);
    }

    public function delete(CandidateNote $note): void
    {
        $note->delete();
    }
}
