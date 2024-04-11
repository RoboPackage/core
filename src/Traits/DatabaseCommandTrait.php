<?php

declare(strict_types=1);

namespace RoboPackage\Core\Traits;

use JmesPath\Env;
use RoboPackage\Core\Database\Database;
use RoboPackage\Core\Contract\DatabaseInterface;
use RoboPackage\Core\Exception\RoboPackageRuntimeException;

/**
 * Define the database command trait.
 *
 * This trait requires you to utilize the Robo\Contract\ConfigAwareInterface
 * on your plugin class.
 */
trait DatabaseCommandTrait
{
    use TokenTrait;
    use ConfigCommandTrait;

    /**
     * @var \RoboPackage\Core\Contract\DatabaseInterface[]
     */
    protected array $databases = [];

    /**
     * @var array
     */
    protected array $databaseContexts = [];

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
        $this->setDatabaseContext('connection', $connection);

        if (count($this->databases) === 0) {
            $this->buildDatabases();
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
     * @return \RoboPackage\Core\Contract\DatabaseInterface[]
     *   An array of databases keyed by value set in configuration.
     */
    protected function buildDatabases(): array
    {
        $this->databases = [];
        $config = $this->getConfig();

        foreach ($config->get('databases') ?? [] as $name => $database) {
            if (!is_array($database)) {
                continue;
            }
            $this->processDatabaseProperties($database);

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
     * Process the database properties.
     *
     * @param array $database
     *   An array of the database properties.
     *
     * @return void
     */
    protected function processDatabaseProperties(
        array &$database
    ): void {
        foreach ($database as &$value) {
            if (!is_array($value) || !isset($value['type'])) {
                continue;
            }
            $type = $value['type'];
            $configuration = $value['configuration'] ?? [];

            if ($type === 'command' && isset($configuration['command'])) {
                $value = $this->executeDatabaseCommand(
                    $configuration['command']
                );
            }

            if ($type === 'expression' && isset($configuration['data']['type'])) {
                $data = $configuration['data'];
                if (
                    $data['type'] === 'command'
                    && isset($data['command'], $configuration['expression'])
                    && $expressionData = $this->executeDatabaseCommand($data['command'])
                ) {
                    $value = $this->executeDatabaseExpression(
                        $expressionData,
                        $configuration['expression']
                    );
                }
            }
        }
    }

    /**
     * Execute a database command.
     *
     * @param string $command
     *   The command to execute.
     *
     * @return string|null
     */
    protected function executeDatabaseCommand(
        string $command
    ): ?string {
        $command = $this->replaceToken($command);

        $task = $this->taskExec($command)
            ->silent(true)
            ->printOutput(false)
            ->run();

        return $task->wasSuccessful()
            ? $task->getMessage()
            : null;
    }

    /**
     * Execute the database expression.
     *
     * @param string $jsonData
     *   The JSON string data.
     * @param string|array $expression
     *   The single or multiple JMES expression.
     *
     * @return string|null
     *   The expression value extracted.
     */
    protected function executeDatabaseExpression(
        string $jsonData,
        string|array $expression,
    ): mixed {
        $value = null;
        try {
            if ($json = json_decode($jsonData, true, 512, JSON_THROW_ON_ERROR)) {
                if (is_string($expression)) {
                    $value = Env::search(
                        $this->replaceToken($expression),
                        $json
                    );
                }

                if (is_array($expression)) {
                    $value = $this->executeDatabaseConnectionExpression(
                        $json,
                        $expression,
                    );
                }

                if (!is_scalar($value)) {
                    throw new RoboPackageRuntimeException(
                        'The expression value is an invalid type.'
                    );
                }
            }
        } catch (\Exception $exception) {
            new RoboPackageRuntimeException(sprintf(
                'The following exception: %s was thrown while running the expression.',
                $exception->getMessage()
            ));
        }

        return $value;
    }

    /**
     * Execute the database connection expression.
     *
     * @param array $data
     *   An array of expression data.
     * @param array $expression
     *   An array of expression with the following:
     *      - query: The JMES expression query.
     *      - connection: Either internal or external.
     *
     * @return mixed
     *   The extract value based on the expression query.
     */
    protected function executeDatabaseConnectionExpression(
        array $data,
        array $expression,
    ): mixed {
        $contexts = $this->getDatabaseContexts();
        if (isset($contexts['connection'])) {
            foreach ($expression as $expressionInfo) {
                if (
                    !isset(
                        $expressionInfo['query'],
                        $expressionInfo['connection']
                    )
                ) {
                    continue;
                }
                if ($expressionInfo['connection'] === $contexts['connection']) {
                    return Env::search(
                        $this->replaceToken($expressionInfo['query']),
                        $data
                    );
                }
            }
        }

        return null;
    }

    /**
     * Get the database contexts.
     *
     * @return array
     *   The database contexts.
     */
    protected function getDatabaseContexts(): array
    {
        return $this->databaseContexts;
    }

    /**
     * Set the database context.
     *
     * @param string $key
     *   The context key.
     * @param string $value
     *   The context value.
     *
     * @return $this
     */
    protected function setDatabaseContext(
        string $key,
        string $value
    ): static {
        $this->databaseContexts[$key] = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function getTokenData(): array
    {
        $contexts = $this->getDatabaseContexts();

        return [
            'connection' => $contexts['connection'] ?? null
        ];
    }
}
