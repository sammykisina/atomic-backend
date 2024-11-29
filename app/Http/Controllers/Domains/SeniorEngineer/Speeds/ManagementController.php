<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SeniorEngineer\Speeds;

use Domains\SeniorEngineer\Models\Speed;
use Domains\SeniorEngineer\Requests\AdjustSpeedRestrictionRequest;
use Domains\SeniorEngineer\Services\SpeedService;
use Domains\Shared\Enums\AtomikLogsTypes;
use Domains\Shared\Services\AtomikLogService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;

final class ManagementController
{
    public function __construct(
        public SpeedService $speedService,
    ) {}


    /**
     * @param Speed $speed
     * @return Response
     */
    public function delete(Request $request, Speed $speed): Response
    {
        if ( ! $speed->delete()) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Speed restriction deletion failed.',
            );
        }

        AtomikLogService::createAtomicLog(atomikLogData: [
            'type' => AtomikLogsTypes::SPEED_RESTRICTION_DELETED,
            'resourceble_id' => $speed->id,
            'resourceble_type' => get_class(object: $speed),
            'actor_id' => Auth::id(),
            'receiver_id' => Auth::id(),
            'current_location' => " KM " . $speed->start_kilometer . ' To KM ' . $speed->end_kilometer,
        ]);

        return response(
            content: [
                'message' => 'Speed restriction deleted successfully.',
            ],
            status: Http::OK(),
        );
    }


    /**
     * ADJUST SPEED RESTRICTION
     * @param AdjustSpeedRestrictionRequest $request
     * @param Speed $speed
     * @return Response
     */
    public function adjustSpeedRestriction(AdjustSpeedRestrictionRequest $request, Speed $speed): Response
    {
        //  $overlappingModel = SpeedService::checkOverlap(
        //     startKm: $request->validated(key: 'start_kilometer'),
        //     endKm: $request->validated(key: 'end_kilometer'),
        //     line_id: $speed->line_id,
        // );

        // if($overlappingModel){
        //      abort(
        //         code: Http::UNPROCESSABLE_ENTITY(),
        //         message: 'The speed restriction will overlaps with another speed restriction. There is a speed restriction of ' . $overlappingModel->speed . " at KM " . $overlappingModel->start_kilometer . ' to KM ' . $overlappingModel->end_kilometer . '. You can delete the exiting speed restriction or adjust its KMs before you adjust this one.',
        //     );
        // }

        if ( ! $speed->update(
            attributes: [
                'start_kilometer' => $request->validated('start_kilometer'),
                'end_kilometer' => $request->validated('end_kilometer'),
                'speed' => $request->validated('speed'),
                'start_kilometer_latitude' => $request->validated('start_kilometer_latitude'),
                'start_kilometer_longitude' => $request->validated('start_kilometer_longitude'),
                'end_kilometer_latitude' => $request->validated('end_kilometer_latitude'),
                'end_kilometer_longitude' => $request->validated('end_kilometer_longitude'),
            ],
        )) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Speed restriction adjustment failed.',
            );
        }

        return response(
            content: [
                'message' => 'Speed restriction adjusted successfully.',
            ],
            status: Http::OK(),
        );
    }
}
