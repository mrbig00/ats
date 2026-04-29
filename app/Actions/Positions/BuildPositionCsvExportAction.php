<?php

namespace App\Actions\Positions;

use App\Data\Positions\PositionFilterData;
use App\Exports\PositionExport;
use App\Repositories\PositionRepository;

class BuildPositionCsvExportAction
{
    public function handle(PositionFilterData $filters): PositionExport
    {
        $query = app(PositionRepository::class)->exportQuery($filters);

        return new PositionExport($query);
    }
}

