<?php

declare(strict_types=1);

namespace RoboPackage\Core\Attributes;

/**
 * Define the installable plugin metadata.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class InstallablePluginMetadata extends PluginMetadata
{
    /**
     * Define the attribute constructor.
     *
     * @param string $id
     * @param string $label
     * @param string $group
     */
    public function __construct(
        protected string $id,
        protected string $label,
        protected string $group
    ) {
        parent::__construct($id, $label);
    }
}
