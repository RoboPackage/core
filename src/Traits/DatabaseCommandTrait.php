<?php

declare(strict_types=1);

namespace RoboPackage\Core\Traits;

use RoboPackage\Core\Database\Database;
use RoboPackage\Core\Contract\DatabaseInterface;
use RoboPackage\Core\Exception\RoboPackageRuntimeException;

/**
 * Define the database command trait.
 */
trait DatabaseCommandTrait
{
    /**
     * @var \RoboPackage\Core\Contract\DatabaseInterface[]
     */
    protected array $databases = [];

    /**
     * Get the database instance.
     *
     * @param string $key
     *   The database key, defaults to primary.
     *
     * @return \RoboPackage\Core\Contract\DatabaseInterface|null
     *   The database instance.
     *
     * @throws \JsonException
     * @throws \RoboPackage\Core\Exception\RoboPackageRuntimeException
     */
    protected function getDatabase(
        string $key = 'primary',
        string $connection = 'external'
    ): ?DatabaseInterface {

        if (count($this->databases) === 0) {
            $this->buildDatabases($connection);
        }

        return $this->databases[$key]
            ?? throw new RoboPackageRuntimeException(
                'The database configurations are invalid. Please check the
                .robo.yml to ensure you defined the database definition
                correctly.'
            );
    }

    /**
     * Build the configuration databases.
     *
     * @param string $connection
     *   The database connection (e.g. internal, external).
     *
     * @return \RoboPackage\Core\Contract\DatabaseInterface[]
     *   An array of databases keyed by value set in configuration.
     *
     * @throws \JsonException
     */
    protected function buildDatabases(string $connection): array
    {
        $this->databases = [];
        $config = $this->getConfig();

        foreach ($config->get('databases') ?? [] as $name => $database) {
            if (!is_array($database)) {
                continue;
            }
            $this->appendDatabaseRuntimeArguments(
                $database,
                $connection
            );

            if (
                isset(
                    $database['host'],
                    $database['port'],
                    $database['database'],
                    $database['username'],
                    $database['password']
                )
            ) {
                $this->databases[$name] = (new Database())
                    ->setType($database['type'] ?? 'mysql')
                    ->setHost($database['host'])
                    ->setPort((int)$database['port'])
                    ->setDatabase($database['database'])
                    ->setUsername($database['username'])
                    ->setPassword($database['password']);
            } else {
                throw new RoboPackageRuntimeException(sprintf(
                    'The %s database is missing required configurations!',
                    $name
                ));
            }
        }

        return $this->databases;
    }

    /**
     * Append database runtime arguments.
     *
     * @param array $database
     *   The database configuration.
     * @param string $connection
     *   The database connection (e.g. internal, external).
     * @return void
     *
     * @throws \JsonException
     */
    protected function appendDatabaseRuntimeArguments(
        array &$database,
        string $connection
    ): void {
        if (isset($database['runtime_arguments'])) {
            $runtimeArgs = $database['runtime_arguments'];

            if ($command = $runtimeArgs['command'] ?? null) {
                $command = str_replace('{connection}', $connection, $command);
                $task = $this->taskExec($command)
                    ->silent(true)
                    ->printOutput(false)
                    ->run();

                if ($task->wasSuccessful()) {
                    $message = $task->getMessage();
                    $result = json_decode(
                        $message,
                        true,
                        512,
                        JSON_THROW_ON_ERROR
                    );
                    $mapping = $runtimeArgs['mapping'] ?? [];

                    foreach ($mapping as $fromKey => $toKey) {
                        if (isset($result[$fromKey]) && $fromKey !== $toKey) {
                            $result[$toKey] = $result[$fromKey];
                            unset($result[$fromKey]);
                        }
                    }

                    $database += $result;
                }
            }

            unset($database['runtime_arguments']);
        }
    }
}
