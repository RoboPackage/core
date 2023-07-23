<?php

declare(strict_types=1);

namespace RoboPackage\Core\Plugin;

use Psr\Container\ContainerInterface;
use RoboPackage\Core\Contract\PluginInterface;
use RoboPackage\Core\Contract\PluginDiscoveryInterface;
use RoboPackage\Core\Contract\PluginContainerInjectionInterface;

/**
 * Define the plugin manager.
 */
abstract class PluginManagerBase
{
    /**
     * Set the use cache flag.
     *
     * @var bool
     */
    protected bool $useCache = true;

    /**
     * An array of plugin definitions.
     *
     * @var array
     */
    protected array $definitions = [];

    /**
     * @var \Psr\Container\ContainerInterface
     */
    protected ContainerInterface $container;

    /**
     * The plugin manager constructor.
     *
     * @param \RoboPackage\Core\Contract\PluginDiscoveryInterface $discovery
     */
    public function __construct(
        protected PluginDiscoveryInterface $discovery
    ) {
    }

    /**
     * Set the use plugin cache.
     */
    public function useCache(bool $value): static
    {
        $this->useCache = $value;

        return $this;
    }

    /**
     * Set the plugin container.
     *
     * @param \Psr\Container\ContainerInterface $container
     *   The container instance.
     */
    public function setContainer(ContainerInterface $container): static
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Create the plugin instance.
     *
     * @param string $pluginId
     *   The plugin ID.
     * @param array $configuration
     *   The plugin configuration.
     */
    public function createInstance(
        string $pluginId,
        array $configuration = []
    ): ?PluginInterface {
        $pluginDefinition = $this->getDefinition($pluginId);

        if (($classname = $pluginDefinition['class']) && class_exists($classname)) {
            unset($pluginDefinition['class']);

            if (
                isset($this->container)
                && is_subclass_of($classname, PluginContainerInjectionInterface::class)
            ) {
                return $classname::create(
                    $configuration,
                    $pluginDefinition,
                    $this->container,
                );
            }

            return new $classname(
                $configuration,
                $pluginDefinition
            );
        }

        return null;
    }

    /**
     * Get the plugin definition.
     *
     * @param string $pluginId
     *   The plugin ID.
     *
     * @return array
     *   An array of the plugin definition information.
     */
    public function getDefinition(string $pluginId): array
    {
        return $this->getDefinitions()[$pluginId] ?? [];
    }

    /**
     * Get the plugin definitions.
     *
     * @return array
     *   An array of plugin definitions.
     */
    public function getDefinitions(): array
    {
        if (!$this->useCache || count($this->definitions) === 0) {
            foreach ($this->discovery->find() as $className => $metadata) {
                if (!isset($metadata['id'])) {
                    continue;
                }
                $this->definitions[$metadata['id']] = [
                    'class' => $className,
                ] + $metadata;
            }
        }

        return $this->definitions;
    }

    /**
     * Get the plugin definition options.
     *
     * @return array
     *   An array of the plugin options.
     */
    public function getDefinitionOptions(): array
    {
        $options = [];

        foreach ($this->getDefinitions() as $pluginId => $definition) {
            if (!isset($definition['label'])) {
                continue;
            }
            $options[$pluginId] = $definition['label'];
        }

        return $options;
    }
}
