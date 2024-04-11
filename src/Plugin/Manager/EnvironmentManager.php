<?php

declare(strict_types=1);

namespace RoboPackage\Core\Plugin\Manager;

use EnvironmentPluginMetadata;
use Composer\Autoload\ClassLoader;
use RoboPackage\Core\Plugin\PluginManagerBase;
use RoboPackage\Core\Plugin\Discovery\PluginNamespaceDiscovery;

/**
 * Define the environment plugin manager.
 */
class EnvironmentManager extends PluginManagerBase
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
                ->setNamespace('Plugin\RoboPackage\Environment')
                ->setAttributeClass(EnvironmentPluginMetadata::class)
        );
    }
}
