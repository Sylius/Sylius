<?php

namespace Sylius\Bundle\ResourceBundle\Doctrine\ODM\PHPCR\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Doctrine\ODM\PHPCR\DocumentManagerInterface;
use PHPCR\Util\NodeHelper;
use Sylius\Component\Resource\Metadata\Registry;
use Doctrine\ODM\PHPCR\Mapping\ClassMetadata;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;

/**
 * Automatically set the parent brefore the creation.
 *
 * TODO: Only applies when ClassMetadata::GENERATOR_TYPE_PARENT strategy is used.
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class DefaultPathListener
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var DocumentManagerInterface
     */
    private $documentManager;

    /**
     * @param Registry $registry
     * @param DocumentManagerInterface $documentManager
     */
    public function __construct(
        Registry $registry,
        DocumentManagerInterface $documentManager
    )
    {
        $this->registry = $registry;
        $this->documentManager = $documentManager;
    }

    /**
     * @param ResourceControllerEvent $event
     */
    public function onPreCreate(ResourceControllerEvent $event)
    {
        $document = $event->getSubject();

        $class = get_class($document);

        $resourceMetadata = $this->registry->getByClass($class);

        $options = array_merge(
            [
                'default_parent_path' => null,
                'autocreate' => false,
                'force' => false,
            ],
            $resourceMetadata->getParameter('options')
        );

        if (null === $options['default_parent_path']) {
            return;
        }

        $this->resolveParent(
            $document,
            $this->documentManager->getClassMetadata($class),
            $options['default_parent_path'],
            $options['autocreate'],
            $options['force']
        );
    }

    private function resolveParent(
        $document,
        ClassMetadata $metadata,
        $defaultParentPath,
        $autocreate,
        $force
    )
    {
        if (!$parentField = $metadata->parentMapping) {
            throw new \RuntimeException(sprintf(
                'A default parent path has been specified, but no parent mapping has been applied to document "%s"',
                get_class($document)
            ));
        }

        if (false === $force) {
            $actualParent = $metadata->getFieldValue($document, $parentField);

            if ($actualParent) {
                return;
            }
        }

        $parentDocument = $this->documentManager->find(null, $defaultParentPath);

        if (true === $autocreate && null === $parentDocument) {
            $nodeHelper = new NodeHelper();
            $nodeHelper->createPath($this->documentManager->getPhpcrSession(), $defaultParentPath);
            $parentDocument = $this->documentManager->find(null, $defaultParentPath);
        }

        if (null === $parentDocument) {
            throw new \RuntimeException(sprintf(
                'Document at default parent path "%s" does not exist. `autocreate` was set to "%s"',
                $defaultParentPath, $autocreate ? 'true' : 'false'
            ));
        }

        $metadata->setFieldValue($document, $parentField, $parentDocument);
    }
}
