<?php

declare(strict_types=1);

namespace RoboPackage\Core\Datastore;

use Symfony\Component\Yaml\Yaml;

/**
 * Define the YAML datastore.
 */
class YamlDatastore extends DatastoreBase
{
    /**
     * YAML dump inline value.
     *
     * @var int
     */
    protected int $inline = 2;

    /**
     * YAML dump indent value.
     *
     * @var int
     */
    protected int $indent = 4;

    /**
     * Set the YAML dump inline value.
     *
     * @param int $inline
     *   The inline integer.
     *
     * @return $this
     */
    public function setInline(int $inline): self
    {
        $this->inline = $inline;

        return $this;
    }

    /**
     * Set the YAML dump indent value.
     *
     * @param int $indent
     *   The indent integer.
     *
     * @return $this
     */
    public function setIndent(int $indent): self
    {
        $this->indent = $indent;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    protected function transformInput(string|array $content): string
    {
        return Yaml::dump($content, $this->inline, $this->indent);
    }

    /**
     * {@inheritDoc}
     */
    protected function transformOutput(string $content): array
    {
        return Yaml::parse($content);
    }
}
