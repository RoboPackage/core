<?php

declare(strict_types=1);

namespace RoboPackage\Core\Traits;

use Robo\Result;

/**
 * Define the environment command trait.
 */
trait EnvironmentCommandTrait
{
    use ConfigCommandTrait;

    /**
     * Run the environment config command.
     *
     * @param string $command
     *   The command to execute.
     * @param array $commandArgs
     *   The command pass-through arguments.
     */
    protected function runEnvironmentCommand(
        string $command,
        array $commandArgs = []
    ): Result|bool {
        return $this->runConfigCommand(
            $command,
            'environment',
            $commandArgs
        );
    }
}
