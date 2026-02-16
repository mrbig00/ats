<?php

namespace App\Livewire\Positions;

use App\Actions\Positions\UpdatePositionAction;
use App\Data\Positions\PositionData;
use App\Models\Position;
use Carbon\CarbonImmutable;
use Livewire\Component;

class EditPosition extends Component
{
    public Position $position;

    public string $title = '';

    public string $description = '';

    public string $status = 'open';

    public ?string $opensAt = null;

    public ?string $closesAt = null;

    public function mount(Position $position): void
    {
        $this->position = $position;
        $this->authorize('update', $position);
        $this->title = $position->title;
        $this->description = $position->description ?? '';
        $this->status = $position->status;
        $this->opensAt = $position->opens_at?->format('Y-m-d');
        $this->closesAt = $position->closes_at?->format('Y-m-d');
    }

    public function save(): mixed
    {
        $this->authorize('edit', $this->position);

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

        app(UpdatePositionAction::class)->handle($this->position, $data);

        $this->dispatch('notify', __('job.updated'));

        return $this->redirect(route('jobs.show', $this->position), navigate: true);
    }

    public function render()
    {
        return view('livewire.positions.edit-position')->title(__('job.edit'));
    }
}
