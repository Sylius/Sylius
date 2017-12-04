<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ResourceBundle\Doctrine\ODM\PHPCR\EventListener;

use Doctrine\ODM\PHPCR\DocumentManagerInterface;
use Doctrine\ODM\PHPCR\Mapping\ClassMetadata;
use PHPCR\Util\NodeHelper;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;

/**
 * Automatically set the parent brefore the creation.
 */
class DefaultParentListener
{
    /**
     * @var DocumentManagerInterface
     */
    private $documentManager;

    /**
     * @var string
     */
    private $parentPath;

    /**
     * @var bool
     */
    private $autocreate;

    /**
     * @var bool
     */
    private $force;

    /**
     * @param DocumentManagerInterface $documentManager
     * @param string $parentPath
     * @param bool $autocreate
     * @param bool $force
     */
    public function __construct(
        DocumentManagerInterface $documentManager,
        $parentPath,
        $autocreate = false,
        $force = false
    ) {
        $this->documentManager = $documentManager;
        $this->parentPath = $parentPath;
        $this->autocreate = $autocreate;
        $this->force = $force;
    }

    /**
     * @param ResourceControllerEvent $event
     */
    public function onPreCreate(ResourceControllerEvent $event)
    {
        $document = $event->getSubject();
        $class = get_class($document);

        $this->resolveParent(
            $document,
            $this->documentManager->getClassMetadata($class)
        );
    }

    private function resolveParent(
        $document,
        ClassMetadata $metadata
    ) {
        if (!$parentField = $metadata->parentMapping) {
            throw new \RuntimeException(sprintf(
                'A default parent path has been specified, but no parent mapping has been applied to document "%s"',
                get_class($document)
            ));
        }

        if (false === $this->force) {
            $actualParent = $metadata->getFieldValue($document, $parentField);

            if ($actualParent) {
                return;
            }
        }

        $parentDocument = $this->documentManager->find(null, $this->parentPath);

        if (true === $this->autocreate && null === $parentDocument) {
            NodeHelper::createPath($this->documentManager->getPhpcrSession(), $this->parentPath);
            $parentDocument = $this->documentManager->find(null, $this->parentPath);
        }

        if (null === $parentDocument) {
            throw new \RuntimeException(sprintf(
                'Document at default parent path "%s" does not exist. `autocreate` was set to "%s"',
                $this->parentPath, $this->autocreate ? 'true' : 'false'
            ));
        }

        $metadata->setFieldValue($document, $parentField, $parentDocument);
    }
}
