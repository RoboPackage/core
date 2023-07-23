<?php

declare(strict_types=1);

namespace RoboPackage\Core\Attributes;

/**
 * Define the plugin metadata attribute.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class PluginMetadata
{
    /**
     * Define the attribute constructor.
     *
     * @param string $id
     * @param string $label
     */
    public function __construct(
        protected string $id,
        protected string $label,
    ) {
    }
}
