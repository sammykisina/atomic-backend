<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Shared;

use Carbon\Carbon;
use Domains\Shared\Models\User;
use Domains\Shared\Resources\NotificationResource;
use Illuminate\Http\Response;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

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

    /**
     * GET READ NOTIFICATIONS
     * @param User $user
     * @return Response
     */
    public function readNotifications(User $user): Response
    {
        if ($user->id !== Auth::id()) {
            abort(
                code: Http::UNAUTHORIZED(),
                message: 'Unauthorized',
            );
        }

        $notifications = DatabaseNotification::query()
            ->where('notifiable_id', '=', $user->id)
            ->where('read_at', '!=', null)
            ->get();

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

    /**
     * MARK NOTIFICATION AS READ
     * @param DatabaseNotification $notification
     * @return HttpException|Response
     */
    public function markAsRead(DatabaseNotification $notification): HttpException | Response
    {
        if ( ! $notification->update([
            'read_at' => Carbon::now(),
        ])) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Notification not marked as read.Please try again.',
            );
        }

        return Response(
            content: [
                'message' => 'Notification marked as read successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }
}
