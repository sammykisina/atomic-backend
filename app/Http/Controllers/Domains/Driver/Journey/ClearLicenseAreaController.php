<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Driver\Journey;

use Domains\Driver\Enums\LicenseRouteStatuses;
use Domains\Driver\Models\License;
use Domains\Driver\Requests\ClearRequest;
use Domains\Driver\Services\JourneyService;
use Domains\Operator\Services\LicenseService;
use Domains\Shared\Enums\AtomikLogsTypes;
use Domains\Shared\Services\AtomikLogService;
use Domains\SuperAdmin\Enums\StationSectionLoopStatuses;
use Domains\SuperAdmin\Services\ShiftManagement\ShiftService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use JustSteveKing\StatusCode\Http;

final class ClearLicenseAreaController
{
    public function __invoke(ClearRequest $request, License $license): Response
    {
        DB::transaction(function () use ($request, $license): void {
            $model = LicenseService::getModel(
                model_type: $request->validated('type'),
                model_id: $request->validated('area_id'),
            );

            if ( ! $model) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'No model found. Please try again',
                );
            }

            $through = $license->through;
            $destination = $license->destination;

            if ($license->train_at_origin) {
                // Clear origin and handle transition to `through` or `destination`
                $license->train_at_origin = false;
                $origin = $license->origin;
                $origin['status'] = StationSectionLoopStatuses::GOOD->value;
                $origin['end_time'] = now();
                $origin['in_route'] = LicenseRouteStatuses::COMPLETED->value;
                $origin['distance_remaining'] = 0;
                $license->origin = $origin;

                // Occupy destination if there's no through point
                if (empty($through)) {
                    $this->occupyDestination($license, $destination);
                } else {
                    $this->occupyFirstThrough($through, $license, $destination);
                }
            } else {
                $this->clearCurrentThroughPoint($through, $license, $destination);
            }

            // Save updated license
            $license->through = $through;
            $license->save();
        });

        $shifts = $license->journey->shifts;
        $shift = ShiftService::getShiftById(
            shift_id: end($shifts),
        );

        $logs = array_merge([
            ['type' => AtomikLogsTypes::LICENSE_AREA_SECTION_CLEARED->value,
                'cleared_by' => Auth::user()->employee_id,
                'cleared_at' => now(),
                'area_name' => LicenseService::getLicenseOrigin(
                    model: LicenseService::getModel(
                        model_type: $request->validated('type'),
                        model_id: $request->validated('area_id'),
                    ),
                ),
            ],
        ], $license->logs);

        defer(callback: fn() => $license->update(attributes: [
            'logs' => $logs,
        ]));


        AtomikLogService::createAtomicLog(atomikLogData: [
            'type' => AtomikLogsTypes::LICENSE_AREA_SECTION_CLEARED,
            'resourceble_id' => $license->id,
            'resourceble_type' => get_class(object: $license),
            'actor_id' => Auth::id(),
            'receiver_id' => $shift->user_id,
            'current_location' => JourneyService::getTrainLocation(journey: $license->journey)['name'],
            'train_id' => $license->journey->train_id,
            'locomotive_number_id' => $license->journey->train->locomotive_number_id,
        ]);

        return response(
            content: [
                'message' => 'Area cleared successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }

    private function occupyFirstThrough(array &$through, License $license, array &$destination): void
    {
        if ($through[0]['in_route'] === LicenseRouteStatuses::REVOKED->value) {
            abort(
                code: Http::FORBIDDEN(),
                message: 'The first through area is revoked and cannot be occupied.',
            );
        }

        // Occupy the first through point
        $through[0]['train_is_here'] = true;
        $through[0]['in_route'] = LicenseRouteStatuses::OCCUPIED->value;
        $through[0]['start_time'] = now();
        $through[0]['status'] = StationSectionLoopStatuses::LICENSE_ISSUED->value;

        // If there's only one through point, mark the destination as NEXT
        if (1 === count($through)) {
            if ($destination['in_route'] !== LicenseRouteStatuses::REVOKED->value) {
                $destination['in_route'] = LicenseRouteStatuses::NEXT->value;
                $license->destination = $destination;
            }
        }

        // Mark next through point as NEXT if there are more than one
        if (count($through) > 1 && $through[1]['in_route'] !== LicenseRouteStatuses::REVOKED->value) {
            $through[1]['in_route'] = LicenseRouteStatuses::NEXT->value;
        }
    }

    private function occupyDestination(License $license, array &$destination): void
    {
        if ($destination['in_route'] === LicenseRouteStatuses::REVOKED->value) {
            abort(
                code: Http::FORBIDDEN(),
                message: 'The destination area is revoked and cannot be occupied.',
            );
        }

        $destination['in_route'] = LicenseRouteStatuses::OCCUPIED->value;
        $destination['start_time'] = now();
        $license->destination = $destination;
        $license->train_at_destination = true;
    }

    private function clearCurrentThroughPoint(array &$through, License $license, array &$destination): void
    {
        // Check if the train is already at the destination
        if ($license->train_at_destination) {
            abort(
                code: Http::FORBIDDEN(),
                message: 'You have arrived at your license destination. Please request your OCC for line exit.',
            );
        }

        foreach ($through as $index => &$point) {
            if ($point['train_is_here']) {
                // Clear the current point
                $point['train_is_here'] = false;
                $point['status'] = StationSectionLoopStatuses::GOOD->value;
                $origin['distance_remaining'] = 0;
                $point['end_time'] = now();
                $point['in_route'] = LicenseRouteStatuses::COMPLETED->value;

                // Handle the case when we clear the origin (first through section)
                if (0 === $index) {
                    // Transition the next through point if exists
                    if (count($through) > 1 && $through[1]['in_route'] !== LicenseRouteStatuses::REVOKED->value) {
                        $through[1]['in_route'] = LicenseRouteStatuses::OCCUPIED->value;
                        $through[1]['train_is_here'] = true;
                        $through[1]['start_time'] = now();
                        $through[1]['status'] = StationSectionLoopStatuses::LICENSE_ISSUED->value;
                    }
                    // Mark destination as NEXT if there's only one through point
                    if (1 === count($through) && $destination['in_route'] !== LicenseRouteStatuses::REVOKED->value) {
                        $destination['in_route'] = LicenseRouteStatuses::NEXT->value;
                    }
                }

                // Handle the case for the second-to-last through point
                if ($index === count($through) - 2) {
                    // Mark the destination as NEXT
                    if ($destination['in_route'] !== LicenseRouteStatuses::REVOKED->value) {
                        $destination['in_route'] = LicenseRouteStatuses::NEXT->value;
                    }
                }

                // Handle the case for the last through point (clear it and occupy the destination)
                if ($index === count($through) - 1) {
                    // If this is the last point in the through, mark the destination as OCCUPIED
                    if ($destination['in_route'] !== LicenseRouteStatuses::REVOKED->value) {
                        $destination['in_route'] = LicenseRouteStatuses::OCCUPIED->value;
                        $destination['start_time'] = now();  // Set the start time for destination occupancy
                        $destination['status'] = StationSectionLoopStatuses::LICENSE_ISSUED->value;
                        $license->train_at_destination = true; // Set train_at_destination to true
                    } else {
                        abort(
                            code: Http::FORBIDDEN(),
                            message: 'Destination is revoked.',
                        );
                    }
                }

                // Transition to the next through section if there are more points
                if ($index < count($through) - 1) {
                    $this->occupyNextThroughPoint($through, $index + 1);
                }

                break; // Exit the loop once the current point is cleared and processed
            }
        }

        // Save the updated destination back to the license
        $license->destination = $destination;
        $license->save();
    }

    private function occupyNextThroughPoint(array &$through, int $nextIndex): void
    {
        if ($through[$nextIndex]['in_route'] === LicenseRouteStatuses::REVOKED->value) {
            abort(
                code: Http::FORBIDDEN(),
                message: 'The next through area is revoked and cannot be occupied.',
            );
        }

        // Ensure that the next through point exists and is not revoked
        if (isset($through[$nextIndex]) && $through[$nextIndex]['in_route'] !== LicenseRouteStatuses::REVOKED->value) {
            // Occupy the next through point
            $through[$nextIndex]['train_is_here'] = true;
            $through[$nextIndex]['in_route'] = LicenseRouteStatuses::OCCUPIED->value;
            $through[$nextIndex]['start_time'] = now();
            $through[$nextIndex]['status'] = StationSectionLoopStatuses::LICENSE_ISSUED->value;

            // Mark the next through point as NEXT if it exists and is not revoked
            if (isset($through[$nextIndex + 1]) && $through[$nextIndex + 1]['in_route'] !== LicenseRouteStatuses::REVOKED->value) {
                $through[$nextIndex + 1]['in_route'] = LicenseRouteStatuses::NEXT->value;
            }
        }
    }
}
