<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Operator\Journeys;

use Domains\Driver\Models\Journey;
use Illuminate\Http\Request;

final class ExitTrainController
{
    public function __invoke(Request $request, Journey $journey): void {}
}
