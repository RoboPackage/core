<?php

declare(strict_types=1);

namespace RoboPackage\Core\Contract;

/**
 * Define the database command interface.
 */
interface DatabaseInterface
{
    /**
     * Determine if the database is valid.
     *
     * @return bool
     */
    public function isValid(): bool;

    /**
     * Determine if the database file is gzipped.
     *
     * @param string $filepath
     *   The fully qualified path to the file.
     *
     * @return bool
     *   Return true if filepath is gzipped; otherwise false.
     */
    public static function isGzipped(string $filepath): bool;

    /**
     * Get the database type.
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Set the database type.
     *
     * @param string $type
     */
    public function setType(string $type): static;

    /**
     * Get the database host address.
     *
     * @return string
     */
    public function getHost(): string;

    /**
     * Set the database host address.
     *
     * @param string $host
     */
    public function setHost(string $host): static;

    /**
     * Get the database port.
     *
     * @return int
     */
    public function getPort(): int;

    /**
     * Set the database port.
     *
     * @param int $port
     */
    public function setPort(int $port): static;

    /**
     * Get the database name.
     *
     * @return string
     */
    public function getDatabase(): string;

    /**
     * Set the database name.
     *
     * @param string $name
     */
    public function setDatabase(string $name): static;

    /**
     * Get the database username.
     *
     * @return string
     */
    public function getUsername(): string;

    /**
     * Set the database username.
     *
     * @param string $username
     */
    public function setUsername(string $username): static;

    /**
     * Get the database password.
     *
     * @return string
     */
    public function getPassword(): string;

    /**
     * Set the database password.
     *
     * @param string $password
     */
    public function setPassword(string $password): static;

    /**
     * Get the database connection URL.
     *
     * @return string
     */
    public function getConnectionUrl(): string;
}
