<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use App\Rules\SchoolEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class CreateUser extends Command
{
    protected $signature = 'campus:create-user
        {--admin : Create an administrator instead of a student/faculty account}
        {--name= : Full name}
        {--email= : Email address}
        {--school-id= : Student / employee ID (optional)}
        {--temporary : Force the user to change the password at first login}';

    protected $description = 'Create a user account (the way to create the very first administrator)';

    public function handle(): int
    {
        $isAdmin = (bool) $this->option('admin');

        $name = $this->option('name') ?: text('Full name', required: true);
        $email = strtolower((string) ($this->option('email') ?: text('Email address', required: true)));
        $schoolId = $this->option('school-id') ?: null;

        // Passwords are always asked interactively so they never end up in shell history.
        $plain = password('Password', required: true, hint: 'At least '.config('campus.password_min_length').' characters, letters and numbers');

        // School-domain rule applies to student/faculty accounts. Admins may use any valid email.
        $emailRules = ['required', 'email', 'max:255', 'unique:users,email'];
        if (! $isAdmin) {
            $emailRules[] = new SchoolEmail;
        }

        $validator = Validator::make(
            ['name' => $name, 'email' => $email, 'school_id' => $schoolId, 'password' => $plain],
            [
                'name' => ['required', 'string', 'max:150'],
                'email' => $emailRules,
                'school_id' => ['nullable', 'string', 'max:50', 'unique:users,school_id'],
                'password' => ['required', Password::defaults()],
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->components->error($error);
            }

            return self::FAILURE;
        }

        $user = new User($validator->validated());
        $user->role = $isAdmin ? UserRole::Admin : UserRole::User;
        $user->is_active = true;
        $user->must_change_password = (bool) $this->option('temporary');
        $user->save();

        $this->components->info(sprintf('%s created: %s <%s>', $user->role->label(), $user->name, $user->email));

        return self::SUCCESS;
    }
}
