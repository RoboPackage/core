<?php

declare(strict_types=1);

namespace RoboPackage\Core\Traits;

/**
 * Define the token trait.
 */
trait TokenTrait
{
    /**
     * Replace the token pattern.
     *
     * @param string $string
     *   The string to replace with tokens.
     * @param array $tokens
     *   The tokens used in the replacement.
     *
     * @return string
     *   The string with the token replaced.
     */
    protected function replaceToken(
        string $string,
        array $tokens = []
    ): string {
        return preg_replace(
            $this->formatTokenPattern($tokens),
            array_values($tokens),
            $string
        );
    }

    /**
     * Format the token pattern.
     *
     * @param array $tokens
     *   An array of tokens.
     *
     * @return array
     *   An array of token pattern.
     */
    protected function formatTokenPattern(
        array $tokens
    ): array {
        $pattern = [];

        foreach ($tokens as $token => $value) {
            $pattern[] = "/{{\s*$token\s*}}/";
        }

        return $pattern;
    }
}
