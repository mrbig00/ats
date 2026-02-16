<?php

namespace App\Livewire\Housing;

use App\Actions\Housing\EndOccupancyAction;
use App\Models\Occupancy;
use Carbon\CarbonImmutable;
use Livewire\Component;

class EndOccupancyModal extends Component
{
    public bool $show = false;

    public ?int $occupancyId = null;

    public string $endsAt = '';

    protected $listeners = ['openEndOccupancyModal' => 'open'];

    /**
     * @param  array{occupancyId: int}  $payload
     */
    public function open($payload): void
    {
        $occupancyId = is_array($payload) ? ($payload['occupancyId'] ?? null) : $payload;
        if (! is_numeric($occupancyId)) {
            return;
        }
        $occupancy = Occupancy::query()->find((int) $occupancyId);
        if ($occupancy === null || ! $occupancy->isActive()) {
            return;
        }
        $this->authorize('update', $occupancy);
        $this->occupancyId = $occupancyId;
        $this->endsAt = now()->toDateString();
        $this->show = true;
    }

    public function close(): void
    {
        $this->show = false;
        $this->occupancyId = null;
        $this->resetValidation();
    }

    public function endOccupancy(): mixed
    {
        $occupancy = Occupancy::query()->find($this->occupancyId);
        if ($occupancy === null) {
            $this->close();

            return null;
        }
        $this->authorize('update', $occupancy);

        $this->validate([
            'endsAt' => ['required', 'date', 'after_or_equal:'.$occupancy->starts_at->toDateString()],
        ], [], [
            'endsAt' => __('housing.occupancy_ends_at'),
        ]);

        try {
            app(EndOccupancyAction::class)->handle($occupancy, CarbonImmutable::parse($this->endsAt));
        } catch (\DomainException $e) {
            $this->addError('endsAt', $e->getMessage());

            return null;
        }

        $this->dispatch('notify', __('housing.occupancy_ended'));
        $this->close();
        $this->dispatch('occupancy-ended');

        return null;
    }

    public function render()
    {
        return view('livewire.housing.end-occupancy-modal');
    }
}
