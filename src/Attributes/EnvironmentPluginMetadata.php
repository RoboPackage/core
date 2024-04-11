<?php

declare(strict_types=1);

use RoboPackage\Core\Attributes\PluginMetadata;

/**
 * Define the environment plugin metadata.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class EnvironmentPluginMetadata extends PluginMetadata {}
