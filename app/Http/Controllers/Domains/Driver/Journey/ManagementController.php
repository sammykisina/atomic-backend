<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Driver\Journey;

use Carbon\Carbon;
use Domains\Driver\Enums\LicenseStatuses;
use Domains\Driver\Models\Journey;
use Domains\Driver\Models\License;
use Domains\Driver\Notifications\JourneyNotification;
use Domains\Driver\Requests\CreateOrEditJourneyRequest;
use Domains\Driver\Requests\LocationRequest;
use Domains\Driver\Resources\JourneyResource;
use Domains\Driver\Services\JourneyService;
use Domains\Operator\Enums\ShiftStatuses;
use Domains\Shared\Enums\NotificationTypes;
use Domains\SuperAdmin\Models\Group;
use Domains\SuperAdmin\Services\TrainService;
use Illuminate\Http\Response;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\DB;
use JustSteveKing\StatusCode\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class ManagementController
{
    public function __construct(
        protected JourneyService $journeyService,
    ) {}


    /**
     * CREATE JOURNEY
     * @param CreateOrEditJourneyRequest $request
     * @return Response|HttpException
     */
    public function create(CreateOrEditJourneyRequest $request): Response|HttpException
    {
        DB::transaction(function () use ($request): Journey {
            if ($this->journeyService->activeJourney()) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'You already have an active journey.Contact operator for further inquires',
                );
            }

            $train = TrainService::getTrainById(
                train_id: $request->validated(key: "train_id"),
            );

            $group_with_train_origin = Group::query()
                ->with(relations: ['shifts' => function ($query): void {
                    $query->where('status', ShiftStatuses::CONFIRMED->value)
                        ->where('active', true)
                        ->with('user');
                }])
                ->whereJsonContains(column: 'stations', value: $train->origin->id)
                ->first();


            $currentTime = Carbon::now()->format('H:i');
            $shift = $group_with_train_origin->shifts->filter(function ($shift) use ($currentTime) {
                return
                    Carbon::now()->isSameDay(Carbon::parse($shift['day']))
                    && $currentTime >= $shift['from']
                    && $currentTime <= $shift['to'];
            })->first();

            if ( ! $shift) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'Please inform your system administrator there is no active shift currently set for the line you are trying to request a trip.',
                );
            }

            $journey = $this->journeyService->createJourney(
                journeyData: array_merge(
                    ['shifts' => [$shift->id]],
                    $request->validated(),
                ),
            );


            if ( ! $journey) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'Journey creation failed.',
                );
            }

            $shift->user->notify(new JourneyNotification(
                journey: $journey,
                type: NotificationTypes::JOURNEY_CREATED,
            ));

            return $journey;
        });

        return response(
            content: [
                'message' => 'Journey created successfully.',
            ],
            status: Http::CREATED(),
        );
    }


    /**
     * CONFIRM CURRENT TRAIN LOCATION
     * @param LocationRequest $request
     * @param Journey $journey
     * @return Response|HttpException
     */
    public function createLocation(LocationRequest $request, Journey $journey): Response | HttpException
    {
        if ( ! $this->journeyService->createTrainLocation(
            journey: $journey,
            locationData: $request->validated(),
        )) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Location confirmation failed.',
            );
        }

        return response(
            content: [
                'message' => 'Location confirmation successful.',
            ],
            status: Http::ACCEPTED(),
        );
    }

    /**
     * SHOW JOURNEY
     * @param Journey $journey
     * @return Response
     */
    public function show(Journey $journey): Response
    {
        return response(
            content: [
                'message' => 'Journey fetched successful.',
                'journey' => new JourneyResource(
                    resource: $journey,
                ),
            ],
            status: Http::ACCEPTED(),
        );
    }


    /**
     * CONFIRM LICENSE
     * @param Journey $journey
     * @param License $license
     * @return Response|HttpException
     */
    public function confirmLicense(Journey $journey, License $license, DatabaseNotification $notification): Response | HttpException
    {
        $result = DB::transaction(function () use ($journey, $license, $notification): bool {
            if (null !== $notification->read_at) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'License confirmation already.Incase of inquires please contact your system administration.',
                );
            }

            if ( ! $license->update([
                'status' => LicenseStatuses::CONFIRMED,
                'confirmed_at' => Carbon::now(),
            ])) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'License confirmation failed. Please try again',
                );
            }

            $license->issuer->notify(new JourneyNotification(
                journey: $journey,
                type: NotificationTypes::LICENSE_CONFIRMED,
            ));

            $notification->markAsRead();

            return true;
        });


        if ( ! $result) {
            return response(
                content: [
                    'message' => 'Something went wrong.Please try again.',
                ],
                status: Http::NOT_IMPLEMENTED(),
            );
        }

        return response(
            content: [
                'message' => 'License confirmed successfully. You can begin or continue with your journey.',
            ],
            status: Http::ACCEPTED(),
        );
    }

    /**
     * END JOURNEY
     * @param Journey $journey
     * @return Response|HttpException
     */
    public function endJourney(Journey $journey): Response|HttpException
    {
        $latest_journey_license = $journey->licenses()
            ->with(['paths' => function ($query): void {
                $query->latest()->limit(1); // Get only the latest path
            }])
            ->latest()
            ->first();

        if ($latest_journey_license) {
            $latest_path = $latest_journey_license->paths[0];

            if ($journey->destination_station_id !== $latest_path['destination_station_id']) {
                abort(
                    code: Http::UNAUTHORIZED(),
                    message: 'This journey can only be ended when you are at your destination.',
                );
            }
        }


        DB::transaction(function () use ($journey): void {
            if ( ! $journey->update([
                'status' => false,
            ])) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'Journey not ended.Please try again.',
                );
            }

            $licenses = $journey->licenses();
            if ($licenses->count() > 0) {
                foreach ($licenses as $license) {
                    $license->update([
                        'status' => LicenseStatuses::USED,
                    ]);
                }
            }
        });

        return response(
            content: [
                'message' => 'Journey ended successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }
}
