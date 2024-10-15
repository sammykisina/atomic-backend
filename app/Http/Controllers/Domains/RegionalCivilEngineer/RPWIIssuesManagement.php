<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\RegionalCivilEngineer;

use Domains\PermanentWayInspector\Models\IssueArea;
use Domains\RegionalCivilEngineer\Enums\SpeedSuggestionStatuses;
use Domains\RegionalCivilEngineer\Requests\SpeedRestrictionRequest;
use Domains\SuperAdmin\Models\Section;
use Domains\SuperAdmin\Models\Station;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class RPWIIssuesManagement
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

        if ($issueArea->section_id) {
            $section = Section::query()->where('id', $issueArea->section_id)->first();
        }

        if ($issueArea->station_id) {
            $station = Station::query()->where('id', $issueArea->station_id)->first();
        }

        if ($section) {
            $section->update([
                'speed' => $issueArea->speed_suggestion,
            ]);

            $issueArea->update([
                'speed_suggestion_status' => SpeedSuggestionStatuses::APPROVED,
            ]);

            return response(
                content: [
                    'message' => 'Speed restriction suggestion  approved successfully',
                ],
                status: Http::ACCEPTED(),
            );
        }

        if ($station) {
            $station->update([
                'speed' => $issueArea->speed_suggestion,
            ]);

            $issueArea->update([
                'speed_suggestion_status' => SpeedSuggestionStatuses::APPROVED,
            ]);

            return response(
                content: [
                    'message' => 'Speed restriction suggestion  approved successfully.',
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
        $section = null;
        $station = null;

        if ($issueArea->section_id) {
            $section = Section::query()->where('id', $issueArea->section_id)->first();
        }

        if ($issueArea->station_id) {
            $station = Station::query()->where('id', $issueArea->station_id)->first();
        }

        if ($section) {
            $section->update([
                'speed' => $request->validated('proposed_speed'),
            ]);

            $issueArea->update([
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

        if ($station) {
            $station->update([
                'speed' => $request->validated('proposed_speed'),
            ]);

            $issueArea->update([
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

        return response(
            content: [
                'message' => 'Something went wrong. Speed restriction was not added',
            ],
            status: Http::NOT_ACCEPTABLE(),
        );

    }
}
