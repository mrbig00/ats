<?php

namespace App\Livewire\Employees;

use App\Actions\Employees\CreateContractAction;
use App\Actions\Employees\TerminateEmployeeWorkflowService;
use App\Actions\Employees\UpdateContractAction;
use App\Actions\Employees\UpdateEmployeeAction;
use App\Data\Employees\ContractData;
use App\Data\Employees\TerminateEmployeeData;
use App\Data\Employees\UpdateContractData;
use App\Data\Employees\UpdateEmployeeData;
use App\Models\Employee;
use App\Repositories\ContractRepository;
use App\Repositories\OccupancyRepository;
use Carbon\CarbonImmutable;
use Livewire\Component;

class EmployeeShow extends Component
{
    public Employee $employee;

    public string $entryDate = '';

    public bool $showTerminateModal = false;

    public string $terminateExitDate = '';

    public string $terminateStatus = 'left';

    public bool $showContractModal = false;

    public string $contractType = '';

    public string $contractStartsAt = '';

    public string $contractEndsAt = '';

    public ?string $contractNotes = null;

    public bool $showEditContractModal = false;

    public ?int $editingContractId = null;

    public string $editContractType = '';

    public string $editContractStartsAt = '';

    public string $editContractEndsAt = '';

    public ?string $editContractNotes = null;

    public function mount(Employee $employee): void
    {
        $this->authorize('view', $employee);
        $this->employee = $employee->load(['person', 'occupancies.room.apartment', 'contracts']);
        $this->entryDate = $this->employee->entry_date?->format('Y-m-d') ?? '';
        $this->terminateExitDate = now()->format('Y-m-d');
    }

    public function saveEntryDate(): void
    {
        $this->authorize('update', $this->employee);
        $this->validate([
            'entryDate' => ['required', 'date'],
        ], [], ['entryDate' => __('employee.entry_date')]);

        app(UpdateEmployeeAction::class)->handle(new UpdateEmployeeData(
            employeeId: $this->employee->id,
            entryDate: CarbonImmutable::parse($this->entryDate),
        ));

        $this->employee->refresh();
        $this->dispatch('notify', __('employee.entry_date_updated'));
    }

    public function openTerminateModal(): void
    {
        $this->authorize('terminate', $this->employee);
        $this->showTerminateModal = true;
        $this->terminateExitDate = now()->format('Y-m-d');
        $this->terminateStatus = 'left';
    }

    public function terminate(): mixed
    {
        $this->authorize('terminate', $this->employee);
        $this->validate([
            'terminateExitDate' => ['required', 'date'],
            'terminateStatus' => ['required', 'in:leaving,left'],
        ], [], [
            'terminateExitDate' => __('employee.exit_date'),
            'terminateStatus' => __('employee.status'),
        ]);

        app(TerminateEmployeeWorkflowService::class)->handle(new TerminateEmployeeData(
            employeeId: $this->employee->id,
            exitDate: CarbonImmutable::parse($this->terminateExitDate),
            status: $this->terminateStatus,
        ));

        $this->showTerminateModal = false;
        $this->employee->refresh();
        $this->employee->load(['person', 'occupancies.room.apartment', 'contracts']);
        $this->dispatch('notify', __('employee.terminated'));

        return $this->redirect(route('employees.show', $this->employee), navigate: true);
    }

    public function openContractModal(): void
    {
        $this->authorize('update', $this->employee);
        $this->showContractModal = true;
        $this->contractType = '';
        $this->contractStartsAt = now()->format('Y-m-d');
        $this->contractEndsAt = '';
        $this->contractNotes = null;
    }

    public function addContract(): void
    {
        $this->authorize('update', $this->employee);
        $this->validate([
            'contractType' => ['required', 'string', 'max:100'],
            'contractStartsAt' => ['required', 'date'],
            'contractEndsAt' => ['nullable', 'date', 'after_or_equal:contractStartsAt'],
            'contractNotes' => ['nullable', 'string', 'max:2000'],
        ], [], [
            'contractType' => __('contract.type'),
            'contractStartsAt' => __('contract.starts_at'),
            'contractEndsAt' => __('contract.ends_at'),
        ]);

        app(CreateContractAction::class)->handle(new ContractData(
            employeeId: $this->employee->id,
            type: $this->contractType,
            startsAt: CarbonImmutable::parse($this->contractStartsAt),
            endsAt: $this->contractEndsAt !== '' ? CarbonImmutable::parse($this->contractEndsAt) : null,
            notes: $this->contractNotes ?: null,
        ));

        $this->showContractModal = false;
        $this->employee->load('contracts');
        $this->dispatch('notify', __('contract.created'));
    }

    public function openEditContractModal(int $contractId): void
    {
        $contract = $this->employee->contracts->firstWhere('id', $contractId);
        if ($contract === null) {
            return;
        }
        $this->authorize('update', $contract);
        $this->editingContractId = $contractId;
        $this->editContractType = $contract->type;
        $this->editContractStartsAt = $contract->starts_at->format('Y-m-d');
        $this->editContractEndsAt = $contract->ends_at?->format('Y-m-d') ?? '';
        $this->editContractNotes = $contract->notes;
        $this->showEditContractModal = true;
    }

    public function updateContract(): void
    {
        $this->validate([
            'editContractType' => ['required', 'string', 'max:100'],
            'editContractStartsAt' => ['required', 'date'],
            'editContractEndsAt' => ['nullable', 'date', 'after_or_equal:editContractStartsAt'],
            'editContractNotes' => ['nullable', 'string', 'max:2000'],
        ], [], [
            'editContractType' => __('contract.type'),
            'editContractStartsAt' => __('contract.starts_at'),
            'editContractEndsAt' => __('contract.ends_at'),
        ]);

        app(UpdateContractAction::class)->handle(new UpdateContractData(
            contractId: $this->editingContractId,
            type: $this->editContractType,
            startsAt: CarbonImmutable::parse($this->editContractStartsAt),
            endsAt: $this->editContractEndsAt !== '' ? CarbonImmutable::parse($this->editContractEndsAt) : null,
            notes: $this->editContractNotes ?: null,
        ));

        $this->showEditContractModal = false;
        $this->editingContractId = null;
        $this->employee->load('contracts');
        $this->dispatch('notify', __('contract.updated'));
    }

    public function deleteContract(int $contractId): void
    {
        $contract = app(ContractRepository::class)->find($contractId);
        if ($contract !== null && $contract->employee_id === $this->employee->id) {
            $this->authorize('delete', $contract);
            app(ContractRepository::class)->delete($contract);
            $this->employee->load('contracts');
            $this->dispatch('notify', __('contract.deleted'));
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Occupancy>
     */
    public function getActiveOccupanciesProperty(): \Illuminate\Database\Eloquent\Collection
    {
        return app(OccupancyRepository::class)->getActiveByEmployeeId($this->employee->id);
    }

    public function render()
    {
        return view('livewire.employees.employee-show', [
            'activeOccupancies' => $this->activeOccupancies,
        ])->title($this->employee->person->fullName());
    }
}
