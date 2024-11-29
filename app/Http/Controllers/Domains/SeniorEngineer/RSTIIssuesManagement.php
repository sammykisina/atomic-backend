<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SeniorEngineer;

use Domains\SeniorEngineer\Enums\SpeedSuggestionStatuses;
use Domains\SeniorEngineer\Models\Speed;
use Domains\SeniorEngineer\Requests\ApproveSpeedRestrictionRequest;
use Domains\SeniorEngineer\Requests\RevertSpeedRestrictionRequest;
use Domains\SeniorEngineer\Requests\SpeedRestrictionRequest;
use Domains\SeniorEngineer\Services\SpeedService;
use Domains\SeniorTrackInspector\Models\IssueArea;
use Domains\Shared\Enums\AtomikLogsTypes;
use Domains\Shared\Services\AtomikLogService;
use Domains\SuperAdmin\Services\SectionService;
use Domains\SuperAdmin\Services\StationService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class RSTIIssuesManagement
{
    public function __construct(
        public SpeedService $speedService,
    ) {}


    /**
     * APPROVE SPEED RESTRICTION
     * @param IssueArea $issueArea
     * @return  HttpException|Response
     */
    public function approveSpeedRestrictionSuggestion(ApproveSpeedRestrictionRequest $request, IssueArea $issueArea): Response | HttpException
    {
        $section = null;
        $station = null;

        $overlappingModel = SpeedService::checkOverlap(
            startKm: $issueArea->issue->issue_kilometer,
            endKm: $request->validated(key: 'end_kilometer'),
            line_id: $issueArea->line_id,
        );

        if ($overlappingModel) {
            abort(
                code: Http::UNPROCESSABLE_ENTITY(),
                message: 'The speed restriction will overlaps with another speed restriction. There is a speed restriction of ' . $overlappingModel->speed . " at KM " . $overlappingModel->start_kilometer . ' to KM ' . $overlappingModel->end_kilometer . '. You can delete the exiting speed restriction or adjust its KMs in the Speed Restriction module before you create a new one.',
            );
        }

        if ($issueArea->station_id) {
            $station = StationService::getStationById(
                station_id: $issueArea->station_id,
            );

            $speed = $this->speedService->createSpeed(speedData: array_merge(
                [
                    'areable_id' => $station->id,
                    'areable_type' => get_class(object: $station),
                    'speed' => $issueArea->speed_suggestion,
                    'start_kilometer' => $issueArea->issue->issue_kilometer,
                    'start_kilometer_latitude' => $issueArea->issue->latitude,
                    'start_kilometer_longitude' => $issueArea->issue->longitude,
                    'line_id' => $station->line_id,
                ],
                $request->validated(),
            ));

            $issueArea->update(attributes: [
                'speed_suggestion_status' => SpeedSuggestionStatuses::APPROVED,
            ]);

            AtomikLogService::createAtomicLog(atomikLogData: [
                'type' => AtomikLogsTypes::SPEED_RESTRICTION_ADDED,
                'resourceble_id' => $speed->id,
                'resourceble_type' => get_class(object: $speed),
                'actor_id' => Auth::id(),
                'receiver_id' => Auth::id(),
                'current_location' => $station->name . " KM " . $speed->start_kilometer . ' To KM ' . $speed->end_kilometer,
            ]);

            return response(
                content: [
                    'message' => 'Speed restriction suggestion  approved successfully. Please state ',
                ],
                status: Http::ACCEPTED(),
            );

        }

        if ($issueArea->section_id) {
            $section = SectionService::getSectionById(
                section_id: $issueArea->section_id,
            );

            $speed = $this->speedService->createSpeed(speedData: array_merge(
                [
                    'areable_id' => $section->id,
                    'areable_type' => get_class(object: $section),
                    'speed' => $issueArea->speed_suggestion,
                    'start_kilometer' => $issueArea->issue->issue_kilometer,
                    'start_kilometer_latitude' => $issueArea->issue->latitude,
                    'start_kilometer_longitude' => $issueArea->issue->longitude,
                    'line_id' => $section->line_id,
                ],
                $request->validated(),
            ));
            $issueArea->update(attributes: [
                'speed_suggestion_status' => SpeedSuggestionStatuses::APPROVED,
            ]);

            AtomikLogService::createAtomicLog(atomikLogData: [
                'type' => AtomikLogsTypes::SPEED_RESTRICTION_ADDED,
                'resourceble_id' => $speed->id,
                'resourceble_type' => get_class(object: $speed),
                'actor_id' => Auth::id(),
                'receiver_id' => Auth::id(),
                'current_location' => $section->start_name . " - " . $section->end_name . " KM " . $speed->start_kilometer . ' To KM ' . $speed->end_kilometer,
            ]);


            return response(
                content: [
                    'message' => 'Speed restriction suggestion  approved successfully',
                ],
                status: Http::ACCEPTED(),
            );
        }

        return response(
            content: [
                'message' => 'Something went wrong. Speed restriction suggestion not approved',
            ],
            status: Http::NOT_ACCEPTABLE(),
        );

    }

    /**
     * ADD SPEED RESTRICTION
     * @param IssueArea $issueArea
     * @return  HttpException|Response
     */
    public function addSpeedRestriction(SpeedRestrictionRequest $request, IssueArea $issueArea): Response | HttpException
    {
        $station = null;
        $section = null;

        if ($issueArea->station_id) {
            $station = StationService::getStationById(
                station_id: $issueArea->station_id,
            );

            $speed = $this->speedService->createSpeed(speedData: array_merge(
                [
                    'areable_id' => $station->id,
                    'areable_type' => get_class(object: $station),
                    'speed' => $request->validated(key: 'proposed_speed'),
                    'start_kilometer' => $issueArea->issue->issue_kilometer,
                    'start_kilometer_latitude' => $issueArea->issue->latitude,
                    'start_kilometer_longitude' => $issueArea->issue->longitude,
                    'line_id' => $station->line_id,
                    'end_kilometer' => $request->validated('end_kilometer'),
                    'end_kilometer_latitude' => $request->validated('end_kilometer_latitude'),
                    'end_kilometer_longitude' => $request->validated('end_kilometer_longitude'),
                ],
            ));


            $issueArea->update(attributes: [
                'speed_suggestion_status' => SpeedSuggestionStatuses::CHANGED,
                'proposed_speed_comment' => $request->validated('proposed_speed_comment'),
                'proposed_speed' => $request->validated('proposed_speed'),
            ]);

            AtomikLogService::createAtomicLog(atomikLogData: [
                'type' => AtomikLogsTypes::SPEED_RESTRICTION_ADDED,
                'resourceble_id' => $speed->id,
                'resourceble_type' => get_class(object: $speed),
                'actor_id' => Auth::id(),
                'receiver_id' => Auth::id(),
                'current_location' => $station->name . " KM " . $speed->start_kilometer . ' To KM ' . $speed->end_kilometer,
            ]);

            return response(
                content: [
                    'message' => 'Speed restriction added successfully.',
                ],
                status: Http::ACCEPTED(),
            );
        }

        if ($issueArea->section_id) {
            $section = SectionService::getSectionById(
                section_id: $issueArea->section_id,
            );

            $issueArea->update(attributes: [
                'speed_suggestion_status' => SpeedSuggestionStatuses::CHANGED,
                'proposed_speed_comment' => $request->validated('proposed_speed_comment'),
                'proposed_speed' => $request->validated('proposed_speed'),
            ]);

            $speed = $this->speedService->createSpeed(speedData: array_merge(
                [
                    'areable_id' => $section->id,
                    'areable_type' => get_class(object: $section),
                    'speed' => $request->validated(key: 'proposed_speed'),
                    'start_kilometer' => $issueArea->issue->issue_kilometer,
                    'start_kilometer_latitude' => $issueArea->issue->latitude,
                    'start_kilometer_longitude' => $issueArea->issue->longitude,
                    'line_id' => $section->line_id,
                    'end_kilometer' => $request->validated('end_kilometer'),
                    'end_kilometer_latitude' => $request->validated('end_kilometer_latitude'),
                    'end_kilometer_longitude' => $request->validated('end_kilometer_longitude'),
                ],
            ));

            AtomikLogService::createAtomicLog(atomikLogData: [
                'type' => AtomikLogsTypes::SPEED_RESTRICTION_ADDED,
                'resourceble_id' => $speed->id,
                'resourceble_type' => get_class(object: $speed),
                'actor_id' => Auth::id(),
                'receiver_id' => Auth::id(),
                'current_location' => $section->start_name . " - " . $section->end_name . " KM " . $speed->start_kilometer . ' To KM ' . $speed->end_kilometer,
            ]);

            return response(
                content: [
                    'message' => 'Speed restriction added successfully.',
                ],
                status: Http::ACCEPTED(),
            );
        }

        return response(
            content: [
                'message' => 'Something went wrong. Speed restriction was not added',
            ],
            status: Http::NOT_ACCEPTABLE(),
        );

    }


    /**
     * REVERT SPEED RESTRICTION
     * @param IssueArea $issueArea
     * @return  HttpException|Response
     */
    public function revertSpeedRestriction(RevertSpeedRestrictionRequest $request, IssueArea $issueArea): Response | HttpException
    {
        $section = null;
        $station = null;


        if ($issueArea->station_id) {
            $station = StationService::getStationById(
                station_id: $issueArea->station_id,
            );

            $station->update(attributes: [
                'speed' => $request->validated('reverted_speed'),
            ]);

            $issueArea->update(attributes: [
                'speed_suggestion_status' => SpeedSuggestionStatuses::REVERTED,
                'reverted_speed_comment' => $request->validated('reverted_speed_comment'),
                'reverted_speed' => $request->validated('reverted_speed'),
            ]);

            return response(
                content: [
                    'message' => 'Speed restriction reverted successfully.',
                ],
                status: Http::ACCEPTED(),
            );
        }

        if ($issueArea->section_id) {
            $section = SectionService::getSectionById(
                section_id: $issueArea->section_id,
            );

            $section->update(attributes: [
                'speed' => $request->validated('reverted_speed'),
            ]);

            $issueArea->update(attributes: [
                'speed_suggestion_status' => SpeedSuggestionStatuses::REVERTED,
                'reverted_speed_comment' => $request->validated('reverted_speed_comment'),
                'reverted_speed' => $request->validated('reverted_speed'),
            ]);

            return response(
                content: [
                    'message' => 'Speed restriction reverted successfully.',
                ],
                status: Http::ACCEPTED(),
            );
        }


        return response(
            content: [
                'message' => 'Something went wrong. Speed restriction was not added',
            ],
            status: Http::NOT_ACCEPTABLE(),
        );

    }
}
