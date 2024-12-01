<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Shared\AtomikLogs;

use Domains\Shared\Export\AtomikLogsExport;
use Domains\SuperAdmin\Models\Train;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;

final class GenerateLogsReportController
{
    public function __invoke(Request $request, Excel $excel, Train $train)
    {
        return $excel->download(new AtomikLogsExport(
            train_id: $train->id,
            date: $request->query('date'),
        ), fileName: $train->trainName->name . '-logs.xlsx');


    }
}
