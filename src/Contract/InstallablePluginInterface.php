<?php

declare(strict_types=1);

namespace RoboPackage\Core\Contract;

/**
 * Define the installable plugin interface.
 */
interface InstallablePluginInterface extends PluginInterface
{
    /**
     * Run installation for the plugin instance.
     */
    public function runInstallation(): void;

    /**
     * Call the other installation step process independently.
     *
     * @param string $step
     *   The installation step to invoke (e.g. pre, post).
     */
    public function callInstallationStep(string $step): void;
}
