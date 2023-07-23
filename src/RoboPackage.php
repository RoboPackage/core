<?php

declare(strict_types=1);

namespace RoboPackage\Core;

use Robo\Robo;
use Composer\InstalledVersions;
use RoboPackage\Core\Exception\RoboPackageRuntimeException;

/**
 * Define the Robo package instance.
 */
class RoboPackage
{
    /**
     * The Robo package project root path.
     *
     * @var string
     */
    protected static string $rootPath;

    /**
     * The Robo package project composer data.
     *
     * @var array
     */
    protected static array $composer = [];

    /**
     * Get the Robo package project root path.
     *
     * @return string
     *   The path to the project root directory.
     */
    public static function rootPath(): string
    {
        if (!isset(static::$rootPath)) {
            static::$rootPath = static::findFileRootPath(
                'composer.json'
            );
        }

        return static::$rootPath;
    }

    /**
     * Get the Robo package template manager.
     *
     * @return \RoboPackage\Core\TemplateManager
     */
    public static function templateManager(): TemplateManager
    {
        return (new TemplateManager(static::rootPath()))->setContainer(
            Robo::getContainer()
        );
    }

    /**
     * Get the Robo package executable manager.
     *
     * @return \RoboPackage\Core\ExecutableManager
     */
    public static function executableManager(): ExecutableManager
    {
        return (new ExecutableManager())->setContainer(
            Robo::getContainer()
        );
    }

    /**
     * Get active PHP versions.
     *
     * @return array
     *   An array of active PHP versions.
     */
    public static function activePhpVersions(): array
    {
        try {
            $activeVersions = json_decode(
                file_get_contents(
                    'https://www.php.net/releases/active.php'
                ),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (\Exception $exception) {
            throw new RoboPackageRuntimeException(
                $exception->getMessage()
            );
        }

        return array_keys(current($activeVersions));
    }

    /**
     * Get the project composer.json data.
     *
     * @return array
     *   An array representation of the composer.json.
     */
    public static function getComposer(): array
    {
        if (count(static::$composer) === 0) {
            static::loadComposer();
        }

        return static::$composer;
    }

    /**
     * Get an installed composer package version.
     *
     * @param string $packageName
     *   The composer vendor package name.
     *
     * @return string|null
     */
    public static function composerPackageVersion(
        string $packageName
    ): ?string {
        return InstalledVersions::getVersion($packageName);
    }

    /**
     * Check if a package is defined in the composer.json.
     *
     * @param string $packageName
     *   The composer package name.
     *
     * @return bool
     *   Return true if composer package exist; otherwise false.
     */
    public static function hasComposerPackage(string $packageName): bool
    {
        $composer = static::getComposer();

        foreach (['require', 'require-dev'] as $definitionName) {
            if (
                isset($composer[$definitionName])
                && is_array($composer[$definitionName])
                && in_array($packageName, $composer[$definitionName], true)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Load the project composer.json into memory.
     */
    protected static function loadComposer(): void
    {
        try {
            $composerFile = static::rootPath() . '/composer.json';

            if (!file_exists($composerFile)) {
                throw new \RuntimeException(
                    'Unable to locate the composer.json within the project.'
                );
            }
            static::$composer = json_decode(
                file_get_contents($composerFile),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (\Exception $exception) {
            throw new RoboPackageRuntimeException(
                $exception->getMessage()
            );
        }
    }

    /**
     * Find the file root path.
     *
     * @param string $filename
     *   The search filename to base the path from.
     *
     * @return string
     *   The file root path; otherwise fallback to the search path.
     */
    protected static function findFileRootPath(string $filename): string
    {
        $searchPath = getcwd();

        if (!file_exists("$searchPath/$filename")) {
            $searchDirs = explode('/', $searchPath);
            $searchDirCount = count($searchDirs);

            for ($i = 1; $i < $searchDirCount - 1; $i++) {
                $searchDir = implode('/', array_slice($searchDirs, 0, -$i));

                if (file_exists("$searchDir/$filename")) {
                    return $searchDir;
                }
            }
        }

        return $searchPath;
    }
}
