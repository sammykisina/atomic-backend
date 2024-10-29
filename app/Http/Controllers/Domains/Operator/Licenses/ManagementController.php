<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Operator\Licenses;

use Domains\Driver\Models\Journey;
use Domains\Driver\Models\License;
use Domains\Driver\Services\JourneyService;
use Domains\Operator\Enums\ShiftStatuses;
use Domains\Operator\Notifications\LicenseNotification;
use Domains\Operator\Requests\LicenseRequest;
use Domains\Operator\Services\LicenseService;
use Domains\Shared\Enums\NotificationTypes;
use Domains\SuperAdmin\Enums\StationSectionLoopStatuses;
use Domains\SuperAdmin\Models\Shift;
use Domains\SuperAdmin\Services\TrainService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use JustSteveKing\StatusCode\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class ManagementController
{
    public function __construct(
        protected LicenseService $licenseService,
    ) {}

    /**
     *  ACCEPT JOURNEY REQUEST
     * @param LicenseRequest $request
     * @param Journey $journey
     * @param DatabaseNotification $notification
     * @return Response | HttpException
     */
    public function acceptRequest(Request $request, Journey $journey, DatabaseNotification $notification): Response|HttpException
    {
        if (null !== $notification->read_at) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'This request was processed before.Please contact your system administration for more inquires on the same.',
            );
        }

        $is_authorized =  DB::transaction(function () use ($journey, $notification): bool {
            $is_updated = $journey->update(attributes: [
                'is_authorized' => true,
            ]);

            $notification->markAsRead();

            return $is_updated;

        });

        if ( ! $is_authorized) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Request for line entry is not authorized successfully.Please try again.',
            );
        }


        return response(
            content: [
                'message' => 'Request for line entry has been authorized successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }

    /**
     * ASSIGN LICENSE
     * @param LicenseRequest $request
     * @param Journey $journey
     * @return  Response|HttpException
     */
    public function assignLicense(LicenseRequest $request, Journey $journey): Response|HttpException
    {
        DB::transaction(function () use ($request, $journey): License {
            $shift  = Shift::query()
                ->where('user_id', Auth::id())
                ->where('status', ShiftStatuses::CONFIRMED->value)
                ->where('active', true)
                ->first();

            $currentShiftIds = $journey->shifts ?? [];
            $currentShiftIds[] = $shift->id;

            $license = $this->createLicense(
                request: $request,
                journey: $journey,
            );

            $journey->update(attributes: [
                'shifts' => $currentShiftIds,
            ]);

            return $license;
        });


        return response(
            content: [
                'message' => 'License assigned successfully. Awaiting confirmation.',
            ],
            status: Http::CREATED(),
        );
    }


    /**
     * DECLINE LINE ENTRY
     * @param Request $request
     * @param Journey $journey
     * @param DatabaseNotification $notification
     * @return Response|HttpException
     */
    public function declineRequest(Request $request, Journey $journey, DatabaseNotification $notification): Response|HttpException
    {
        if (null !== $notification->read_at) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'This request was processed before.Please contact your system administration for more inquires on the same.',
            );
        }

        $is_declined =  DB::transaction(function () use ($journey, $notification): bool {
            $is_declined = $journey->delete();

            $notification->markAsRead();

            return $is_declined;

        });

        if ( ! $is_declined) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Request for line entry is not declined successfully.Please try again.',
            );
        }


        return response(
            content: [
                'message' => 'Request for line entry has been authorized successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }

    /**
     * CREATE LICENSE
     * @param LicenseRequest $request
     * @param Journey $journey
     * @return License
     */
    private function createLicense(LicenseRequest $request, Journey $journey): License
    {
        $train = TrainService::getTrainById($journey->train->id);
        /**
         * ORIGIN
         */
        $origin = null;
        $origin_id = $request->validated('origin')['origin_id'];
        $origin_type = $request->validated('origin')['type'];
        $origin = LicenseService::getModel(
            model_type: $origin_type,
            model_id: $origin_id,
        );


        /**
         * DESTINATION
         */
        $destination = null;
        $destination_id = $request->validated('destination')['destination_id'];
        $destination_type = $request->validated('destination')['type'];
        $destination = LicenseService::getModel(
            model_type: $destination_type,
            model_id: $destination_id,
        );

        $throughs = $request->validated('through');
        $updated_throughs = array_map(
            callback: function (array $through): array {
                return [
                    'id' => $through['id'],
                    'type' => $through['type'],
                    'train_is_here' => false,
                ];
            },
            array: $throughs,
        );




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
                'train_at_origin' => true,

                'through' => $updated_throughs,

                'destinationable_id' => $destination_id,
                'destinationable_type' => get_class(
                    object: $destination,
                ),
                'train_at_destination' => false,
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

            $through_model =  LicenseService::getModel(
                model_type: $through_type,
                model_id: $through_id,
            );

            $through_model->update(attributes: [
                'status'  => StationSectionLoopStatuses::LICENSE_ISSUED->value,
            ]);
        }

        $journey->train->driver->notify(new LicenseNotification(
            license: $license,
            type: NotificationTypes::REQUEST_ACCEPTED,
        ));

        return $license;
    }
}
