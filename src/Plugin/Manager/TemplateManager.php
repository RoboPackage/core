<?php

declare(strict_types=1);

namespace RoboPackage\Core\Plugin\Manager;

use Composer\Autoload\ClassLoader;
use Kcs\ClassFinder\PathNormalizer;
use RoboPackage\Core\Plugin\PluginManagerBase;
use RoboPackage\Core\Contract\PluginInterface;
use RoboPackage\Core\Attributes\TemplatePluginMetadata;
use RoboPackage\Core\Plugin\Discovery\PluginNamespaceDiscovery;

/**
 * Define the template manager.
 */
class TemplateManager extends PluginManagerBase
{
    /**
     * @var array
     */
    protected array $templateDirectories = [];

    /**
     * The template plugin manager constructor.
     */
    public function __construct(
        protected string $rootPath,
        protected ClassLoader $classloader
    ) {
        parent::__construct(
            (new PluginNamespaceDiscovery($classloader))
                ->setNamespace('Plugin\RoboPackage\Template')
                ->setAttributeClass(TemplatePluginMetadata::class)
        );
    }

    /**
     * Get the current working directory.
     *
     * @return string
     *   The project current working directory.
     */
    public function getRootPath(): string
    {
        return $this->rootPath;
    }

    /**
     * Set additional plugin template directory.
     *
     * @param string $directory
     *   A valid directory path.
     */
    public function setTemplateDirectory(string $directory): static
    {
        if (is_dir($directory)) {
            $this->templateDirectories[] = $directory;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function createInstance(
        string $pluginId,
        array $configuration = []
    ): ?PluginInterface {
        $configuration['templateDirectories'] = $this->templateDirectories();
        return parent::createInstance(
            $pluginId,
            $configuration
        );
    }

    /**
     * Process the template plugin.
     *
     * @param string $pluginId
     *   The template plugin ID.
     * @param callable $callback
     *   The callback to execute with the template object.
     */
    public function process(string $pluginId, callable $callback): void
    {
        if ($template = $this->createInstance($pluginId)) {
            $callback($template);
        }
    }

    /**
     * Define the template search directories.
     *
     * @return string[]
     *   An array of paths to template directories.
     */
    protected function templateDirectories(): array
    {
        $templateDirectories = [
            "$this->rootPath/.robo/templates",
        ] + $this->templateDirectories;

        foreach ($this->getDefinitions() as $definition) {
            if (isset($definition['templateDirs'])) {
                foreach ($definition['templateDirs'] as $templateDir) {
                    $templateDirectories[] = PathNormalizer::resolvePath(
                        $templateDir
                    );
                }
            }
        }

        return $templateDirectories;
    }
}
