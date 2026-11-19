<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed initial demo/test users into Supabase.
     */
    public function run(): void
    {
        // 1. Administrator Account
        $admin = User::firstOrNew(['email' => 'admin@school.edu']);
        $admin->name = 'Campus Admin';
        $admin->password = Hash::make('Password123');
        $admin->role = UserRole::Admin;
        $admin->is_active = true;
        $admin->must_change_password = false;
        $admin->save();

        // 2. Student / Faculty Account
        $student = User::firstOrNew(['email' => 'student@school.edu']);
        $student->name = 'Student User';
        $student->department = 'College of Computer Studies';
        $student->phone = '+63 912 345 6789';
        $student->password = Hash::make('Password123');
        $student->role = UserRole::User;
        $student->is_active = true;
        $student->must_change_password = false;
        $student->save();
    }
}
