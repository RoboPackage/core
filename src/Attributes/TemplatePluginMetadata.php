<?php

declare(strict_types=1);

namespace RoboPackage\Core\Attributes;

/**
 * Define the template plugin metadata.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class TemplatePluginMetadata extends PluginMetadata
{
    /**
     * Define the attribute constructor.
     *
     * @param string $id
     * @param string $label
     * @param string $templateFile
     * @param array<int, string> $templateDirs
     */
    public function __construct(
        protected string $id,
        protected string $label,
        protected string $templateFile,
        protected array $templateDirs,
    ) {
        parent::__construct($id, $label);
    }
}
