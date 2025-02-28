<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Services;

use Domains\SuperAdmin\Models\Section;

final class SectionService
{
    /**
     * GET SECTION BY ID
     * @param int $section_id
     * @return Section|null
     */
    public static function getSectionById(int $section_id): ?Section
    {
        return Section::query()->where('id', $section_id)->first();
    }
    /**
     * CREATE SECTION
     * @param array $sectionData
     * @return Section
     */
    public function createSection(array $sectionData): Section
    {
        $attributes = [
            'start_name' => $sectionData['start_name'],
            'end_name' => $sectionData['end_name'],
            'start_kilometer' => $sectionData['start_kilometer'],
            'end_kilometer' => $sectionData['end_kilometer'],
            'start_latitude' => $sectionData['start_latitude'],
            'start_longitude' => $sectionData['start_longitude'],
            'end_latitude' => $sectionData['end_latitude'],
            'end_longitude' => $sectionData['end_longitude'],
            'line_id' => $sectionData['line_id'],
            'number_of_kilometers_to_divide_section_to_subsection' => $sectionData['number_of_kilometers_to_divide_section_to_subsection'],
            'section_type' => $sectionData['section_type'],
        ];

        if (isset($sectionData['station_id'])) {
            $attributes['station_id'] = $sectionData['station_id'];
        }

        return Section::query()->create($attributes);
    }

    /**
     * EDIT SECTION
     * @param array $updatedSectionData
     * @param Section $section
     * @return bool
     */
    public function editSection(array $updatedSectionData, Section $section): bool
    {
        $attributes = [
            'start_name' => $updatedSectionData['start_name'],
            'end_name' => $updatedSectionData['end_name'],
            'start_kilometer' => $updatedSectionData['start_kilometer'],
            'end_kilometer' => $updatedSectionData['end_kilometer'],
            'start_latitude' => $updatedSectionData['start_latitude'],
            'start_longitude' => $updatedSectionData['start_longitude'],
            'end_latitude' => $updatedSectionData['end_latitude'],
            'end_longitude' => $updatedSectionData['end_longitude'],
            'line_id' => $updatedSectionData['line_id'],
            'number_of_kilometers_to_divide_section_to_subsection' => $updatedSectionData['number_of_kilometers_to_divide_section_to_subsection'],
            'section_type' => $updatedSectionData['section_type'],
        ];

        if (isset($updatedSectionData['station_id'])) {
            $attributes['station_id'] = $updatedSectionData['station_id'];
        }

        return $section->update($attributes);
    }
}
