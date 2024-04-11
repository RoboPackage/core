<?php

declare(strict_types=1);

namespace RoboPackage\Core\Plugin;

use Robo\Symfony\ConsoleIO;
use Psr\Container\ContainerInterface;
use Consolidation\Config\ConfigInterface;
use RoboPackage\Core\Contract\TemplatePluginInterface;

/**
 * Define the template plugin base class.
 */
abstract class TemplatePluginBase extends PluginBase implements TemplatePluginInterface
{
    /**
     * The internal template variables.
     *
     * @var array
     */
    protected array $variables = [];

    /**
     * The Robo configuration object.
     *
     * @var \Consolidation\Config\ConfigInterface
     */
    protected ConfigInterface $config;

    /**
     * Define the template plugin base contractor.
     *
     * @param array $configuration
     *   The plugin configuration.
     * @param array $pluginDefinition
     *   The plugin definition.
     * @param \Consolidation\Config\ConfigInterface $config
     *   The configuration service.
     */
    public function __construct(
        array $configuration,
        array $pluginDefinition,
        ConfigInterface $config
    ) {
        $this->config = $config;
        parent::__construct($configuration, $pluginDefinition);
    }

    /**
     * {@inheritDoc}
     */
    public static function create(
        array $configuration,
        array $pluginDefinition,
        ContainerInterface $container,
    ): static {
        return new static(
            $configuration,
            $pluginDefinition,
            $container->get('config')
        );
    }

    /**
     * @inheritDoc
     */
    public function getFilename(): ?string
    {
        return $this->pluginDefinition['templateFile'] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getContent(): ?string
    {
        if ($filePath = $this->getFilePath()) {
            return file_get_contents($filePath);
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getFilePath(): ?string
    {
        return $this->resolveTemplatePath();
    }

    /**
     * Set the template variable.
     *
     * @param string $name
     *   The variable name.
     * @param mixed $value
     *   The variable value.
     *
     * @return $this
     */
    public function setVariable(string $name, mixed $value): static
    {
        $this->variables['{{' . $name . '}}'] = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getVariables(): array
    {
        $variables = $this->getStaticVariables();

        foreach ($this->variableDefinitions() as $name => $definition) {
            if (
                !isset($definition['callback'])
                || !is_callable($definition['callback'])
            ) {
                continue;
            }
            $name = $definition['variable'] ?? $name;

            if (!isset($variables[$name])) {
                $variables[$name] = $definition['callback']($this->io());
            }
        }

        return $variables;
    }

    /**
     * Get the static template variables.
     *
     * @return array
     *   An array of variables keyed by the name.
     */
    protected function getStaticVariables(): array
    {
        return array_replace(
            $this->variables,
            $this->getPluginTemplateVariables()
        );
    }

    /**
     * Define the template plugin variable definitions.
     *
     * @return array
     */
    protected function variableDefinitions(): array
    {
        return [];
    }

    /**
     * Resolve the template path.
     *
     * @return string|null
     */
    protected function resolveTemplatePath(): ?string
    {
        $templateName = $this->getFilename();

        foreach ($this->templateDirectories() as $directory) {
            $templatePath = "$directory/$templateName";

            if (file_exists($templatePath)) {
                return $templatePath;
            }
        }

        return null;
    }

    /**
     * Get the configuration template directories.
     *
     * @return array
     */
    protected function templateDirectories(): array
    {
        return $this->configuration['templateDirectories'] ?? [];
    }

    /**
     * Get the plugin template variables.
     *
     * @return array
     */
    protected function getPluginTemplateVariables(): array
    {
        return $this->getPluginTemplateConfig()['variables'] ?? [];
    }

    /**
     * Get the plugin template configuration.
     *
     * @return array
     */
    protected function getPluginTemplateConfig(): array
    {
        return $this->config->get('plugins.templates')[$this->getPluginId()] ?? [];
    }
}
