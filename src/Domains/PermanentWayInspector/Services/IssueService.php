<?php

declare(strict_types=1);

namespace Domains\PermanentWayInspector\Services;

use Domains\Inspector\Models\Issue;
use Domains\PermanentWayInspector\Models\Assignment;

final class IssueService
{
    /**
     * ASSIGN GANG MEN TO ISSUE
     * @param Issue $issue
     * @param array $gang_men
     * @return void
     */
    public function assignIssueToGangMen(Issue $issue, array $gang_men): Assignment
    {
        return Assignment::query()->updateOrCreate(
            ['issue_id' => $issue->id],
            [
                'gang_men' => $gang_men
            ],
        );
    }
}
