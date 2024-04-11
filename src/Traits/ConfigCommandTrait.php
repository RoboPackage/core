<?php

declare(strict_types=1);

namespace RoboPackage\Core\Traits;

use Robo\Result;
use Robo\Task\Base\Exec;
use Robo\Common\ConfigAwareTrait;
use Robo\Collection\CollectionBuilder;
use RoboPackage\Core\Exception\RoboPackageRuntimeException;

/**
 * Define the config command trait.
 *
 * This trait requires you to utilize the Robo\Contract\ConfigAwareInterface
 * on your plugin class.
 */
trait ConfigCommandTrait
{
    use ConfigAwareTrait;

    /**
     * Run the config command
     *
     * @param string $command
     *   The command to execute.
     * @param string $configPrefix
     *   The command config prefix.
     * @param array $commandArgs
     *   The command input object.
     *
     * @return \Robo\Result|bool
     */
    protected function runConfigCommand(
        string $command,
        string $configPrefix,
        array $commandArgs = []
    ): Result|bool {
        try {
            return $this->buildCommandTask(
                $command,
                $configPrefix,
                $commandArgs
            )->run();
        } catch (\Exception $exception) {
            $this->io()->error($exception->getMessage());
        }

        return false;
    }

    /**
     * Build the command task.
     *
     * @param string $command
     *   The executable command.
     * @param string $configPrefix
     *   The configuration prefix.
     * @param array $commandArgs
     *   The command arguments.
     *
     * @return \Robo\Collection\CollectionBuilder|\Robo\Task\Base\Exec
     */
    protected function buildCommandTask(
        string $command,
        string $configPrefix,
        array $commandArgs = []
    ): CollectionBuilder|Exec {
        if ($executeCommand = $this->resolveConfigCommand($command, $configPrefix)) {
            $task = $this->taskExec($executeCommand);

            if (count($commandArgs) !== 0) {
                $task->args($commandArgs);
            }

            return $task;
        }

        throw new RoboPackageRuntimeException(sprintf(
            "The %s command hasn't been defined in the robo.yml.",
            $command
        ));
    }

    /**
     * Resolve the config arbitrary command.
     *
     * @param string $command
     *   The command to execute.
     *
     * @return string|null
     *   An arbitrary terminal command.
     */
    protected function resolveConfigCommand(
        string $command,
        string $configPrefix,
        mixed $default = null
    ): ?string {
        return $this->getConfig()->get(
            "$configPrefix.$command",
            $default
        );
    }
}
