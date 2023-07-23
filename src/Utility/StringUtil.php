<?php

declare(strict_types=1);

namespace RoboPackage\Core\Utility;

use Symfony\Component\String\UnicodeString;

/**
 * Define the string utility class.
 *
 * This utility class should be used instead of using the Symfony string
 * component directly. This allows us to control where the third-party package
 * is used.
 */
class StringUtil
{
    /**
     * Format the string as a machine name.
     *
     * @param string $string
     *
     * @return string
     */
    public static function machineName(string $string): string
    {
        return (new UnicodeString($string))
            ->lower()
            ->replace(' ', '-')
            ->toString();
    }

    /**
     * Format the string without extra whitespaces.
     *
     * @param string $string
     *
     * @return string
     */
    public static function stripWhitespace(string $string): string
    {
        return (new UnicodeString($string))
            ->collapseWhitespace()
            ->toString();
    }
}
