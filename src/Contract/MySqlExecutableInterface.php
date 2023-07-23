<?php

declare(strict_types=1);

namespace RoboPackage\Core\Contract;

/**
 * Define the MySql executable interface.
 */
interface MySqlExecutableInterface extends ExecutablePluginInterface
{
    /**
     * Set the MySql database.
     *
     * @param string $database
     */
    public function database(string $database): static;

    /**
     * Set the MySql database port.
     *
     * @param int $port
     */
    public function port(int $port): static;

    /**
     * Set the MySql database host.
     *
     * @param string $host
     */
    public function host(string $host): static;

    /**
     * Set the MySql database user.
     *
     * @param string $user
     */
    public function user(string $user): static;

    /**
     * Set the MySql database password.
     *
     * @param string $password
     */
    public function password(string $password): static;

    /**
     * Set the MySql execute command.
     *
     * @param string $command
     */
    public function execute(string $command): static;
}
