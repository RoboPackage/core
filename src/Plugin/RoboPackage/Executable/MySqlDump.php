<?php

declare(strict_types=1);

namespace RoboPackage\Core\Plugin\RoboPackage\Executable;

use RoboPackage\Core\Attributes\ExecutablePluginMetadata;
use RoboPackage\Core\Contract\MySqlDumpExecutableInterface;

/**
 * Define the MySQL dump executable.
 */
#[ExecutablePluginMetadata(
    id: 'mysql_dump',
    label: 'MySql Dump',
    binary: 'mysqldump'
)]
class MySqlDump extends MySql implements MySqlDumpExecutableInterface
{
    /**
     * @inheritDoc
     */
    public function noTablespaces(): static
    {
        $this->setOption('no-tablespaces');

        return $this;
    }
}
