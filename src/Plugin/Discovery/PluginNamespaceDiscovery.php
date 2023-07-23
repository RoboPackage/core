<?php

declare(strict_types=1);

namespace RoboPackage\Core\Plugin\Discovery;

use Composer\Autoload\ClassLoader;
use Kcs\ClassFinder\Finder\ComposerFinder;
use RoboPackage\Core\Contract\PluginDiscoveryInterface;

/**
 * Define a plugin namespace discovery mechanism.
 */
class PluginNamespaceDiscovery implements PluginDiscoveryInterface
{
    /**
     * The class namespace.
     *
     * @var string
     */
    protected string $namespace;

    /**
     * @var string
     */
    protected string $attributeClass;

    /**
     * @var \Composer\Autoload\ClassLoader
     */
    protected ClassLoader $classLoader;

    /**
     * The class constructor.
     *
     * @param \Composer\Autoload\ClassLoader $classLoader
     */
    public function __construct(ClassLoader $classLoader)
    {
        $this->classLoader = $classLoader;
    }

    /**
     * Set the plugin namespace.
     *
     * @param string $namespace
     *   The plugin namespace.
     *
     * @return $this
     */
    public function setNamespace(string $namespace): static
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * Set the plugin attribute class.
     *
     * @param string $attributeClass
     *   The plugin attribute class.
     *
     * @return $this
     */
    public function setAttributeClass(string $attributeClass): static
    {
        $this->attributeClass = $attributeClass;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function find(): array
    {
        $definitions = [];

        $composerFinder = (new ComposerFinder())
            ->inNamespace($this->findPluginNamespaces())
            ->withAttribute($this->attributeClass);

        /** @var \ReflectionClass $reflection */
        foreach ($composerFinder->getIterator() as $className => $reflection) {
            foreach ($reflection->getAttributes() as $attribute) {
                if ($attribute->getTarget() !== \Attribute::TARGET_CLASS) {
                    continue;
                }
                $definitions[$className] = $attribute->getArguments();
            }
        }

        return $definitions;
    }

    /**
     * Find the plugin namespaces.
     *
     * @return array
     *   An array of plugin namespaces.
     */
    protected function findPluginNamespaces(): array
    {
        $namespaces = [];
        $classLoader = $this->classLoader;

        foreach ($classLoader->getPrefixesPsr4() as $prefixNamespace => $dirs) {
            foreach ($dirs as $directory) {
                if (!is_dir("$directory/{$this->namespaceAsDir()}")) {
                    continue;
                }
                $namespaces[] = "$prefixNamespace$this->namespace";
            }
        }

        return $namespaces;
    }

    /**
     * Get namespace in directory format.
     *
     * @return string
     *   The namespace in a directory format.
     */
    protected function namespaceAsDir(): string
    {
        return str_replace("\\", '/', trim($this->namespace, '\\'));
    }
}
