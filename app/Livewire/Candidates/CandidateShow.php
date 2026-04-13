<?php

namespace App\Livewire\Candidates;

use App\Actions\Candidates\AddCandidateNoteAction;
use App\Actions\Candidates\ScheduleInterviewAction;
use App\Actions\Candidates\UpdateCandidateProfileAction;
use App\Data\Candidates\CandidateNoteData;
use App\Data\Candidates\UpdateCandidateProfileData;
use App\Data\Candidates\InterviewData;
use App\Data\Candidates\UpdateCandidateStageData;
use App\Actions\Candidates\UpdateCandidateStageAction;
use App\Models\Candidate;
use App\Repositories\CandidateRepository;
use App\Repositories\PipelineStageRepository;
use App\Services\HireCandidateWorkflowService;
use App\Data\Candidates\ConvertCandidateToEmployeeData;
use Carbon\CarbonImmutable;
use App\Enums\GermanLanguageLevel;
use App\Repositories\CandidateDocumentRepository;
use App\Support\CandidateActivityPresentation;
use App\Support\CandidatePipelineStageActivity;
use App\Support\CandidateProfileValidationRules;
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

    public ?string $stageSetByLine = null;

    public bool $showChangeHistoryModal = false;

    public bool $showInterviewModal = false;

    public string $interviewTitle = '';

    public string $interviewStartsAt = '';

    public string $interviewEndsAt = '';

    public string $interviewNotes = '';

    public string $nationality = '';

    public string $driving_license_category = '';

    public string $has_own_car = '';

    public string $german_level = '';

    public ?string $available_from = null;

    public string $housing_needed = '';

    public function mount(Candidate $candidate): void
    {
        $this->authorize('view', $candidate);
        $this->candidate = $candidate->load([
            'person.employee',
            'position',
            'pipelineStage',
            'notes.user',
            'documents',
            'interviews',
            'activities' => fn ($q) => $q->with('causer')->latest(),
        ]);
        $this->newStageId = $candidate->pipeline_stage_id;
        $this->syncStageAuditMetadata();
        $this->convertEntryDate = now()->format('Y-m-d');
        $this->syncProfileFieldsFromCandidate();
    }

    public function saveProfile(): void
    {
        $this->authorize('update', $this->candidate);

        $validated = $this->validate(
            CandidateProfileValidationRules::livewireOptionalProfileRules(),
            [],
            CandidateProfileValidationRules::attributeNames(),
        );

        $attributes = [
            'nationality' => ($validated['nationality'] ?? '') !== '' ? $validated['nationality'] : null,
            'driving_license_category' => ($validated['driving_license_category'] ?? '') !== '' ? $validated['driving_license_category'] : null,
            'has_own_car' => $this->triStateToBool($validated['has_own_car'] ?? ''),
            'german_level' => ($validated['german_level'] ?? '') !== '' ? $validated['german_level'] : null,
            'available_from' => ($validated['available_from'] ?? '') !== '' ? $validated['available_from'] : null,
            'housing_needed' => $this->triStateToBool($validated['housing_needed'] ?? ''),
        ];

        app(UpdateCandidateProfileAction::class)->handle(
            $this->candidate,
            new UpdateCandidateProfileData($attributes),
        );

        $this->candidate->refresh()->load([
            'person.employee',
            'position',
            'pipelineStage',
            'notes.user',
            'documents',
            'interviews',
            'activities' => fn ($q) => $q->with('causer')->latest(),
        ]);
        $this->syncStageAuditMetadata();
        $this->syncProfileFieldsFromCandidate();
        $this->dispatch('notify', __('candidate.profile_updated'));
    }

    private function syncProfileFieldsFromCandidate(): void
    {
        $c = $this->candidate;
        $this->nationality = $c->nationality ?? '';
        $this->driving_license_category = $c->driving_license_category ?? '';
        $this->has_own_car = match ($c->has_own_car) {
            true => '1',
            false => '0',
            default => '',
        };
        $this->german_level = $c->german_level?->value ?? '';
        $this->available_from = $c->available_from?->format('Y-m-d');
        $this->housing_needed = match ($c->housing_needed) {
            true => '1',
            false => '0',
            default => '',
        };
    }

    private function triStateToBool(string $value): ?bool
    {
        return match ($value) {
            '1' => true,
            '0' => false,
            default => null,
        };
    }

    private function syncStageAuditMetadata(): void
    {
        $setter = CandidatePipelineStageActivity::currentStageSetter($this->candidate);
        $this->stageSetByLine = $setter !== null
            ? __('candidate.stage_set_by', ['name' => $setter['name']])
            : null;
    }

    /**
     * @return list<array{id: int, happened_at: \Carbon\CarbonInterface, summary: string, actor: string, lines: list<array{label: string, from: string, to: string}>}>
     */
    public function getChangeHistoryRowsProperty(): array
    {
        return CandidateActivityPresentation::changeHistoryRows($this->candidate);
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
        $this->candidate->load([
            'notes.user',
            'activities' => fn ($q) => $q->with('causer')->latest(),
        ]);
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
        $this->candidate->load([
            'pipelineStage',
            'activities' => fn ($q) => $q->with('causer')->latest(),
        ]);
        $this->syncStageAuditMetadata();
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

    public function openChangeHistoryModal(): void
    {
        $this->candidate->load([
            'activities' => fn ($q) => $q->with('causer')->latest(),
        ]);
        $this->showChangeHistoryModal = true;
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

        // Read size and mime type before store(): with same disk, Livewire moves the temp file and metadata is no longer available.
        $size = $this->documentFile->getSize();
        $mimeType = $this->documentFile->getMimeType();

        $path = $this->documentFile->store('candidate-documents/'.$this->candidate->id, 'local');
        app(CandidateDocumentRepository::class)->create(
            candidateId: $this->candidate->id,
            name: $this->documentName,
            path: $path,
            mimeType: $mimeType,
            size: $size,
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
            'germanLevels' => GermanLanguageLevel::cases(),
            'changeHistoryRows' => $this->changeHistoryRows,
        ])->title($this->candidate->person->fullName());
    }
}
