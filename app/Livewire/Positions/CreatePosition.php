<?php

namespace App\Livewire\Positions;

use App\Actions\Positions\CreatePositionAction;
use App\Data\Positions\PositionData;
use Carbon\CarbonImmutable;
use Livewire\Component;

class CreatePosition extends Component
{
    public string $title = '';

    public string $description = '';

    public string $status = 'open';

    public ?string $opensAt = null;

    public ?string $closesAt = null;

    public function mount(): void
    {
        $this->authorize('create', \App\Models\Position::class);
    }

    public function save(): mixed
    {
        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:open,closed'],
            'opensAt' => ['nullable', 'date'],
            'closesAt' => ['nullable', 'date', 'after_or_equal:opensAt'],
        ], [], [
            'title' => __('job.title'),
            'description' => __('job.description'),
            'status' => __('job.status'),
            'opensAt' => __('job.opens_at'),
            'closesAt' => __('job.closes_at'),
        ]);

        $data = new PositionData(
            title: $validated['title'],
            description: $validated['description'] ?: null,
            status: $validated['status'],
            opensAt: isset($validated['opensAt']) ? CarbonImmutable::parse($validated['opensAt']) : null,
            closesAt: isset($validated['closesAt']) ? CarbonImmutable::parse($validated['closesAt']) : null,
        );

        $position = app(CreatePositionAction::class)->handle($data);

        $this->dispatch('notify', __('job.created'));

        return $this->redirect(route('jobs.show', $position), navigate: true);
    }

    public function render()
    {
        return view('livewire.positions.create-position')->title(__('job.create'));
    }
}
