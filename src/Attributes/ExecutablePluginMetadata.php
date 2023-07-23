<?php

declare(strict_types=1);

namespace RoboPackage\Core\Attributes;

/**
 * Define the executable plugin metadata.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class ExecutablePluginMetadata extends PluginMetadata
{
    /**
     * Define the attribute constructor.
     *
     * @param string $id
     * @param string $label
     * @param string $binary
     */
    public function __construct(
        protected string $id,
        protected string $label,
        protected string $binary,
    ) {
        parent::__construct($id, $label);
    }
}
