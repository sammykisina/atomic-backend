<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Shared\AtomikLogs;

use Domains\Shared\Export\AtomikLogsExport;
use Domains\SuperAdmin\Models\Train;
use Domains\SuperAdmin\Models\TrainName;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class GenerateLogsReportController
{
    public function __invoke(Request $request, Excel $excel, TrainName $trainName): BinaryFileResponse
    {
        $train = Train::query()
            ->where(column: 'train_name_id', operator: '=', value: $trainName->id)
            ->first();

        return $excel->download(new AtomikLogsExport(
            train_id: $train->id,
            date: $request->query('date'),
        ), fileName: $train->trainName->name . '-logs.xlsx');


    }
}
