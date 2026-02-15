<?php

namespace App\Livewire\Candidates;

use App\Actions\Candidates\AddCandidateNoteAction;
use App\Actions\Candidates\ScheduleInterviewAction;
use App\Data\Candidates\CandidateNoteData;
use App\Data\Candidates\InterviewData;
use App\Data\Candidates\UpdateCandidateStageData;
use App\Actions\Candidates\UpdateCandidateStageAction;
use App\Models\Candidate;
use App\Repositories\CandidateRepository;
use App\Repositories\PipelineStageRepository;
use App\Services\HireCandidateWorkflowService;
use App\Data\Candidates\ConvertCandidateToEmployeeData;
use Carbon\CarbonImmutable;
use App\Repositories\CandidateDocumentRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class CandidateShow extends Component
{
    use WithFileUploads;

    public Candidate $candidate;

    public $documentFile = null;

    public string $documentName = '';

    public string $noteContent = '';

    public bool $showConvertModal = false;

    public string $convertEntryDate = '';

    public ?int $newStageId = null;

    public bool $showInterviewModal = false;

    public string $interviewTitle = '';

    public string $interviewStartsAt = '';

    public string $interviewEndsAt = '';

    public string $interviewNotes = '';

    public function mount(Candidate $candidate): void
    {
        $this->authorize('view', $candidate);
        $this->candidate = $candidate->load(['person.employee', 'position', 'pipelineStage', 'notes.user', 'documents', 'interviews']);
        $this->newStageId = $candidate->pipeline_stage_id;
        $this->convertEntryDate = now()->format('Y-m-d');
    }

    public function addNote(): void
    {
        $this->validate([
            'noteContent' => ['required', 'string', 'max:10000'],
        ], [], ['noteContent' => __('candidate.note_content')]);

        app(AddCandidateNoteAction::class)->handle(new CandidateNoteData(
            candidateId: $this->candidate->id,
            userId: Auth::id(),
            content: $this->noteContent,
        ));

        $this->noteContent = '';
        $this->candidate->load(['notes.user']);
        $this->dispatch('notify', __('candidate.note_added'));
    }

    public function updateStage(): void
    {
        $this->validate([
            'newStageId' => ['required', 'integer', 'exists:pipeline_stages,id'],
        ]);

        app(UpdateCandidateStageAction::class)->handle(new UpdateCandidateStageData(
            candidateId: $this->candidate->id,
            pipelineStageId: $this->newStageId,
        ));

        $this->candidate->refresh();
        $this->candidate->load(['pipelineStage']);
        $this->dispatch('notify', __('candidate.stage_updated'));
    }

    public function openConvertModal(): void
    {
        $this->authorize('convertToEmployee', $this->candidate);
        $this->showConvertModal = true;
        $this->convertEntryDate = now()->format('Y-m-d');
    }

    public function convertToEmployee(): mixed
    {
        $this->authorize('convertToEmployee', $this->candidate);
        $this->validate([
            'convertEntryDate' => ['required', 'date'],
        ]);

        $employee = app(HireCandidateWorkflowService::class)->handle(new ConvertCandidateToEmployeeData(
            candidateId: $this->candidate->id,
            entryDate: CarbonImmutable::parse($this->convertEntryDate),
        ));

        $this->showConvertModal = false;
        $this->dispatch('notify', __('candidate.converted_to_employee'));
        return $this->redirect(route('employees.index'), navigate: true);
    }

    public function openInterviewModal(): void
    {
        $this->showInterviewModal = true;
        $this->interviewTitle = __('meeting.interview_with', ['name' => $this->candidate->person->fullName()]);
        $this->interviewStartsAt = now()->addDay()->format('Y-m-d\TH:i');
        $this->interviewEndsAt = now()->addDay()->addHour()->format('Y-m-d\TH:i');
        $this->interviewNotes = '';
    }

    public function scheduleInterview(): void
    {
        $this->validate([
            'interviewTitle' => ['required', 'string', 'max:255'],
            'interviewStartsAt' => ['required', 'date'],
            'interviewEndsAt' => ['nullable', 'date', 'after:interviewStartsAt'],
            'interviewNotes' => ['nullable', 'string', 'max:2000'],
        ]);

        app(ScheduleInterviewAction::class)->handle(new InterviewData(
            candidateId: $this->candidate->id,
            title: $this->interviewTitle,
            startsAt: CarbonImmutable::parse($this->interviewStartsAt),
            endsAt: $this->interviewEndsAt ? CarbonImmutable::parse($this->interviewEndsAt) : null,
            notes: $this->interviewNotes ?: null,
        ));

        $this->showInterviewModal = false;
        $this->candidate->load(['interviews']);
        $this->dispatch('notify', __('candidate.interview_scheduled'));
    }

    public function uploadDocument(): void
    {
        $this->validate([
            'documentFile' => ['required', 'file', 'max:10240', 'mimes:pdf,doc,docx,txt,jpg,jpeg,png'],
            'documentName' => ['required', 'string', 'max:255'],
        ], [], [
            'documentFile' => __('candidate.document'),
            'documentName' => __('candidate.document_name'),
        ]);

        $path = $this->documentFile->store('candidate-documents/'.$this->candidate->id, 'local');
        app(CandidateDocumentRepository::class)->create(
            candidateId: $this->candidate->id,
            name: $this->documentName,
            path: $path,
            mimeType: $this->documentFile->getMimeType(),
            size: $this->documentFile->getSize(),
        );

        $this->reset(['documentFile', 'documentName']);
        $this->candidate->load(['documents']);
        $this->dispatch('notify', __('candidate.document_uploaded'));
    }

    public function deleteDocument(int $documentId): void
    {
        $document = $this->candidate->documents->firstWhere('id', $documentId);
        if ($document) {
            app(CandidateDocumentRepository::class)->delete($document);
            $this->candidate->load(['documents']);
            $this->dispatch('notify', __('candidate.document_deleted'));
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\PipelineStage>
     */
    public function getPipelineStagesProperty(): \Illuminate\Database\Eloquent\Collection
    {
        return app(PipelineStageRepository::class)->allOrdered();
    }

    public function render()
    {
        return view('livewire.candidates.candidate-show', [
            'pipelineStages' => $this->pipelineStages,
        ])->title($this->candidate->person->fullName());
    }
}
