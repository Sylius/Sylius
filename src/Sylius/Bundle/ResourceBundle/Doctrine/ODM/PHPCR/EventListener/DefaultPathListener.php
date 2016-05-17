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
 * TODO: Allow `rewrite` (or similar) option to allow the forceful setting
 *       of the parent even if the parent is already set (and is different from
 *       the candidate parent).
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


    public function onPreCreate(ResourceControllerEvent $event)
    {
        $document = $event->getSubject();

        if (!is_object($document)) {
            throw new \InvalidArgumentException(sprintf(
                'Expected an object, got a "%s"',
                gettype($document)
            ));
        }

        $class = get_class($document);

        $resourceMetadata = $this->registry->getByClass($class);
        $documentMetadata = $this->documentManager->getClassMetadata(get_class($document));

        $options = array_merge(
            [
                'default_parent_path' => null,
                'autocreate' => false,
            ],
            $resourceMetadata->getParameter('options')
        );

        if (null === $options['default_parent_path']) {
            return;
        }

        $this->resolveParent(
            $document,
            $documentMetadata,
            $options['default_parent_path'],
            $options['autocreate']
        );
    }

    private function resolveParent(
        $document,
        ClassMetadata $metadata,
        $defaultParentPath,
        $autocreate
    )
    {
        if (!$parentField = $metadata->parentMapping) {
            throw new \RuntimeException(sprintf(
                'A default parent path has been specified, but no parent mapping has been applied to document "%s"',
                get_class($document)
            ));
        }

        $actualParent = $metadata->getFieldValue($document, $parentField);

        if ($actualParent) {
            return;
        }

        $parentDocument = $this->documentManager->find(null, $defaultParentPath);

        if (true === $autocreate && null === $parentDocument) {
            NodeHelper::createPath($this->documentManager->getPhpcrSession(), $this->defaultPath);
            $parentDocument = $this->documentManager->find(null, $this->defaultPath);
        }

        if (null === $parentDocument) {
            throw new \RuntimeException(sprintf(
                'Parent path was null and the default parent path "%s" does not exist.`autocreate` was set to "%s"',
                $this->defaultPath, $autocreate ? 'true' : 'false'
            ));
        }

        $metadata->setFieldValue($document, $parentField, $parentDocument);
    }
}
