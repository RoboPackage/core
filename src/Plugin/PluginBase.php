<?php

declare(strict_types=1);

namespace RoboPackage\Core\Plugin;

use Robo\Tasks;
use RoboPackage\Core\Contract\PluginInterface;

/**
 * Define the plugin base.
 */
abstract class PluginBase extends Tasks implements PluginInterface
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
     * @inheritDoc
     */
    public function getPluginId(): ?string
    {
        return $this->pluginDefinition['id'] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getPluginLabel(): ?string
    {
        return $this->pluginDefinition['label'] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getConfiguration(): array
    {
        return $this->configuration ?? [];
    }

    /**
     * @inheritDoc
     */
    public function isApplicable(): bool
    {
        return true;
    }
}
