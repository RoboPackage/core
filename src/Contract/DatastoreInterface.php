<?php

declare(strict_types=1);

namespace RoboPackage\Core\Contract;

/**
 * Define the datastore interface.
 */
interface DatastoreInterface
{
    /**
     * Read contents from datastore.
     */
    public function read(): array|string;

    /**
     * Set contents to be merged.
     */
    public function merge(): static;

    /**
     * Write contents to datastore.
     *
     * @param string|array $content
     *   The datastore content
     *
     * @return bool
     *   Return true when write was successful; otherwise false.
     */
    public function write(string|array $content): bool;
}
