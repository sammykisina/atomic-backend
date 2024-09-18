<?php

declare(strict_types=1);

namespace Domains\Shared\Services;

use Carbon\Carbon;
use Domains\Shared\Enums\OtpTypes;
use Domains\Shared\Mails\OtpMail;
use Domains\Shared\Models\Otp;
use Domains\Shared\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use JustSteveKing\StatusCode\Http;

final class AuthService
{
    // LOGIN USER
    public function login(array $data): ?User
    {
        $user = User::query()->where('email', $data['email'])->first();

        if ($user && Hash::check(value: $data['password'], hashedValue: $user->password)) {
            return $user;
        }

        return null;
    }

    // GET USER BY EMAIL
    public function getUserByEmail(string $email): User
    {
        return User::query()->where('email', $email)->first();
    }

    // GENERATE AND SEND OTP VIA MAIL
    public function opt(User $user, string $type = OtpTypes::VERIFICATION->value): void
    {
        $tries = 3;
        $time = Carbon::now()->subMinutes(30); // 30 mins in the past
        $count = $otp = Otp::query()->where([
            'user_id' => $user->id,
            'active' => true,
            'type' => $type,
        ])->where('created_at', '>=', $time)->count();

        // if ($count >= $tries) {
        //     abort(
        //         code: Http::UNPROCESSABLE_ENTITY(),
        //         message: 'Too many attempts. Try again in 30 minutes.',
        //     );
        // }

        $code = random_int(min: 100000, max: 999999);

        $otp = Otp::create([
            'type' => $type,
            'code' => $code,
            'active' => true,
            'user_id' => $user->id,
        ]);

        Mail::to($user)->send(
            new OtpMail(
                user: $user,
                otp: $otp,
            ),
        );
    }

    // RESET PASSWORD
    public function resetPassword(User $user, array $data): User
    {
        $otp = Otp::query()->where([
            'code' => $data['otp'],
            'user_id' => $user->id,
            'active' => true,
            'type' => OtpTypes::PASSWORD_RESET->value,
        ])->first();

        if ( ! $otp) {
            abort(
                code: Http::UNPROCESSABLE_ENTITY(),
                message: 'Invalid OTP code.',
            );
        }

        $user->password = Hash::make($data['password']);
        $user->updated_at = Carbon::now();
        $user->update();

        $otp->update([
            'active' => false,
            'updated_at' => Carbon::now(),
        ]);

        return $user;
    }
}
