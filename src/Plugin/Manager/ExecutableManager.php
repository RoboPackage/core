<?php

declare(strict_types=1);

namespace RoboPackage\Core\Plugin\Manager;

use Composer\Autoload\ClassLoader;
use RoboPackage\Core\Plugin\PluginManagerBase;
use RoboPackage\Core\Attributes\ExecutablePluginMetadata;
use RoboPackage\Core\Plugin\Discovery\PluginNamespaceDiscovery;

/**
 * Define the executable manager.
 */
class ExecutableManager extends PluginManagerBase
{
    /**
     * The executable plugin manager constructor.
     */
    public function __construct(
        protected ClassLoader $classloader
    )
    {
        parent::__construct(
            (new PluginNamespaceDiscovery($classloader))
                ->setNamespace('Plugin\RoboPackage\Executable')
                ->setAttributeClass(ExecutablePluginMetadata::class)
        );
    }
}
