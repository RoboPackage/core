<?php

declare(strict_types=1);

namespace RoboPackage\Core\Robo\Plugin\Commands;

use Robo\Tasks;
use Robo\Symfony\ConsoleIO;
use RoboPackage\Core\RoboPackage;
use Robo\Contract\ConfigAwareInterface;
use RoboPackage\Core\Service\DatabaseLauncher;
use RoboPackage\Core\Traits\DatabaseCommandTrait;
use RoboPackage\Core\Plugin\Manager\ExecutableManager;
use RoboPackage\Core\Contract\MySqlExecutableInterface;
use RoboPackage\Core\Contract\MySqlDumpExecutableInterface;
use RoboPackage\Core\Exception\RoboPackageRuntimeException;

/**
 * Define the database commands.
 */
class DatabaseCommands extends Tasks implements ConfigAwareInterface
{
    use DatabaseCommandTrait;

    /**
     * @var \RoboPackage\Core\Plugin\Manager\ExecutableManager
     */
    protected ExecutableManager $executableManager;

    /**
     * The class constructor.
     */
    public function __construct()
    {
        $this->executableManager = RoboPackage::getContainer()->get('executableManager');
    }

    /**
     * Import the project database.
     *
     * @param string $importFile
     *   The path to the database import file.
     * @param string $databaseKey
     *   The project database key in the configuration.
     */
    public function dbImport(
        ConsoleIO $io,
        string $importFile,
        string $databaseKey = 'primary'
    ): void {
        try {
            if (!file_exists($importFile)) {
                throw new RoboPackageRuntimeException(
                    'Invalid database import file.'
                );
            }

            if (
                ($database = $this->getDatabase($databaseKey))
                && $database->isValid()
                && $mySqlInstance = $this->getMySqlExecutable()
            ) {
                $unpackedGzippedFile = false;

                if ($database::isGzipped($importFile)) {
                    $unpackedGzippedFile = true;
                    $importFile = $this->unpackGzippedFile(
                        $importFile
                    );
                }
                $mysqlCommand = $mySqlInstance
                    ->host($database->getHost())
                    ->port($database->getPort())
                    ->user($database->getUsername())
                    ->password($database->getPassword())
                    ->database($database->getDatabase())
                    ->build();

                $result = $this->taskExec("$mysqlCommand < $importFile")->run();

                if ($result->wasSuccessful()) {
                    if ($unpackedGzippedFile) {
                        $this->_remove($importFile);
                    }
                    $io->success(
                        'The database was successfully imported!'
                    );
                }
            }
        } catch (\Exception $exception) {
            $io->error($exception->getMessage());
        }
    }

    /**
     * Export the project database.
     *
     * @param string $exportPath
     *   The project database export path.
     * @param string $databaseKey
     *   The project database key in the configuration.
     */
    public function dbExport(
        ConsoleIO $io,
        string $exportPath,
        string $exportFile = 'db',
        string $databaseKey = 'primary'
    ): void {
        try {
            if (!file_exists($exportPath)) {
                throw new RoboPackageRuntimeException(
                    'Invalid database export path.'
                );
            }

            if (
                ($database = $this->getDatabase($databaseKey))
                && $database->isValid()
                && $mysqlDumpInstance = $this->getMySqlDumpExecutable()
            ) {
                $mysqlDump = $mysqlDumpInstance
                    ->host($database->getHost())
                    ->port($database->getPort())
                    ->user($database->getUsername())
                    ->password($database->getPassword())
                    ->database($database->getDatabase())
                    ->noTablespaces()
                    ->build();

                $dbFilename = "$exportPath/$exportFile.sql.gz";
                $result = $this->taskExec("$mysqlDump | gzip -c > $dbFilename")
                    ->run();

                if ($result->wasSuccessful()) {
                    $io->success(
                        'The database was successfully exported!'
                    );
                }
            }
        } catch (\Exception $exception) {
            $io->error($exception->getMessage());
        }
    }

    /**
     * Launch the project database.
     *
     * @param string|null $appName
     *   The database application e.g (table_plus, sequel_pro, sequel_ace).
     * @param string $databaseKey
     *   The project database key in the configuration.
     * @param string $databaseConnection
     *   The project database connection type (e.g. external, internal)
     */
    public function dbLaunch(
        ConsoleIO $io,
        string $appName = null,
        string $databaseKey = 'primary',
        string $databaseConnection = 'external'
    ): void {
        try {
            if (
                ($database = $this->getDatabase($databaseKey, $databaseConnection))
                && $database->isValid()
            ) {
                $databaseLauncher = new DatabaseLauncher();
                $appOptions = $databaseLauncher->applicationOptions();

                if (count($appOptions) === 0) {
                    throw new RoboPackageRuntimeException(
                        'No supported database applications found!'
                    );
                }

                if (!isset($appName)) {
                    $default = array_key_first($appOptions);

                    $appName = count($appOptions) === 1
                        ? $default
                        : $io->choice(
                            'Select the database application',
                            $appOptions,
                            $default
                        );
                }

                $this->taskExec($databaseLauncher->launch(
                    $appName,
                    $database
                ))->run();
            }
        } catch (\Exception $exception) {
            $io->error($exception->getMessage());
        }
    }

    /**
     * Unpack a gzipped file.
     *
     * @param string $filepath
     *   The path to the gzipped file.
     *
     * @return string
     *   The unpacked file path.
     */
    protected function unpackGzippedFile(string $filepath): string
    {
        if ($this->_exec("gunzip -dk $filepath")->wasSuccessful()) {
            $filepath = substr($filepath, 0, strrpos($filepath, '.'));

            if (!file_exists($filepath)) {
                throw new \RuntimeException(
                    'An error occurred unpacking the gzipped database file!'
                );
            }
        }

        return $filepath;
    }

    /**
     * Get the MySql executable.
     *
     * @return \RoboPackage\Core\Contract\MySqlExecutableInterface|null
     */
    protected function getMySqlExecutable(): ?MySqlExecutableInterface
    {
        return $this->executableManager->createInstance('mysql');
    }

    /**
     * Get the MySql dump executable.
     *
     * @return \RoboPackage\Core\Contract\MySqlDumpExecutableInterface|null
     */
    protected function getMySqlDumpExecutable(): ?MySqlDumpExecutableInterface
    {
        return $this->executableManager->createInstance('mysql_dump');
    }
}
