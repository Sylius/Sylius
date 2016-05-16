<?php

namespace Sylius\Bundle\ResourceBundle\Doctrine\ODM\PHPCR\Form\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Doctrine\ODM\PHPCR\DocumentManagerInterface;
use PHPCR\Util\NodeHelper;

/**
 * Automatically set the parent after submitting the form.
 *
 * TODO: Allow `rewrite` (or similar) option to allow the forceful setting
 *       of the parent even if the parent is already set (and is different from
 *       the candidate parent).
 * TODO: Only applies when ClassMetadata::GENERATOR_TYPE_PARENT strategy is used.
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class DefaultPathSubscriber implements EventSubscriberInterface
{
    /**
     * @var DocumentManagerInterface
     */
    private $documentManager;

    /**
     * @var string
     */
    private $defaultPath;

    /**
     * @var bool
     */
    private $autocreate;

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::POST_SUBMIT => 'onPostSubmit',
        ];
    }

    /**
     * @param DocumentManagerInterface $documentManager
     * @param mixed $defaultPath
     * @param mixed $autocreate
     */
    public function __construct(
        DocumentManagerInterface $documentManager,
        $defaultPath,
        $autocreate
    )
    {
        $this->documentManager = $documentManager;
        $this->defaultPath = $defaultPath;
        $this->autocreate = $autocreate;
    }


    public function onPostSubmit(FormEvent $event)
    {
        $document = $event->getData();
        if (!is_object($document)) {
            throw new \InvalidArgumentException(sprintf(
                'Expected an object, got a "%s"',
                gettype($document)
            ));
        }

        $metadata = $this->documentManager->getClassMetadata(get_class($document));

        if (!$this->defaultPath) {
            return;
        }

        if (!$parentField = $metadata->parentMapping) {
            throw new \RuntimeException(sprintf(
                'A default parent path has been specified, but no parent mapping has been applied to "%s"',
                get_class($document)
            ));
        }

        $actualParent = $metadata->getFieldValue($document, $parentField);

        if ($actualParent) {
            return;
        }

        $parentDocument = $this->documentManager->find(null, $this->defaultPath);

        if (true === $this->autocreate && null === $parentDocument) {
            NodeHelper::createPath($this->documentManager->getPhpcrSession(), $this->defaultPath);
            $parentDocument = $this->documentManager->find(null, $this->defaultPath);
        }

        if (null === $parentDocument) {
            throw new \RuntimeException(sprintf(
                'Parent path was null and the default parent path "%s" does not exist.`autocreate` was set to "%s"',
                $this->defaultPath, $this->autocreate ? 'true' : 'false'
            ));
        }

        $metadata->setFieldValue($document, $parentField, $parentDocument);
    }
}
