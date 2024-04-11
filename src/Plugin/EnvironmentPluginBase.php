<?php

declare(strict_types=1);

namespace RoboPackage\Core\Plugin;

use RoboPackage\Core\RoboPackage;
use Psr\Container\ContainerInterface;
use RoboPackage\Core\Contract\PluginInterface;
use RoboPackage\Core\Plugin\Manager\TemplateManager;
use RoboPackage\Core\Traits\TemplateTaskActionsTrait;
use RoboPackage\Core\Plugin\Manager\ExecutableManager;
use RoboPackage\Core\Contract\EnvironmentPluginInterface;

/**
 * Define the environment base plugin class.
 */
abstract class EnvironmentPluginBase extends PluginBase implements EnvironmentPluginInterface
{
    use TemplateTaskActionsTrait;

    /**
     * @var \RoboPackage\Core\Plugin\Manager\TemplateManager
     */
    protected TemplateManager $templateManager;

    /**
     * @var \RoboPackage\Core\Plugin\Manager\ExecutableManager
     */
    protected ExecutableManager $executableManager;

    /**
     * Define the class constructor.
     *
     * @param array $configuration
     *   The plugin configuration.
     * @param array $pluginDefinition
     *   The plugin definition.
     * @param \RoboPackage\Core\Plugin\Manager\TemplateManager $templateManager
     *   The template manager instance
     * @param \RoboPackage\Core\Plugin\Manager\ExecutableManager $executableManager
     *   The executable manager instance.
     */
    public function __construct(
        array $configuration,
        array $pluginDefinition,
        TemplateManager $templateManager,
        ExecutableManager $executableManager
    )
    {
        parent::__construct($configuration, $pluginDefinition);

        $this->templateManager = $templateManager;
        $this->executableManager = $executableManager;
    }

    /**
     * @inheritDoc
     */
    public static function create(
        array $configuration,
        array $pluginDefinition,
        ContainerInterface $container
    ): PluginInterface
    {
        return new static (
            $configuration,
            $pluginDefinition,
            $container->get('templateManager'),
            $container->get('executableManager')
        );
    }

    /**
     * @inheritDoc
     */
    public function configure(): static
    {
        if ($databases = $this->configureDatabases()) {
            $configs['databases'] = $databases;
        }

        if ($environment = $this->configureEnvironment()) {
            $configs['environment'] = $environment;
        }

        RoboPackage::writeConfig($configs);

        return $this;
    }

    /**
     * Define the robo database configurations.
     *
     * @return array
     *   An array of the database configurations.
     */
    abstract protected function configureDatabases(): array;

    /**
     * Define the robo environment configurations.
     *
     * @return array
     *   An array of the environment configurations.
     */
    abstract protected function configureEnvironment(): array;
}
