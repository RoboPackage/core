<?php

declare(strict_types=1);

namespace RoboPackage\Core\Database;

use RoboPackage\Core\Contract\DatabaseInterface;
use RoboPackage\Core\Exception\RoboPackageRuntimeException;

/**
 * Define the database object.
 */
class Database implements DatabaseInterface
{
    /**
     * @var string
     */
    protected string $type;

    /**
     * @var string
     */
    protected string $host;

    /**
     * @var int
     */
    protected int $port;

    /**
     * @var string
     */
    protected string $database;

    /**
     * @var string
     */
    protected string $username;

    /**
     * @var string
     */
    protected string $password;

    /**
     * Define the database required properties.
     */
    protected const REQUIRED_PROPERTIES = [
        'host',
        'port',
        'database',
        'username',
        'password'
    ];

    /**
     * @inheritDoc
     */
    public function isValid(): bool
    {
        $properties = $this->getProperties();

        foreach (static::REQUIRED_PROPERTIES as $propertyKey) {
            $value = $properties[$propertyKey] ?? null;

            if (!isset($value)) {
                throw new RoboPackageRuntimeException(sprintf(
                    'The database %s property is required!',
                    $propertyKey
                ));
            }
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public static function isGzipped(string $filepath): bool
    {
        if (!file_exists($filepath)) {
            throw new \InvalidArgumentException(
                'The database file does not exist.'
            );
        }
        $contentType = mime_content_type($filepath);

        $mimeType = substr(
            $contentType,
            strpos($contentType, '/') + 1
        );

        return $mimeType === 'x-gzip' || $mimeType === 'gzip';
    }

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return $this->type ?? 'mysql';
    }

    /**
     * @inheritDoc
     */
    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @inheritDoc
     */
    public function setHost(string $host): static
    {
        $this->host = $host;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @inheritDoc
     */
    public function setPort(int $port): static
    {
        $this->port = $port;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDatabase(): string
    {
        return $this->database;
    }

    /**
     * @inheritDoc
     */
    public function setDatabase(string $name): static
    {
        $this->database = $name;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @inheritDoc
     */
    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @inheritDoc
     */
    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the database connection properties.
     *
     * @return array
     *   An array of the database connection details.
     */
    public function getProperties(): array
    {
        return get_object_vars($this);
    }

    /**
     * @inheritDoc
     */
    public function getConnectionUrl(): string
    {
        $protocol = ($this->getType());
        $location = "{$this->getHost()}:{$this->getPort()}";
        $credential = "{$this->getUsername()}:{$this->getPassword()}";

        return "$protocol://$credential@$location/{$this->getDatabase()}";
    }
}
