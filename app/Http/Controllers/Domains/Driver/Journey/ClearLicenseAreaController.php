<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Driver\Journey;

use Domains\Driver\Enums\LicenseRouteStatuses;
use Domains\Driver\Models\License;
use Domains\Driver\Requests\ClearRequest;
use Domains\Operator\Services\LicenseService;
use Domains\SuperAdmin\Enums\StationSectionLoopStatuses;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

            Log::channel('atomik')->info(message: 'License to be cleared ' . $license->id);

            $through = $license->through;

            // Clear the origin
            if ($license->train_at_origin) {
                $license->train_at_origin = false;

                $origin = $license->origin;
                $origin['status'] = StationSectionLoopStatuses::GOOD->value;
                $origin['end_time'] = now(); // Set end time as train leaves
                $origin['in_route'] = LicenseRouteStatuses::OCCUPIED->value; // Keep as OCCUPIED
                $license->origin = $origin;

                // Handle the case with no through points
                if (0 === count($through)) {
                    // Move directly to destination
                    $destination = $license->destination;
                    $destination['in_route'] = LicenseRouteStatuses::OCCUPIED->value; // Set destination as OCCUPIED
                    $destination['start_time'] = now(); // Set start time for destination
                    $license->destination = $destination;

                    // Set train_at_destination to true since the train has moved to the destination
                    $license->train_at_destination = true;
                } elseif (count($through) > 0) {
                    // Move to the first through point
                    $through[0]['train_is_here'] = true;
                    $through[0]['in_route'] = LicenseRouteStatuses::OCCUPIED->value; // Keep as OCCUPIED
                    $through[0]['start_time'] = now();
                    $through[0]['status'] = StationSectionLoopStatuses::LICENSE_ISSUED->value;

                    // Check if there are more through points
                    if (1 === count($through)) {
                        // If there's only one through element, set destination to NEXT
                        $destination = $license->destination;
                        $destination['in_route'] = LicenseRouteStatuses::NEXT->value; // Set to NEXT since we are going there
                        $license->destination = $destination;
                    } elseif (count($through) > 1) {
                        // Set the second point as NEXT
                        $through[1]['in_route'] = LicenseRouteStatuses::NEXT->value; // Mark the next point
                    }
                }
            } else {
                // Clearing a through point
                foreach ($through as $index => &$point) {
                    if ($point['train_is_here']) {
                        // Clear the current position in through
                        $point['train_is_here'] = false;
                        $point['status'] = StationSectionLoopStatuses::GOOD->value;
                        $point['end_time'] = now(); // Set end time as train leaves
                        $point['in_route'] = LicenseRouteStatuses::OCCUPIED->value; // Keep as OCCUPIED

                        // If this is the last through point
                        if ($index === count($through) - 1) {
                            // Set the destination as OCCUPIED
                            $destination = $license->destination;
                            $destination['in_route'] = LicenseRouteStatuses::OCCUPIED->value; // Change to OCCUPIED
                            $destination['start_time'] = now(); // Set start_time for destination
                            $license->destination = $destination;

                            // Set train_at_destination to true since we've reached the destination
                            $license->train_at_destination = true;
                        } elseif ($index === count($through) - 2) {
                            // If this is the second-to-last through point
                            // Mark the last through point as OCCUPIED
                            $lastPoint = &$through[$index + 1];
                            $lastPoint['train_is_here'] = true;
                            $lastPoint['in_route'] = LicenseRouteStatuses::OCCUPIED->value; // Set last point as OCCUPIED
                            $lastPoint['start_time'] = now(); // Set start_time for the last through point
                            $lastPoint['status'] = StationSectionLoopStatuses::LICENSE_ISSUED->value;

                            // Set destination as NEXT
                            $destination = $license->destination;
                            $destination['in_route'] = LicenseRouteStatuses::NEXT->value; // Change to NEXT
                            $license->destination = $destination;
                        } else {
                            // Move to the next through point if not the last or second-to-last
                            if (isset($through[$index + 1])) {
                                $through[$index + 1]['train_is_here'] = true;
                                $through[$index + 1]['in_route'] = LicenseRouteStatuses::OCCUPIED->value; // Set next point as OCCUPIED
                                $through[$index + 1]['start_time'] = now(); // Set start_time for the next through point
                                $through[$index + 1]['status'] = StationSectionLoopStatuses::LICENSE_ISSUED->value;

                                // Mark the next subsequent point as NEXT if it exists
                                if (isset($through[$index + 2])) {
                                    $through[$index + 2]['in_route'] = LicenseRouteStatuses::NEXT->value;
                                }
                            }
                        }

                        break; // Break out of the loop since we moved
                    }
                }
            }

            // Save the updated through array and license model
            $license->through = $through;
            $license->save();
        });

        return response(
            content: [
                'message' => 'Area cleared successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }
}
