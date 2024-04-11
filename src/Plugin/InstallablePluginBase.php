<?php

declare(strict_types=1);

namespace RoboPackage\Core\Plugin;

use RoboPackage\Core\Contract\InstallablePluginInterface;

/**
 * Define the installable plugin base class.
 */
abstract class InstallablePluginBase extends PluginBase implements InstallablePluginInterface
{
    /**
     * @inheritDoc
     */
    public function runInstallation(): void
    {
        $this->preInstallation();
        $this->mainInstallation();
        $this->postInstallation();
    }

    /**
     * @inheritDoc
     */
    public function callInstallationStep(string $step): void
    {
        match ($step) {
            'pre' => $this->preInstallation(),
            'post' => $this->postInstallation(),
        };
    }

    /**
     * Run the pre-installation.
     *
     * The method is called prior to the main installation and accounts for
     * dependencies.
     */
    protected function preInstallation(): void {}

    /**
     * Run the main installation.
     */
    abstract protected function mainInstallation(): void;

    /**
     * Run the post-installation.
     *
     * The method is called after the main installation and accounts for
     * configurations.
     */
    protected function postInstallation(): void {}
}
