<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Shared;

use Domains\Shared\Models\User;
use Domains\Shared\Resources\NotificationResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;

final class NotificationController
{
    /**
     * GET UNREAD NOTIFICATIONS
     * @param User $user
     * @return Response
     */
    public function unreadNotifications(User $user): Response
    {
        if ($user->id !== Auth::id()) {
            abort(
                code: Http::UNAUTHORIZED(),
                message: 'Unauthorized',
            );
        }

        $notifications = $user->unreadNotifications;

        return response(
            content: [
                'message' => 'Notifications fetched successfully.',
                'notifications' => NotificationResource::collection(
                    resource: $notifications,
                ),
            ],
            status: Http::OK(),
        );
    }

    /** */
    public function readNotifications(User $user): Response
    {
        // if($user->id !== Auth::id()){
        //    abort(
        //             code: Http::UNAUTHORIZED(),
        //             message: 'Unauthorized',
        //         );
        // }

        $notifications = $user->readNotifications();

        return response(
            content: [
                'message' => 'Notifications fetched successfully.',
                'notifications' => NotificationResource::collection(
                    resource: $notifications,
                ),
            ],
            status: Http::OK(),
        );
    }
}
