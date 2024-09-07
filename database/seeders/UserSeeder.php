<?php

declare(strict_types=1);

namespace Database\Seeders;

use Domains\Shared\Enums\ModelStatuses;
use Domains\Shared\Enums\UserTypes;
use Domains\Shared\Enums\WorkStatuses;
use Domains\Shared\Models\Department;
use Domains\Shared\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class UserSeeder extends Seeder
{
    /** @return void */
    public function run(): void
    {
        $it_department = Department::factory()->create([
            'name' => 'IT',
            'description' => 'Information Technology Department',
        ]);

        /**
         * SEED  SYSTEM ADMIN
         */
        User::factory()->create([
            'first_name' => 'Brian',
            'last_name' => 'Mwangi',
            'email' => 'info@vasmobile.africa',
            'phone' => '+254711637755',
            'employee_id' => '711637755',
            'national_id' => '711637755',
            'type' => UserTypes::SYSTEM_ADMIN->value,
            'department_id' => $it_department->id,
            'image_url' => 'https://vasmobile.africa/images/home/vasmobile%20logo-01.png',
            'status' => ModelStatuses::ACTIVE->value,
            'work_status' => WorkStatuses::ON_THE_JOB->value,
            'password' => Hash::make(
                value: 'vasmobile',
            ),
            'is_admin' => true,
        ]);

        /**
         * SEED SUPER ADMIN
         */
        User::factory()->create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'superadmin@atomic.com',
            'phone' => '+254700000000',
            'employee_id' => '700000000',
            'national_id' => '700000000',
            'type' => UserTypes::SUPER_ADMIN->value,
            'department_id' => $it_department->id,
            'status' => ModelStatuses::ACTIVE->value,
            'work_status' => WorkStatuses::ON_THE_JOB->value,
            'password' => Hash::make(
                value: 'superadmin',
            ),
            'is_admin' => true,
        ]);
    }
}
