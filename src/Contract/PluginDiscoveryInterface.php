<?php

declare(strict_types=1);

namespace RoboPackage\Core\Contract;

/**
 * Define the plugin discovery interface.
 */
interface PluginDiscoveryInterface
{
    /**
     * Find the plugin metadata.
     *
     * @return array
     *   An array of the plugin metadata, keyed by the plugin classname.
     *     - id: The plugin ID.
     *     - label: The plugin label.
     */
    public function find(): array;
}
