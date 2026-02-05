<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class StrongPassword implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (strlen($value) < 8) {
            $fail('Password must be at least 8 characters.');
        }

        if (!preg_match('/[A-Z]/', $value)) {
            $fail('Password must contain at least one uppercase letter.');
        }

        if (!preg_match('/[a-z]/', $value)) {
            $fail('Password must contain at least one lowercase letter.');
        }

        if (!preg_match('/[0-9]/', $value)) {
            $fail('Password must contain at least one number.');
        }

        if (!preg_match('/[\W_]/', $value)) {
            $fail('Password must contain at least one special character.');
        }

        if (preg_match('/012|123|234|345|456|567|678|789/', $value)) {
            $fail('Password cannot contain sequential numbers.');
        }

        if (preg_match('/abc|bcd|cde|def|efg|fgh|ghi|hij|ijk/i', $value)) {
            $fail('Password cannot contain sequential letters.');
        }
    }
}

