<?php

/*
|--------------------------------------------------------------------------
| Campus Lost & Found settings
|--------------------------------------------------------------------------
|
| School-specific settings live here and are controlled by environment
| variables, so changing school, domain or sign-in method never needs a code change.
|
*/

return [

    'school_name' => env('SCHOOL_NAME', 'Your School Name'),

    /*
    | How do accounts get created?
    |   admin_managed : only administrators create accounts (recommended; no public sign-up)
    |   self_register : anyone with an allowed school email can sign up
    */
    'auth_mode' => env('AUTH_MODE', 'admin_managed'),

    /*
    | School email domains allowed for Student/Faculty accounts, comma separated,
    | e.g. "school.edu.ph,students.school.edu.ph".
    | An EMPTY list blocks every student/faculty email (fail closed).
    | Use "*" only for local testing to allow any email.
    */
    'allowed_email_domains' => array_values(array_filter(array_map(
        'trim',
        explode(',', strtolower((string) env('ALLOWED_EMAIL_DOMAINS', '')))
    ))),

    // Minimum password length for all accounts.
    'password_min_length' => (int) env('PASSWORD_MIN_LENGTH', 8),

];
