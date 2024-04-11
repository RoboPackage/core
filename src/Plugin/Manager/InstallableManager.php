<?php

declare(strict_types=1);

namespace RoboPackage\Core\Plugin\Manager;

use Composer\Autoload\ClassLoader;
use RoboPackage\Core\Plugin\PluginManagerBase;
use RoboCollection\Core\Attributes\InstallablePluginMetadata;
use RoboPackage\Core\Plugin\Discovery\PluginNamespaceDiscovery;

/**
 * Define the installable plugin manager.
 */
class InstallableManager extends PluginManagerBase
{
    /**
     * The environment plugin manager constructor.
     */
    public function __construct(
        protected ClassLoader $classloader
    )
    {
        parent::__construct(
            (new PluginNamespaceDiscovery($classloader))
                ->setNamespace('Plugin\RoboPackage\Installable')
                ->setAttributeClass(InstallablePluginMetadata::class)
        );
    }

    /**
     * Get plugin definition options by group.
     *
     * @param string $group
     *   The plugin metadata group name.
     *
     * @return array
     *   An array of options for definitions by group.
     */
    public function getDefinitionOptionsByGroup(string $group): array
    {
        $options = [];

        foreach ($this->getDefinitions() as $pluginId => $definition) {
            if (
                !isset($definition['label'], $definition['group'])
                || $group !== $definition['group']
            ) {
                continue;
            }
            $options[$pluginId] = $definition['label'];
        }

        return $options;
    }
}
