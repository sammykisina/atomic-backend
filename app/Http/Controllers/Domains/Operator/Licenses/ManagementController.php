<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Operator\Licenses;

use Domains\Driver\Models\Journey;
use Domains\Driver\Models\License;
use Domains\Driver\Services\JourneyService;
use Domains\Operator\Notifications\LicenseNotification;
use Domains\Operator\Requests\LicenseRequest;
use Domains\Operator\Services\LicenseService;
use Domains\Shared\Enums\NotificationTypes;
use Domains\SuperAdmin\Enums\StationSectionLoopStatuses;
use Domains\SuperAdmin\Services\LoopService;
use Domains\SuperAdmin\Services\SectionService;
use Domains\SuperAdmin\Services\StationService;
use Domains\SuperAdmin\Services\TrainService;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\DB;
use JustSteveKing\StatusCode\Http;

final class ManagementController
{
    public function __construct(
        protected LicenseService $licenseService,
    ) {}

    /**
     * ACCEPT JOURNEY REQUEST
     * @param Journey $journey
     * @return mixed
     */
    public function acceptRequest(LicenseRequest $request, Journey $journey, DatabaseNotification $notification)
    {
        if (null !== $notification->read_at) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'This request was processed before.Please contact your system administration for more inquires on the same.',
            );
        }

        DB::transaction(function () use ($request, $journey, $notification): License {

            $train = TrainService::getTrainById($journey->train->id);


            /**
             * ORIGIN
             */
            $origin = null;
            $origin_id = $request->validated('origin')['origin_id'];
            $origin_type = $request->validated('origin')['type'];

            match ($origin_type) {
                'STATION' =>  $origin = StationService::getStationById(station_id: $origin_id),
                'SECTION' =>  $origin = SectionService::getSectionById(section_id: $origin_id),
                'LOOP' =>  $origin = LoopService::getLoopById(loop_id: $origin_id),
            };


            /**
             * DESTINATION
             */
            $destination = null;
            $destination_id = $request->validated('destination')['destination_id'];
            $destination_type = $request->validated('destination')['type'];

            match ($destination_type) {
                'STATION' =>  $destination = StationService::getStationById(station_id: $destination_id),
                'SECTION' =>  $destination = SectionService::getSectionById(section_id: $destination_id),
                'LOOP' =>  $destination = LoopService::getLoopById(loop_id: $destination_id),
            };

            $throughs = $request->validated('through');

            $uniqueLicenseNumber = $this->licenseService->getLicense();
            $license = $this->licenseService->acceptJourneyRequest(
                licenseData: [
                    'license_number' => $uniqueLicenseNumber,
                    'journey_id' => $journey->id,
                    'direction' => JourneyService::getJourneyDirection(
                        origin: $train->origin->start_kilometer,
                        destination: $train->destination->start_kilometer,
                    )->value,


                    'originable_id' => $origin_id,
                    'originable_type' => get_class(
                        object: $origin,
                    ),

                    'through' => $throughs,

                    'destinationable_id' => $destination_id,
                    'destinationable_type' => get_class(
                        object: $destination,
                    ),
                ],
            );

            $origin->update(attributes: [
                'status'  => StationSectionLoopStatuses::LICENSE_ISSUED->value,
            ]);

            $destination->update(attributes: [
                'status'  => StationSectionLoopStatuses::LICENSE_ISSUED->value,
            ]);


            /**
             * THOUGH
             */
            foreach ($throughs as $through) {
                $through_model = null;

                $through_id = $through['id'];
                $through_type = $through['type'];

                match ($through_type) {
                    'STATION' =>  $through_model = StationService::getStationById(station_id: $through_id),
                    'SECTION' =>  $through_model = SectionService::getSectionById(section_id: $through_id),
                    'LOOP' =>  $through_model = LoopService::getLoopById(loop_id: $through_id),
                };

                $through_model->update(attributes: [
                    'status'  => StationSectionLoopStatuses::LICENSE_ISSUED->value,
                ]);
            }

            $journey->train->driver->notify(new LicenseNotification(
                license: $license,
                type: NotificationTypes::REQUEST_ACCEPTED,
            ));

            $notification->markAsRead();

            return $license;

        });


        return response(
            content: [
                'message' => 'Request accepted and license assigned successfully.',
            ],
            status: Http::CREATED(),
        );
    }
}
