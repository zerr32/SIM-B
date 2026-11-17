<?php

namespace App\Support;

class Phone
{
    public static function normalize(mixed $number): ?string
    {
        if (!is_string($number) || $number === '') return null;
        $d = preg_replace('/\D+/', '', $number);
        if (str_starts_with($d, '0')) $d = '62' . substr($d, 1);
        elseif (str_starts_with($d, '8')) $d = '62' . $d;
        return $d ?: null;
    }
}
