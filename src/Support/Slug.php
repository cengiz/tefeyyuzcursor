<?php
declare(strict_types=1);

namespace Tefeyyuz\Support;

final class Slug
{
    public static function make(string $text): string
    {
        $map = [
            'ı' => 'i', 'İ' => 'i', 'ş' => 's', 'Ş' => 's', 'ğ' => 'g', 'Ğ' => 'g',
            'ü' => 'u', 'Ü' => 'u', 'ö' => 'o', 'Ö' => 'o', 'ç' => 'c', 'Ç' => 'c',
            'â' => 'a', 'î' => 'i', 'û' => 'u',
        ];
        $text = strtr(mb_strtolower($text, 'UTF-8'), $map);
        $text = preg_replace('/[^a-z0-9]+/u', '-', $text) ?? $text;
        return trim($text, '-');
    }

    public static function fold(string $text): string
    {
        $map = [
            'ı' => 'i', 'İ' => 'i', 'ş' => 's', 'Ş' => 's', 'ğ' => 'g', 'Ğ' => 'g',
            'ü' => 'u', 'Ü' => 'u', 'ö' => 'o', 'Ö' => 'o', 'ç' => 'c', 'Ç' => 'c',
            'â' => 'a', 'î' => 'i', 'û' => 'u',
        ];
        $text = strtr(mb_strtolower($text, 'UTF-8'), $map);
        $text = preg_replace('/[^a-z0-9\s]+/u', ' ', $text) ?? $text;
        return trim(preg_replace('/\s+/', ' ', $text) ?? $text);
    }
}
