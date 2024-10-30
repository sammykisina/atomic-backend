<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SeniorEngineer;

use Domains\SeniorEngineer\Enums\SpeedSuggestionStatuses;
use Domains\SeniorEngineer\Requests\RevertSpeedRestrictionRequest;
use Domains\SeniorEngineer\Requests\SpeedRestrictionRequest;
use Domains\SeniorTrackInspector\Models\IssueArea;
use Domains\SuperAdmin\Services\SectionService;
use Domains\SuperAdmin\Services\StationService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use JustSteveKing\StatusCode\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class RSTIIssuesManagement
{
    /**
     * APPROVE SPEED RESTRICTION
     * @param IssueArea $issueArea
     * @return  HttpException|Response
     */
    public function approveSpeedRestrictionSuggestion(IssueArea $issueArea): Response | HttpException
    {
        $section = null;
        $station = null;

        Log::channel('custom')->info(message: '=== APPROVING SPEED RESTRICTION ===');
        if ($issueArea->station_id) {
            $station = StationService::getStationById(
                station_id: $issueArea->station_id,
            );

            $station->update(attributes: [
                'speed' => $issueArea->speed_suggestion,
            ]);


            $issueArea->update(attributes: [
                'speed_suggestion_status' => SpeedSuggestionStatuses::APPROVED,
            ]);

            Log::channel(channel: 'custom')->info(
                message: 'Speed Info',
                context: [
                    'speed' => $issueArea->speed_suggestion,
                    'station' => $issueArea->station_id,
                ],
            );

            return response(
                content: [
                    'message' => 'Speed restriction suggestion  approved successfully.',
                ],
                status: Http::ACCEPTED(),
            );

        }

        if ($issueArea->section_id) {
            $section = SectionService::getSectionById(
                section_id: $issueArea->section_id,
            );

            $section->update(attributes: [
                'speed' => $issueArea->speed_suggestion,
            ]);

            $issueArea->update(attributes: [
                'speed_suggestion_status' => SpeedSuggestionStatuses::APPROVED,
            ]);

            Log::channel(channel: 'custom')->info(
                message: 'Speed Info',
                context: [
                    'speed' => $issueArea->speed_suggestion,
                    'section' => $issueArea->section_id,
                ],
            );

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

            $station->update(attributes: [
                'speed' => $request->validated('proposed_speed'),
            ]);

            $issueArea->update(attributes: [
                'speed_suggestion_status' => SpeedSuggestionStatuses::CHANGED,
                'proposed_speed_comment' => $request->validated('proposed_speed_comment'),
                'proposed_speed' => $request->validated('proposed_speed'),
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

            $section->update(attributes: [
                'speed' => $request->validated('proposed_speed'),
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
