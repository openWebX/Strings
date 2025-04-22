<?php

namespace openWebX\Strings;

use Cocur\Slugify\Slugify;

class Strings
{
    private static ?Slugify $slugger = null;

    public static function slugifyString(string $string): string
    {
        return (self::$slugger ??= new Slugify())->slugify($string);
    }

    public static function bytesToHumanReadable(int $bytes): string
    {
        foreach (['TB' => 1 << 40, 'GB' => 1 << 30, 'MB' => 1 << 20, 'KB' => 1 << 10, 'B' => 1] as $unit => $value) {
            if ($bytes >= $value) {
                $result = round($bytes / $value, 2);
                // Deutsch: Komma als Dezimaltrennzeichen
                return str_replace('.', ',', (string)$result) . ' ' . $unit;
            }
        }
        return '0 B';
    }

    public static function oneOf(string $value, array $possibleValues): bool
    {
        return in_array($value, $possibleValues, true);
    }

    public static function isJSON(?string $string): bool
    {
        if ($string === null) {
            return false;
        }
        return json_validate($string);
    }

    public static function truncate(string $string, int $max = 30, string $replacement = '...'): string
    {
        if (mb_strlen($string) <= $max) {
            return $string;
        }
        $keep = $max - mb_strlen($replacement);
        return mb_substr($string, 0, $keep) . $replacement;
    }

    public static function replaceUmlauts(string $orig, bool $trim = false): string
    {
        $map = [
            'Ä' => 'Ae', 'Ö' => 'Oe', 'Ü' => 'Ue',
            'ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue',
            'ß' => 'ss', '´' => '',
        ];
        if ($trim) {
            $map[' '] = '';
        }
        return strtr($orig, $map);
    }

    public static function removeFragments(string $orig, array $fragments = []): string
    {
        return str_replace($fragments, '', $orig);
    }

    public static function decamelize(string $string): string
    {
        // fooBar → foo_bar, FooBarBaz → foo_bar_baz
        return strtolower(preg_replace([
            '/(?<=[a-z\d])([A-Z])/',
            '/(?<!_)(?=[A-Z][a-z])/'
        ], '_$1', $string));
    }

    public static function camelize(string $scored): string
    {
        // foo_bar_baz → fooBarBaz
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', strtolower($scored)))));
    }

    public static function removeSpecialChars(string $string): string
    {
        $trans = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string);
        return $trans !== false ? $trans : $string;
    }
}
