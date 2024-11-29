<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SuperAdmin\Lines;

use Illuminate\Http\Request;

final class CreateLineValidationPatternController
{
    public function __invoke(Request $request): void
    {
        // Fetch all lines with their related stations, loops, and sections
        // $lines = Line::with(['stations.loops', 'stations.section'])->get();

        // // Initialize the result array
        // $result = [];

        // // Loop through each line
        // foreach ($lines as $line) {
        //     $line_pattern = [
        //         'line_id' => $line->id,
        //         'pattern' => [],
        //     ];

        //     // Sort stations by position_from_line in ascending order
        //     $sortedStations = $line->stations->sortBy('position_from_line');

        //     // Loop through each sorted station in the line
        //     foreach ($sortedStations as $station) {
        //         // Add the station details as an individual object
        //         $line_pattern['pattern'][] = [
        //             'id' => $station->id,
        //             'name' => $station->name,
        //             'type' => 'STATION',
        //         ];

        //         // If a loop exists, add it as a separate individual object
        //         if ($station->loops->isNotEmpty()) {
        //             $loop = $station->loops->first(); // Assuming the first loop if multiple exist

        //             $line_pattern['pattern'][] = [
        //                 'id' => $loop->id,
        //                 'type' => 'LOOP',
        //             ];
        //         }

        //         // If a section exists, add it as a separate individual object
        //         if ($station->section) {
        //             $line_pattern['pattern'][] = [
        //                 'id' => $station->section->id, // Optionally include section ID
        //                 'name' => $station->section->start_name . '-' . $station->section->end_name,
        //                 'type' => 'SECTION',
        //             ];
        //         }
        //     }

        //     Pattern::updateOrCreate(
        //         attributes: ['line_id' => $line->id],
        //         values: ['pattern' => $line_pattern['pattern']], // Store the pattern as JSON
        //     );

        //     // Add the line pattern to the result array
        //     $result[] = $line_pattern;
        // }

        // return $result;
    }
}
