<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SuperAdmin\Stations;

use App\Actions\Superadmin\Stations\MakeSectionPartOfTripLine;
use Domains\SuperAdmin\Models\Section;
use Domains\SuperAdmin\Models\Station;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class MakeSectionPartOfTripLineController
{
    public function __invoke(
        Request $request,
        string $station,
        string $section,
        MakeSectionPartOfTripLine $makeSectionPartOfTripLine,
    ): Response | HttpException {
        $station = Station::query()->where('id', $station)->firstOrFail();
        $section = Section::query()->where('id', $section)->with('station')->firstOrFail();

        if ( ! $makeSectionPartOfTripLine->handle(station: $station, section: $section)) {
            abort(code: 417, message: 'Failed to make section part of trip-line');
        }

        return response(content: ['message' => 'Section made part of trip-line'], status: 200);
    }
}
