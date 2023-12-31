<?php

declare(strict_types=1);

namespace RoboPackage\Core\Contract;

/**
 * Define the plugin interface.
 */
interface PluginInterface
{
    /**
     * Get the plugin identifier.
     *
     * @return string|null
     */
    public function getPluginId(): ?string;

    /**
     * Get the plugin label.
     *
     * @return string|null
     */
    public function getPluginLabel(): ?string;

    /**
     * Get the plugin configuration.
     *
     * @return array
     */
    public function getConfiguration(): array;
}
