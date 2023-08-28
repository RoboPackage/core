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
     *
     * @return string
     *   The string with the token replaced.
     */
    protected function replaceToken(
        string $string
    ): string {
        $tokenData = $this->getTokenData();

        return preg_replace(
            $this->formatTokenPattern($tokenData),
            array_values($tokenData),
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

        foreach (array_keys($tokens) as $token) {
            $pattern[] = "/{{\s*$token\s*}}/";
        }

        return $pattern;
    }

    /**
     * Get the token replacement data.
     *
     * @return array
     *   An array of token data.
     */
    protected function getTokenData(): array
    {
        return [];
    }
}
