<?php

declare(strict_types=1);

namespace RoboPackage\Core\Datastore;

/**
 * Define the JSON datastore.
 */
class JsonDatastore extends DatastoreBase
{
    /**
     * @inheritDoc
     *
     * @throws \JsonException
     */
    protected function transformInput(string|array $content): string
    {
        return json_encode(
            $content,
            JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }

    /**
     * @inheritDoc
     *
     * @throws \JsonException
     */
    protected function transformOutput(string $content): array
    {
        return json_decode(
            $content,
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }
}
