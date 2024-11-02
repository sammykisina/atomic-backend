<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Driver\Journey;

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
        $model = DB::transaction(function () use ($request, $license): void {
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

            /**
             * MOVE TRAIN THROUGH THE LICENSE
             */
            // Get the `through` attribute as an array to modify it
            $through = $license->through;

            if ($license->train_at_origin) {
                // Clear train from origin
                $license->train_at_origin = false;

                // Work with `origin` as an array, modify it, then reassign
                $origin = $license->origin;
                $origin['status'] = StationSectionLoopStatuses::GOOD->value;
                $license->origin = $origin;

                if ( ! empty($through)) {
                    // Move train to the first position in `through`
                    $through[0]['train_is_here'] = true;
                } else {
                    // No `through`, move directly to destination
                    $license->train_at_destination = true;
                }
            } else {
                // Moving the train within `through`
                $movedToNext = false; // Flag to track if the train has moved to the next point

                foreach ($through as $index => &$point) {
                    if ($point['train_is_here']) {
                        // Clear current position in `through`
                        $point['train_is_here'] = false;
                        $point['status'] = StationSectionLoopStatuses::GOOD->value;

                        if (isset($through[$index + 1])) {
                            // Move to the next `through` point
                            $through[$index + 1]['train_is_here'] = true;
                        } else {
                            // Last `through` point cleared, move to destination
                            $license->train_at_destination = true;
                        }

                        $movedToNext = true;
                        break;
                    }
                }

                // If the train hasn't moved and it's not in `through`, check if it should be at destination
                if ( ! $movedToNext && ! $license->train_at_destination) {
                    $license->train_at_destination = true;
                }
            }

            // Update the `through` attribute in the license model
            $license->through = $through;
            $license->save();
        });

        return response(
            content: [
                'message' => 'Area cleared successfully.',
                'area' => $model,
            ],
            status: Http::ACCEPTED(),
        );
    }

}
