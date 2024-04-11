<?php

declare(strict_types=1);

namespace RoboPackage\Core;

/**
 * Define reusable question validators that can used.
 */
final class QuestionValidators
{
    /**
     * This method returns a callback function that checks if a given answer is empty.
     *
     * @return callable
     *   Returns a callback function that checks if a given answer is empty.
     */
    public static function requiredValue(): callable
    {
        return static function (?string $answer): string {
            if (empty($answer)) {
                throw new \RuntimeException(
                    'A value is required!'
                );
            }
            return $answer;
        };
    }
}
