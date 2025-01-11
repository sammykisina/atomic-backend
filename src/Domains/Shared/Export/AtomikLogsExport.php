<?php

declare(strict_types=1);

namespace Domains\Shared\Export;

use Domains\Shared\Models\AtomikLog;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

final class AtomikLogsExport implements FromArray, Responsable, ShouldAutoSize, WithEvents, WithHeadings
{
    use Exportable;

    public function __construct(public int $train_id) {}

    /**  @return array<int, string> */
    public function headings(): array
    {
        return [
            '#',
            'RESOURCE',
            'TYPE',
            'LOCOMOTIVE',
            'TRAIN',
            'CURRENT_LOCATION',
            'MESSAGE',
            'ACTOR',
            'RECEIVER',
            'CREATED_AT',
        ];
    }

    /**
     * @return array
     */
    public function array(): array
    {


        $logs = AtomikLog::query()
            ->where('train_id', $this->train_id)
            ->with(['resourceble', 'actor', 'receiver', 'locomotiveNumber', 'train'])
            ->orderBy('created_at', 'DESC')
            ->get();

        return $logs->map(fn($log, $index) => [
            '#' => $index + 1,
            'RESOURCE' => AtomikLog::getResourcebleType(
                namespaceString: $log->resourceble_type,
            ),
            'TYPE' => $log->type->value,
            'LOCOMOTIVE' => $log->locomotive->number ?? '-',
            'TRAIN' => $log->train->trainName->name ?? '-',
            'CURRENT_LOCATION' => $log->current_location ?? '-',
            'MESSAGE' => $log->message ?? '',
            'ACTOR' => $log->actor?->fullname . ' - ' . $log->actor->type->value ?? '-',
            'RECEIVER' => $log->receiver?->fullname . ' - ' . $log->receiver->type->value ?? '-',
            'CREATED_AT' => $log->created_at->format('Y-m-d H:i:s'), // Format the created_at timestamp
        ])->toArray();
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $event->sheet->getStyle('A1:J1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                ]);
            },
        ];
    }
}
