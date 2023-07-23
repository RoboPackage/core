<?php

declare(strict_types=1);

namespace RoboPackage\Core\Plugin;

use RoboPackage\Core\Contract\PluginInterface;

/**
 * Define the plugin base.
 */
abstract class PluginBase implements PluginInterface
{
    /**
     * Define the class constructor.
     *
     * @param array $configuration
     *   An array of plugin configuration.
     * @param array $pluginDefinition
     *   An array of plugin definition.
     */
    public function __construct(
        protected array $configuration,
        protected array $pluginDefinition
    ) {
    }

    /**
     * Get the plugin label.
     *
     * @return string|null
     */
    public function getPluginLabel(): ?string
    {
        return $this->pluginDefinition['label'] ?? null;
    }

    /**
     * Get the plugin identifier.
     *
     * @return string|null
     */
    public function getPluginId(): ?string
    {
        return $this->pluginDefinition['id'] ?? null;
    }

    /**
     * Get the plugin configuration.
     *
     * @return array
     */
    public function getConfiguration(): array
    {
        return $this->configuration ?? [];
    }
}
