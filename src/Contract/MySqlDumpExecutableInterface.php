<?php

declare(strict_types=1);

namespace RoboPackage\Core\Contract;

/**
 * Define the MySql Dump executable interface.
 */
interface MySqlDumpExecutableInterface extends MySqlExecutableInterface
{
    /**
     * Set MySql Dump no tablespaces option.
     */
    public function noTablespaces(): static;
}
