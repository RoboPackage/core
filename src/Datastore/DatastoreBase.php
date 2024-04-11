<?php

declare(strict_types=1);

namespace RoboPackage\Core\Datastore;

use RoboPackage\Core\Contract\DatastoreInterface;
use RoboPackage\Core\Exception\DatastoreRuntimeException;

/**
 * Define the datastore base class.
 */
abstract class DatastoreBase implements DatastoreInterface
{
    /**
     * The datastore merge flag.
     *
     * @var bool
     */
    protected bool $merge = false;

    /**
     * The datastore file.
     *
     * @var \SplFileObject
     */
    protected \SplFileObject $datastoreFile;

    /**
     * Define the datastore file constructor.
     *
     * @param string $filepath
     *   The file path where the data resides.
     */
    public function __construct(string $filepath)
    {
        if (!file_exists($filepath)) {
            $fileDirectory = dirname($filepath);

            if (!is_dir($fileDirectory) && !mkdir($fileDirectory, 0775, true)) {
                throw new DatastoreRuntimeException(sprintf(
                    'Directory "%s" was not created',
                    $fileDirectory
                ));
            }
            touch($filepath);
        }
        $this->datastoreFile = new \SplFileObject($filepath);
    }

    /**
     * @inheritDoc
     */
    public function merge(): static
    {
        $this->merge = true;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function read(): array|string
    {
        return $this->transformOutput(
            file_get_contents(
                $this->getDatastorePath()
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function write(string|array $content): bool
    {
        if (is_array($content) && $this->merge) {
            $content = array_replace_recursive(
                $this->read(),
                $content
            );
        }

        return file_put_contents(
            $this->getDatastorePath(),
            $this->transformInput($content)
        ) !== false;
    }

    /**
     * Get the datastore filesystem path.
     *
     * Only needed due to the vfsStream not being able
     * to test using the splFileInfo::getRealPath() method.
     *
     * @return string
     *   The path to the datastore file.
     */
    protected function getDatastorePath(): string
    {
        $fileObject = $this->datastoreFile;

        return $fileObject->getRealPath() !== false
            ? $fileObject->getRealPath()
            : "{$fileObject->getPath()}/{$fileObject->getFilename()}";
    }

    /**
     * Transform the content before writing input.
     *
     * @param $content
     *   The content to transform.
     *
     * @return string
     *   The transform content input.
     */
    abstract protected function transformInput(string|array $content): string;

    /**
     * Transform the content before reading output.
     *
     * @param $content
     *   The content to transform.
     *
     * @return array
     *   The transform content output.
     */
    abstract protected function transformOutput(string $content): array;
}
