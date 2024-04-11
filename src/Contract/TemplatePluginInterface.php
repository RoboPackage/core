<?php

declare(strict_types=1);

namespace RoboPackage\Core\Contract;

/**
 * Define the template plugin interface.
 */
interface TemplatePluginInterface extends PluginInterface, PluginContainerInjectionInterface
{
    /**
     * Get the template plugin file name.
     *
     * @return string|null
     */
    public function getFilename(): ?string;

    /**
     * Get the template plugin file content.
     *
     * @return string|null
     */
    public function getContent(): ?string;

    /**
     * Get the template plugin file path.
     *
     * @return string|null
     */
    public function getFilePath(): ?string;

    /**
     * Get the template plugin variables.
     *
     * @return array
     */
    public function getVariables(): array;
}
