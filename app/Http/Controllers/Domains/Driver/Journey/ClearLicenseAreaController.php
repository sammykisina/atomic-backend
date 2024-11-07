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

            // Clear the origin
            if ($license->train_at_origin) {
                $license->train_at_origin = false;

                $origin = $license->origin;
                $origin['status'] = StationSectionLoopStatuses::GOOD->value;
                $origin['end_time'] = now();
                $origin['in_route'] = LicenseRouteStatuses::COMPLETED->value; // Set origin as COMPLETED
                $license->origin = $origin;

                // Handle the case with no through points
                if (0 === count($through)) {
                    // Move directly to destination
                    $destination = $license->destination;
                    $destination['in_route'] = LicenseRouteStatuses::OCCUPIED->value;
                    $destination['start_time'] = now();
                    $license->destination = $destination;

                    // Set train_at_destination to true since the train has moved to the destination
                    $license->train_at_destination = true;
                } elseif (count($through) > 0) {
                    // Move to the first through point
                    $through[0]['train_is_here'] = true;
                    $through[0]['in_route'] = LicenseRouteStatuses::OCCUPIED->value;
                    $through[0]['start_time'] = now();
                    $through[0]['status'] = StationSectionLoopStatuses::LICENSE_ISSUED->value;

                    // Check if there are more through points
                    if (1 === count($through)) {
                        // If there's only one through element, set destination to NEXT
                        $destination = $license->destination;
                        $destination['in_route'] = LicenseRouteStatuses::NEXT->value;
                        $license->destination = $destination;
                    } elseif (count($through) > 1) {
                        $through[1]['in_route'] = LicenseRouteStatuses::NEXT->value;
                    }
                }
            } else {
                // Clearing a through point
                foreach ($through as $index => &$point) {
                    if ($point['train_is_here']) {
                        // Clear the current position in through
                        $point['train_is_here'] = false;
                        $point['status'] = StationSectionLoopStatuses::GOOD->value;
                        $point['end_time'] = now();
                        $point['in_route'] = LicenseRouteStatuses::COMPLETED->value; // Set cleared through point as COMPLETED

                        if ($index === count($through) - 1) {
                            // Set the destination as OCCUPIED
                            $destination = $license->destination;
                            $destination['in_route'] = LicenseRouteStatuses::OCCUPIED->value;
                            $destination['start_time'] = now();
                            $license->destination = $destination;

                            // Set train_at_destination to true since we've reached the destination
                            $license->train_at_destination = true;
                        } elseif ($index === count($through) - 2) {
                            // Mark the last through point as OCCUPIED
                            $lastPoint = &$through[$index + 1];
                            $lastPoint['train_is_here'] = true;
                            $lastPoint['in_route'] = LicenseRouteStatuses::OCCUPIED->value;
                            $lastPoint['start_time'] = now();
                            $lastPoint['status'] = StationSectionLoopStatuses::LICENSE_ISSUED->value;

                            // Set destination as NEXT
                            $destination = $license->destination;
                            $destination['in_route'] = LicenseRouteStatuses::NEXT->value;
                            $license->destination = $destination;
                        } else {
                            // Move to the next through point if not the last or second-to-last
                            if (isset($through[$index + 1])) {
                                $through[$index + 1]['train_is_here'] = true;
                                $through[$index + 1]['in_route'] = LicenseRouteStatuses::OCCUPIED->value;
                                $through[$index + 1]['start_time'] = now();
                                $through[$index + 1]['status'] = StationSectionLoopStatuses::LICENSE_ISSUED->value;

                                if (isset($through[$index + 2])) {
                                    $through[$index + 2]['in_route'] = LicenseRouteStatuses::NEXT->value;
                                }
                            }
                        }

                        break;
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

    //     public function __invoke(ClearRequest $request, License $license): Response
    // {
    //     DB::transaction(function () use ($request, $license): void {
    //         $model = LicenseService::getModel(
    //             model_type: $request->validated('type'),
    //             model_id: $request->validated('area_id'),
    //         );

    //         if (!$model) {
    //             abort(
    //                 code: Http::EXPECTATION_FAILED(),
    //                 message: 'No model found. Please try again',
    //             );
    //         }

    //         $through = $license->through;

    //         // Function to check if any point is REVOKED
    //         $isRevoked = function ($point) {
    //             return $point['in_route'] === LicenseRouteStatuses::REVOKED->value;
    //         };

    //         // Clear the origin
    //         if ($license->train_at_origin) {
    //             // Check if destination or first through point is revoked
    //             if ($isRevoked($license->destination)) {
    //                 abort(
    //                     code: Http::EXPECTATION_FAILED(),
    //                     message: 'Area has been revoked',
    //                 );
    //             }

    //             $license->train_at_origin = false;

    //             $origin = $license->origin;
    //             $origin['status'] = StationSectionLoopStatuses::GOOD->value;
    //             $origin['end_time'] = now();
    //             $origin['in_route'] = LicenseRouteStatuses::COMPLETED->value;
    //             $license->origin = $origin;

    //             if (0 === count($through)) {
    //                 $destination = $license->destination;
    //                 $destination['in_route'] = LicenseRouteStatuses::OCCUPIED->value;
    //                 $destination['start_time'] = now();
    //                 $license->destination = $destination;
    //                 $license->train_at_destination = true;
    //             } elseif (count($through) > 0) {
    //                 // Occupy the first through point if not revoked
    //                 if (!$isRevoked($through[0])) {
    //                     $through[0]['train_is_here'] = true;
    //                     $through[0]['in_route'] = LicenseRouteStatuses::OCCUPIED->value;
    //                     $through[0]['start_time'] = now();
    //                     $through[0]['status'] = StationSectionLoopStatuses::LICENSE_ISSUED->value;
    //                 } else {
    //                     abort(
    //                         code: Http::EXPECTATION_FAILED(),
    //                         message: 'Area has been revoked',
    //                     );
    //                 }

    //                 // Mark the next through point as NEXT if it exists and is not revoked
    //                 if (isset($through[1]) && !$isRevoked($through[1])) {
    //                     $through[1]['in_route'] = LicenseRouteStatuses::NEXT->value;
    //                 }
    //             }
    //         } else {
    //             // Clearing a through point
    //             foreach ($through as $index => &$point) {
    //                 if ($point['train_is_here']) {
    //                     $point['train_is_here'] = false;
    //                     $point['status'] = StationSectionLoopStatuses::GOOD->value;
    //                     $point['end_time'] = now();
    //                     $point['in_route'] = LicenseRouteStatuses::COMPLETED->value;

    //                     if ($index === count($through) - 1) {
    //                         // Check if destination is revoked before occupying it
    //                         if ($isRevoked($license->destination)) {
    //                             abort(
    //                                 code: Http::EXPECTATION_FAILED(),
    //                                 message: 'Area has been revoked',
    //                             );
    //                         }

    //                         $destination = $license->destination;
    //                         $destination['in_route'] = LicenseRouteStatuses::OCCUPIED->value;
    //                         $destination['start_time'] = now();
    //                         $license->destination = $destination;
    //                         $license->train_at_destination = true;
    //                     } elseif ($index === count($through) - 2) {
    //                         $lastPoint = &$through[$index + 1];
    //                         if (!$isRevoked($lastPoint)) {
    //                             $lastPoint['train_is_here'] = true;
    //                             $lastPoint['in_route'] = LicenseRouteStatuses::OCCUPIED->value;
    //                             $lastPoint['start_time'] = now();
    //                             $lastPoint['status'] = StationSectionLoopStatuses::LICENSE_ISSUED->value;
    //                         } else {
    //                             abort(
    //                                 code: Http::EXPECTATION_FAILED(),
    //                                 message: 'Area has been revoked',
    //                             );
    //                         }

    //                         // Set destination as NEXT if it's not revoked
    //                         if (!$isRevoked($license->destination)) {
    //                             $license->destination['in_route'] = LicenseRouteStatuses::NEXT->value;
    //                         }
    //                     } else {
    //                         // Move to the next through point if not the last or second-to-last
    //                         if (isset($through[$index + 1]) && !$isRevoked($through[$index + 1])) {
    //                             $through[$index + 1]['train_is_here'] = true;
    //                             $through[$index + 1]['in_route'] = LicenseRouteStatuses::OCCUPIED->value;
    //                             $through[$index + 1]['start_time'] = now();
    //                             $through[$index + 1]['status'] = StationSectionLoopStatuses::LICENSE_ISSUED->value;

    //                             // Set the following point as NEXT if it exists and is not revoked
    //                             if (isset($through[$index + 2]) && !$isRevoked($through[$index + 2])) {
    //                                 $through[$index + 2]['in_route'] = LicenseRouteStatuses::NEXT->value;
    //                             }
    //                         }
    //                     }

    //                     break;
    //                 }
    //             }
    //         }

    //         // Save the updated through array and license model
    //         $license->through = $through;
    //         $license->save();
    //     });

    //     return response(
    //         content: [
    //             'message' => 'Area cleared successfully.',
    //         ],
    //         status: Http::ACCEPTED(),
    //     );
    // }

}
