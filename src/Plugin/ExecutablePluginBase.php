<?php

declare(strict_types=1);

namespace RoboPackage\Core\Plugin;

use RoboPackage\Core\Contract\ExecutablePluginInterface;

/**
 * Define the executable plugin base class.
 */
abstract class ExecutablePluginBase extends PluginBase implements ExecutablePluginInterface
{
    /**
     * Define the executable options.
     *
     * @var array
     */
    protected array $options = [];

    /**
     * Define the executable arguments.
     *
     * @var array
     */
    protected array $arguments = [];

    /**
     * Define the executable command.
     *
     * @var string|null
     */
    protected ?string $command = null;

    /**
     * @inheritDoc
     */
    public function getBinary(): string
    {
        return $this->pluginDefinition['binary'];
    }

    /**
     * @inheritDoc
     */
    public function build(): string
    {
        return trim(implode(
            ' ',
            $this->structure()
        ));
    }

    /**
     * @inheritDoc
     */
    public function setCommand(string $command): static
    {
        $this->command = $command;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCommand(): ?string
    {
        return $this->command;
    }

    /**
     * @inheritDoc
     */
    public function setArgument(string $argument): static
    {
        $this->arguments[] = $argument;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setArguments(array $arguments): static
    {
        foreach ($arguments as $argument) {
            $this->setArgument($argument);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setOption(
        string $parameter,
        ?string $value = null
    ): static {
        $this->options[$parameter] = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setOptions(array $options): static
    {
        foreach ($options as $parameter => $value) {
            $this->setOption($parameter, $value);
        }

        return $this;
    }

    /**
     * Define the executable command structure.
     *
     * @return array
     */
    protected function structure(): array
    {
        return array_filter([
            $this->getBinary(),
            $this->getCommand(),
            $this->flattenOptions(),
            $this->flattenArguments()
        ]);
    }

    /**
     * Flatten the executable arguments.
     *
     * @return string
     */
    protected function flattenArguments(): string
    {
        return trim(implode(' ', $this->arguments));
    }

    /**
     * Flatten the executable options.
     *
     * @return string
     */
    protected function flattenOptions(): string
    {
        $options = [];

        foreach ($this->options as $parameter => $value) {
            if (!is_scalar($value)) {
                continue;
            }
            $parameter = str_starts_with($parameter, '-')
                ? $parameter
                : "--$parameter";

            if ($value !== '') {
                $value = str_contains($value, ' ')
                    ? "\"$value\""
                    : $value;
                $parameter .= "=$value";
            }

            $options[] = $parameter;
        }

        return trim(implode(' ', $options));
    }
}
