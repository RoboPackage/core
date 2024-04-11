<?php

declare(strict_types=1);

namespace RoboPackage\Core\Contract;

/**
 * Define the environment plugin interface.
 */
interface EnvironmentPluginInterface extends PluginInterface, PluginContainerInjectionInterface
{
    /**
     * Setup the environment plugin instance.
     */
    public function setup(): static;

    /**
     * Configure the environment plugin instance.
     */
    public function configure(): static;
}
