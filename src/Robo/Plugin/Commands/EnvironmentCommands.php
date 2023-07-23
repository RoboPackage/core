<?php

declare(strict_types=1);

namespace RoboPackage\Core\Robo\Plugin\Commands;

use Robo\Tasks;
use Robo\Symfony\ConsoleIO;
use Robo\Contract\ConfigAwareInterface;
use RoboPackage\Core\Traits\ConfigCommandTrait;

/**
 * Define the robo package environment commands.
 */
class EnvironmentCommands extends Tasks implements ConfigAwareInterface
{
    use ConfigCommandTrait;

    /**
     * Start the project environment.
     *
     * @aliases env:up
     */
    public function envStart(ConsoleIO $io): void
    {
        $this->runEnvironmentCommand(
            $io,
            'start'
        );
    }

    /**
     * Stop the project environment.
     *
     * @aliases env:down
     */
    public function envStop(ConsoleIO $io): void
    {
        $this->runEnvironmentCommand(
            $io,
            'stop'
        );
    }

    /**
     * Restart the project environment.
     *
     * @alias env:reboot
     */
    public function envRestart(ConsoleIO $io): void
    {
        $this->runEnvironmentCommand(
            $io,
            'restart'
        );
    }

    /**
     * Display info of the project environment.
     */
    public function envInfo(ConsoleIO $io): void
    {
        $this->runEnvironmentCommand(
            $io,
            'info'
        );
    }

    /**
     * SSH into the project environment.
     *
     * @aliases ssh
     */
    public function envSsh(ConsoleIO $io, array $commandArgs): void
    {
        $this->runEnvironmentCommand(
            $io,
            'ssh',
            $commandArgs
        );
    }

    /**
     * Execute command in the project environment.
     */
    public function envExec(ConsoleIO $io, array $execCommand): void
    {
        $commandString = implode(' ', $execCommand);

        $this->runEnvironmentCommand(
            $io,
            'execute',
            [
                $commandString
            ]
        );
    }

    /**
     * Launch the project environment in browser.
     */
    public function envLaunch(ConsoleIO $io): void
    {
        $this->runEnvironmentCommand(
            $io,
            'launch'
        );
    }

    /**
     * Destroy the project environment.
     */
    public function envDestroy(ConsoleIO $io): void
    {
        $this->runEnvironmentCommand(
            $io,
            'destroy'
        );
    }

    /**
     * Run the environment config command.
     *
     * @param \Robo\Symfony\ConsoleIO $io
     *   The console IO service.
     * @param string $command
     *   The command to execute.
     * @param array $commandArgs
     *   The command pass-through arguments.
     *
     * @return void
     */
    protected function runEnvironmentCommand(
        ConsoleIO $io,
        string $command,
        array $commandArgs = []
    ): void {
        $this->runConfigCommand(
            $io,
            $command,
            'environment',
            $commandArgs
        );
    }
}
