<?php

namespace App\Livewire\Positions;

use App\Actions\Positions\DeletePositionAction;
use App\Models\Position;
use App\Repositories\PositionRepository;
use Livewire\Component;

class PositionShow extends Component
{
    public Position $position;

    public function mount(Position $position): void
    {
        $this->position = app(PositionRepository::class)->find($position->id) ?? abort(404);
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
            'position' => $this->position,
        ])->title($this->position->title);
    }
}
