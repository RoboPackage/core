<?php

declare(strict_types=1);

namespace RoboPackage\Core\Traits;

use JmesPath\Env;
use RoboPackage\Core\Database\Database;
use RoboPackage\Core\Contract\DatabaseInterface;
use RoboPackage\Core\Exception\RoboPackageRuntimeException;

/**
 * Define the database command trait.
 */
trait DatabaseCommandTrait
{
    use TokenTrait;

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
            $this->processDatabaseProperties($database, $connection);

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
     * @param string $connection
     *   The database connection (e.g. internal, external).
     *
     * @return void
     */
    protected function processDatabaseProperties(
        array &$database,
        string $connection
    ): void {
        $tokens = ['connection' => $connection];

        foreach ($database as &$value) {
            if (!is_array($value) || !isset($value['type'])) {
                continue;
            }
            $type = $value['type'];

            if ($type === 'command' && isset($value['configuration'])) {
                $configuration = $value['configuration'];
                $value = $this->executeCommand(
                    $configuration['command'],
                    $tokens
                );
            }

            if ($type === 'expression' && isset($value['configuration'])) {
                $configuration = $value['configuration'];

                if (isset($configuration['data']['type'])) {
                    $data = $configuration['data'];

                    if (
                        $data['type'] === 'command'
                        && isset($data['command'], $configuration['expression'])
                        && $expressionData = $this->executeCommand($data['command'], $tokens)
                    ) {
                        $value = $this->executeExpression(
                            $expressionData,
                            $configuration['expression'],
                            $tokens
                        );
                    }
                }
            }
        }
    }

    /**
     * Execute a command.
     *
     * @param string $command
     *   The command to execute.
     * @param array $tokens
     *   An array of tokens used for replacements.
     *
     * @return string|null
     */
    protected function executeCommand(
        string $command,
        array $tokens = []
    ): ?string {
        $command = $this->replaceToken($command, $tokens);

        $task = $this->taskExec($command)
            ->silent(true)
            ->printOutput(false)
            ->run();

        return $task->wasSuccessful()
            ? $task->getMessage()
            : null;
    }

    /**
     * Execute the expression.
     *
     * @param string $data
     *   The JSON string data.
     * @param string $expression
     *   The JMES expression.
     * @param array $tokens
     *   An array of tokens.
     *
     * @return string|null
     *   The expression value extracted.
     */
    protected function executeExpression(
        string $data,
        string $expression,
        array $tokens = []
    ): mixed {
        try {
            if ($json = json_decode($data, true, 512, JSON_THROW_ON_ERROR)) {
                $expression = $this->replaceToken($expression, $tokens);
                $value = Env::search($expression, $json);

                if (!is_scalar($value)) {
                    throw new RoboPackageRuntimeException(
                        'The expression value is an invalid type.'
                    );
                }

                return $value;
            }
        } catch (\Exception $exception) {
            new RoboPackageRuntimeException(sprintf(
                'The following exception: %s was thrown while running the expression.',
                $exception->getMessage()
            ));
        }

        return null;
    }
}
