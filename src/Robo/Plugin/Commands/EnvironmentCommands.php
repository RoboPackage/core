<?php

declare(strict_types=1);

namespace RoboPackage\Core\Robo\Plugin\Commands;

use Robo\Tasks;
use Robo\Contract\ConfigAwareInterface;
use RoboPackage\Core\Traits\EnvironmentCommandTrait;

/**
 * Define the robo package environment commands.
 */
class EnvironmentCommands extends Tasks implements ConfigAwareInterface
{
    use EnvironmentCommandTrait;

    /**
     * Start the project environment.
     *
     * @aliases env:up
     */
    public function envStart(): void
    {
        $this->runEnvironmentCommand(
            'start'
        );
    }

    /**
     * Stop the project environment.
     *
     * @aliases env:down
     */
    public function envStop(): void
    {
        $this->runEnvironmentCommand(
            'stop'
        );
    }

    /**
     * Restart the project environment.
     *
     * @alias env:reboot
     */
    public function envRestart(): void
    {
        $this->runEnvironmentCommand(
            'restart'
        );
    }

    /**
     * Display info of the project environment.
     */
    public function envInfo(): void
    {
        $this->runEnvironmentCommand(
            'info'
        );
    }

    /**
     * SSH into the project environment.
     *
     * @aliases ssh
     */
    public function envSsh(array $commandArgs): void
    {
        $this->runEnvironmentCommand(
            'ssh',
            $commandArgs
        );
    }

    /**
     * Execute command in the project environment.
     */
    public function envExec(array $execCommand): void
    {
        $commandString = implode(' ', $execCommand);

        $this->runEnvironmentCommand(
            'execute',
            [
                $commandString
            ]
        );
    }

    /**
     * Launch the project environment in browser.
     */
    public function envLaunch(): void
    {
        $this->runEnvironmentCommand(
            'launch'
        );
    }

    /**
     * Destroy the project environment.
     */
    public function envDestroy(): void
    {
        $this->runEnvironmentCommand(
            'destroy'
        );
    }
}
