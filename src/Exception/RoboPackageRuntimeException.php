<?php

declare(strict_types=1);

namespace RoboPackage\Core\Exception;

use RoboPackage\Core\Utility\StringUtil;

/**
 * Define the Robo package runtime exception.
 */
class RoboPackageRuntimeException extends \RuntimeException
{
    /**
     * The class constructor.
     *
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        $message = StringUtil::stripWhitespace($message);
        parent::__construct($message, $code, $previous);
    }
}
