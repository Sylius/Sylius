<?php

namespace Sylius\Bundle\ResourceBundle\Doctrine\ODM\PHPCR\Form\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Doctrine\ODM\PHPCR\DocumentManagerInterface;
use PHPCR\Util\NodeHelper;
use Doctrine\ODM\PHPCR\Mapping\ClassMetadata;

/**
 * Handles the resolution of the PHPCR node name field.
 *
 * - If a node already exists with the same name, then a numerical index will
 *   be appended to the name.
 * - Invalid characters will be replaced with "_"
 * 
 * @see http://www.day.com/specs/jcr/2.0/3_Repository_Model.html#3.2.2%20Local%20Names
 * @see https://github.com/phpcr/phpcr-utils/blob/master/src/PHPCR/Util/PathHelper.php#L95
 *
 * TODO: Allow specification of replacement character?
 * TODO: Allow strict mode? i.e. throw exceptions if the node exists or if it
 *       contains illegal characters (in which case a Validator should also be
 *       added).
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class NameResolverSubscriber implements EventSubscriberInterface
{
    /**
     * @var DocumentManagerInterface
     */
    private $documentManager;

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::POST_SUBMIT => 'onPostSubmit',
        ];
    }

    /**
     * @param DocumentManagerInterface $documentManager
     */
    public function __construct(
        DocumentManagerInterface $documentManager
    )
    {
        $this->documentManager = $documentManager;
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

        if ($metadata->idGenerator !== ClassMetadata::GENERATOR_TYPE_PARENT) {
            return;
        }

        // TODO: Throw exception in these two cases? PHPCR-ODM will fail
        //       (later) anyway if either of these are true.
        if (null === $nameField = $metadata->nodename) {
            return;
        }
        if (null === $parentField = $metadata->parentMapping) {
            return;
        }

        $parentDocument = $metadata->getFieldValue($document, $parentField);
        $phpcrNode = $this->documentManager->getNodeForDocument($parentDocument);
        $parentPath = $phpcrNode->getPath();

        $baseCandidateName = $metadata->getFieldValue($document, $nameField);
        $candidateName = $baseCandidateName;

        $index = 1;
        while (true) {
            $candidatePath = sprintf('%s/%s', $parentPath, $candidateName);
            $existing = $this->documentManager->find(null, $candidatePath);

            // if the existing document is the document we are updating, then thats great.
            if ($existing === $document) {
                return;
            }

            if (null === $existing) {
                // Remove any illegal characters
                // TODO: Put this into a helper class and test it.
                $candidateName = preg_replace('/\\/|:|\\[|\\]|\\||\\*/', '_', $candidateName);
                $metadata->setFieldValue($document, $nameField, $candidateName);
                return;
            }

            $candidateName = sprintf('%s-%d', $baseCandidateName, $index);
        }
    }

}
