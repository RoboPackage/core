<?php

declare(strict_types=1);

namespace RoboPackage\Core\Service;

use RoboPackage\Core\Contract\DatabaseInterface;

/**
 * Define the database launcher service.
 */
class DatabaseLauncher
{
    /**
     * @var array
     */
    protected array $applications = [];

    /**
     * Launch the database application command.
     *
     * @param string $name
     *   The database application name.
     *
     * @return ?string
     *   The database application open command.
     */
    public function launch(
        string $name,
        DatabaseInterface $database
    ): ?string {
        if (
            ($definition = $this->getApplicationDefinition($name))
            && is_callable($definition['callback'])
        ) {
            return call_user_func(
                $definition['callback'],
                $definition['location'],
                $database
            );
        } else {
            throw new \InvalidArgumentException(sprintf(
                'The database application %s is invalid!',
                $name
            ));
        }
    }

    /**
     * Get the database application options.
     *
     * @return array
     *   An array of the database application options.
     */
    public function applicationOptions(): array
    {
        $options = [];

        foreach ($this->discoverApplications() as $key => $info) {
            if (!isset($info['label'])) {
                continue;
            }
            $options[$key] = $info['label'];
        }

        return $options;
    }

    /**
     * Get the database application definition.
     *
     * @param string $name
     *   The database application machine name.
     *
     * @return array
     *   An array of the database application definition parameters.
     */
    protected function getApplicationDefinition(string $name): array
    {
        return $this->discoverApplications()[$name] ?? [];
    }

    /**
     * Create TablePlus database command.
     *
     * @param string $application
     *   The database application.
     * @param \RoboPackage\Core\Contract\DatabaseInterface $database
     *   The environment database.
     *
     * @return string|null
     *   The TablePlus database open command.
     */
    protected function createTablePlusDatabaseCommand(
        string $application,
        DatabaseInterface $database
    ): ?string {
        if (!$database->isValid()) {
            return null;
        }
        $query = http_build_query([
            'statusColor' => '007F3D',
            'enviroment' => 'local',
            'name' => 'Local Database',
            'tLSMode' => 0,
            'usePrivateKey' => 'true',
            'safeModeLevel' => 0,
            'advancedSafeModeLevel' => 0
        ]);
        $url = $database->getConnectionUrl();

        return "open -a '$application' $url?$query";
    }

    /**
     * Create the sequel (pro/ace) database command.
     *
     * @param string $application
     *   The database application.
     * @param \RoboPackage\Core\Contract\DatabaseInterface $database
     *   The environment database.
     *
     * @return string|null
     *   The sequel database open command.
     */
    protected function createSequelDatabaseCommand(
        string $application,
        DatabaseInterface $database
    ): ?string {
        if (!$database->isValid()) {
            return null;
        }
        $url = $database->getConnectionUrl();

        return "open -a '$application' $url";
    }

    /**
     * The database application definitions.
     *
     * @return array[]
     *   An array of the database application definitions.
     */
    protected function applicationDefinitions(): array
    {
        return [
            'sequel_ace' => [
                'os' => 'Darwin',
                'label' => 'Sequel Ace',
                'locations' => '/Applications/Sequel Ace.app',
                'callback' => function (string $appLocation, $database) {
                    return $this->createSequelDatabaseCommand($appLocation, $database);
                }
            ],
            'sequel_pro' => [
                'os' => 'Darwin',
                'label' => 'Sequel Pro',
                'locations' => '/Applications/Sequel Pro.app',
                'callback' => function (string $appLocation, $database) {
                    return $this->createSequelDatabaseCommand($appLocation, $database);
                }
            ],
            'table_plus' => [
                'os' => 'Darwin',
                'label' => 'TablePlus',
                'locations' => [
                    '/Applications/TablePlus.app',
                    '/Applications/Setapp/TablePlus.app',
                ],
                'callback' => function (string $appLocation, $database) {
                    return $this->createTablePlusDatabaseCommand($appLocation, $database);
                }
            ],
        ];
    }

    /**
     * Discover the database applications.
     *
     * @return array
     *   An array of database applications found on the host system.
     */
    protected function discoverApplications(): array
    {
        if (count($this->applications) === 0) {
            foreach ($this->applicationDefinitions() as $key => $definition) {
                if ($definition['os'] === PHP_OS && isset($definition['locations'])) {
                    $locations = is_array($definition['locations'])
                        ? $definition['locations']
                        : [$definition['locations']];

                    if ($location = $this->resolveApplicationLocation($locations)) {
                        $this->applications[$key] = [
                            'label' => $definition['label'],
                            'callback' => $definition['callback'],
                            'location' => $location
                        ];
                    }
                }
            }
        }

        return $this->applications;
    }

    /**
     * Resolve the database application location.
     *
     * @param array $locations
     *   An array of searchable locations.
     *
     * @return string|null
     *   The database application location on the host file system.
     */
    protected function resolveApplicationLocation(array $locations): ?string
    {
        foreach ($locations as $location) {
            if (!file_exists($location)) {
                continue;
            }
            return $location;
        }

        return null;
    }
}
