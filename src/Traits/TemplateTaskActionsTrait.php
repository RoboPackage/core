<?php

declare(strict_types=1);

namespace RoboPackage\Core\Traits;

use Robo\Result;
use RoboPackage\Core\Contract\TemplatePluginInterface;
use RoboPackage\Core\Exception\RoboPackageRuntimeException;

/**
 * The trait provides the common functionality for templates.
 */
trait TemplateTaskActionsTrait
{
    /**
     * Copy the template to the specified path.
     *
     * @param string $toPath
     *   The path where the template is copied to.
     * @param \RoboPackage\Core\Contract\TemplatePluginInterface $template
     *   The template plugin instance.
     *
     * @return \Robo\Result|bool
     */
    protected function copyToPath(
        string $toPath,
        TemplatePluginInterface $template
    ): ?Result {
        $io = $this->io();
        if ($fromPath = $template->getFilePath()) {
            $collection = $this->collectionBuilder($io);

            $collection->addTask(
                $this->taskFilesystemStack()->copy(
                    $fromPath, $toPath, true
                )
            );

            if ($variables = $template->getVariables()) {
                $collection->completion($this->taskReplaceInFile($toPath)
                    ->from(array_keys($variables))
                    ->to(array_values($variables))
                );
            }

            return $collection->run();
        }

        throw new RoboPackageRuntimeException(sprintf(
            'Unable to copy the template to the following path: %s.',
            $toPath
        ));
    }
}
