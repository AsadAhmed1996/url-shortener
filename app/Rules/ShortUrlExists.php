<?php

namespace App\Rules;

use App\Models\ShortUrl;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ShortUrlExists implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $shortUrlExists = ShortUrl::where('short_url', $value)->count();
        if (!$shortUrlExists) {
            $fail('The :attribute provided is not a url that has been shortened by this service.');
        }
    }
}
