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
        $admin = User::firstOrNew(['email' => 'admin@sac.edu.ph']);
        $admin->name = 'Campus Administrator';
        $admin->password = Hash::make('ryan123');
        $admin->role = UserRole::Admin;
        $admin->is_active = true;
        $admin->must_change_password = false;
        $admin->school_id = 'SAC-ADMIN-01';
        $admin->department = 'Administration';
        $admin->save();

        // 2. Student / Faculty Account
        $student = User::firstOrNew(['email' => 'figueroaryan@sac.edu.ph']);
        $student->name = 'Ryan Figueroa';
        $student->department = 'College of Computer Studies';
        $student->phone = '+63 963 804 0781';
        $student->password = Hash::make('ryan123');
        $student->role = UserRole::User;
        $student->is_active = true;
        $student->must_change_password = false;
        $student->school_id = 'SAC-STUDENT-01';
        $student->save();
    }
}
