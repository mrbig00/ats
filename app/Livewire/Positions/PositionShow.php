<?php

namespace App\Livewire\Positions;

use App\Actions\Positions\DeletePositionAction;
use App\Models\Position;
use Livewire\Component;

class PositionShow extends Component
{
    public Position $position;

    public function mount(Position $position): void
    {
        $this->position = $position->loadCount('candidates');
        $this->authorize('view', $this->position);
    }

    public function deletePosition(): mixed
    {
        $this->authorize('delete', $this->position);
        if (! $this->position->isOpen()) {
            return null;
        }
        app(DeletePositionAction::class)->handle($this->position);
        $this->dispatch('notify', __('job.deleted'));

        return $this->redirect(route('jobs.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.positions.position-show', [
            'position' => $this->position->loadCount('candidates'),
        ])->title($this->position->title);
    }
}
