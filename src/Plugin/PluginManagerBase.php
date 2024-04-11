<?php

declare(strict_types=1);

namespace RoboPackage\Core\Plugin;

use Robo\Contract\IOAwareInterface;
use Robo\Collection\CollectionBuilder;
use Robo\Contract\ConfigAwareInterface;
use Robo\Contract\BuilderAwareInterface;
use League\Container\ContainerAwareTrait;
use League\Container\ContainerAwareInterface;
use RoboPackage\Core\Contract\PluginInterface;
use RoboPackage\Core\Contract\PluginDiscoveryInterface;
use RoboPackage\Core\Exception\RoboPackageRuntimeException;
use RoboPackage\Core\Contract\PluginContainerInjectionInterface;

/**
 * Define the plugin manager.
 */
abstract class PluginManagerBase implements ContainerAwareInterface
{
    use ContainerAwareTrait;

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
        $instance = null;
        $pluginDefinition = $this->getDefinition($pluginId);

        if (!isset($this->container)) {
            throw new RoboPackageRuntimeException(
                'The container is required to instantiate a plugin.'
            );
        }

        if (($classname = $pluginDefinition['class']) && class_exists($classname)) {
            unset($pluginDefinition['class']);

            if (is_subclass_of($classname, PluginContainerInjectionInterface::class)) {
                $instance = $classname::create(
                    $configuration,
                    $pluginDefinition,
                    $this->container,
                );
            } else {
                $instance = new $classname(
                    $configuration,
                    $pluginDefinition
                );
            }

            if ($instance instanceof IOAwareInterface) {
                if ($input = $this->container->get('input')) {
                    $instance->setInput($input);
                }
                if ($output = $this->container->get('output')) {
                    $instance->setOutput($output);
                }
            }

            if ($instance instanceof ConfigAwareInterface) {
                $instance->setConfig(
                    $this->container->get('config')
                );
            }

            if ($instance instanceof BuilderAwareInterface) {
                $instance->setBuilder(
                    CollectionBuilder::create($this->container, $instance)
                );
            }
        }

        return $instance;
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
