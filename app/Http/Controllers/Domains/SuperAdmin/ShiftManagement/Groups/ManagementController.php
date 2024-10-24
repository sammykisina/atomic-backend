<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SuperAdmin\ShiftManagement\Groups;

use Domains\SuperAdmin\Models\Group;
use Domains\SuperAdmin\Requests\ShiftManagement\GroupRequest;
use Domains\SuperAdmin\Resources\ShiftManagement\GroupResource;
use Domains\SuperAdmin\Services\ShiftManagement\GroupService;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;

final class ManagementController
{
    /** @param GroupService $groupService */
    public function __construct(
        protected GroupService $groupService,
    ) {}


    /**
     * CREATE GROUP
     * @param GroupRequest $request
     * @return Response
     */
    public function create(GroupRequest $request): Response
    {
        $group = $this->groupService->createGroup(
            groupData: $request->validated(),
        );

        if ( ! $group) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Group creation failed.Please try again.',
            );
        }

        return response(
            content: [
                'group' => new GroupResource(
                    resource: $group,
                ),
                'message' => 'Group created successfully.',
            ],
            status: Http::CREATED(),
        );
    }


    /**
     * EDIT GROUP
     * @param GroupRequest $request
     * @param Group $group
     * @return Response
     */
    public function edit(GroupRequest $request, Group $group): Response
    {
        $edited = $this->groupService->editGroup(
            groupData: $request->validated(),
            group: $group,
        );

        if ( ! $edited) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Group update failed.Please try again.',
            );
        }

        return response(
            content: [
                'message' => 'Group updated successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }

    /**
     * SHOW GROUP
     * @param Group $group
     * @return Response
     */
    public function show(Group $group): Response
    {
        $group = GroupService::getGroupById(
            group_id: $group->id,
        );

        return response(
            content: [
                'message' => 'Group fetched successfully.',
                'group' => new GroupResource(
                    resource: $group,
                ),
            ],
            status: Http::ACCEPTED(),
        );
    }

    /**
     * DELETE GROUP
     * @param Group $group
     * @return Response
     */
    public function delete(Group $group): Response
    {
        $deleted = $group->delete();

        if ( ! $deleted) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Group deletion failed.Please try again.',
            );
        }

        return response(
            content: [
                'message' => 'Group deleted successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }
}
