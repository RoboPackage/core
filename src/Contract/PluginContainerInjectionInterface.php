<?php

declare(strict_types=1);

namespace RoboPackage\Core\Contract;

use Psr\Container\ContainerInterface;

/**
 * Define the plugin container injection interface.
 */
interface PluginContainerInjectionInterface
{
    /**
     * @param array $configuration
     * @param array $pluginDefinition
     * @param \Psr\Container\ContainerInterface $container
     *
     * @return \RoboPackage\Core\Contract\PluginInterface
     */
    public static function create(
        array $configuration,
        array $pluginDefinition,
        ContainerInterface $container,
    ): PluginInterface;
}
