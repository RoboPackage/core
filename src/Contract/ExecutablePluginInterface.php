<?php

declare(strict_types=1);

namespace RoboPackage\Core\Contract;

/**
 * Define the executable plugin interface.
 */
interface ExecutablePluginInterface extends PluginInterface
{
    /**
     * Get the executable binary.
     *
     * @return string
     */
    public function getBinary(): string;

    /**
     * Build the executable command.
     *
     * @return string
     *   A fully executable command.
     */
    public function build(): string;

    /**
     * Set the executable command.
     *
     * @param string $command
     *   The executable subcommand
     */
    public function setCommand(string $command): static;

    /**
     * Get the executable command.
     *
     * @return string|null
     *   The executable command.
     */
    public function getCommand(): ?string;

    /**
     * Set the executable argument.
     *
     * @param string $argument
     *   The executable argument.
     */
    public function setArgument(string $argument): static;

    /**
     * Set the executable arguments.
     *
     * @param array<string> $arguments
     *   An array of executable arguments.
     */
    public function setArguments(array $arguments): static;

    /**
     * Set the executable option parameter value.
     *
     * @param string $parameter
     *   The executable parameter.
     * @param string|null $value
     *   The executable parameter value.
     */
    public function setOption(string $parameter, ?string $value = null): static;

    /**
     * Set the executable option parameter values.
     *
     * @param array<string, string> $options
     *   An array of option parameter values.
     */
    public function setOptions(array $options): static;
}
