<?php

declare(strict_types=1);

namespace RoboPackage\Core\Plugin\RoboPackage\Executable;

use RoboPackage\Core\Plugin\ExecutablePluginBase;
use RoboPackage\Core\Contract\MySqlExecutableInterface;
use RoboPackage\Core\Attributes\ExecutablePluginMetadata;

/**
 * Define the MySQL executable.
 */
#[ExecutablePluginMetadata(
    id: 'mysql',
    label: 'MySql',
    binary: 'mysql'
)]
class MySql extends ExecutablePluginBase implements MySqlExecutableInterface
{
    /**
     * @inheritDoc
     */
    public function database(string $database): static
    {
        $this->setArgument($database);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function port(int $port): static
    {
        $this->setOption(__FUNCTION__, (string) $port);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function host(string $host): static
    {
        $this->setOption(__FUNCTION__, $host);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function user(string $user): static
    {
        $this->setOption(__FUNCTION__, $user);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function password(string $password): static
    {
        $this->setOption(__FUNCTION__, $password);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function execute(string $command): static
    {
        $this->setOption(__FUNCTION__, $command);

        return $this;
    }
}
