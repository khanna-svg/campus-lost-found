<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Accepts only emails that belong to one of the school domains configured in
 * config/campus.php (ALLOWED_EMAIL_DOMAINS).
 */
class SchoolEmail implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $domains = config('campus.allowed_email_domains', []);

        if ($domains === []) {
            $fail('No school email domains are configured. Please contact the administrator.');

            return;
        }

        if (in_array('*', $domains, true)) {
            return;
        }

        $domain = str_contains((string) $value, '@')
            ? mb_strtolower(substr(strrchr((string) $value, '@'), 1))
            : '';

        if (! in_array($domain, $domains, true)) {
            $fail('Please use your school email address (@'.implode(', @', $domains).').');
        }
    }
}
