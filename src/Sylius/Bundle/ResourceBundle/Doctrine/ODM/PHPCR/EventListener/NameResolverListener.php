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
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;

/**
 * Handles the resolution of the PHPCR node name field.
 *
 * If a node already exists with the same name, then a numerical index will be
 * appended to the name.
 */
class NameResolverListener
{
    /**
     * @var DocumentManagerInterface
     */
    private $documentManager;

    public function __construct(
        DocumentManagerInterface $documentManager
    ) {
        $this->documentManager = $documentManager;
    }

    public function onEvent(ResourceControllerEvent $event)
    {
        $document = $event->getSubject();

        $metadata = $this->documentManager->getClassMetadata(get_class($document));

        if ($metadata->idGenerator !== ClassMetadata::GENERATOR_TYPE_PARENT) {
            throw new \RuntimeException(sprintf(
'Document of class "%s" must be using the GENERATOR_TYPE_PARENT identificatio strategy (value %s), it is current using "%s" (this may be an automatic configuration: be sure to map both the `nodename` and the `parentDocument`).',
                get_class($document),
                ClassMetadata::GENERATOR_TYPE_PARENT,
                $metadata->idGenerator
            ));
        }

        // NOTE: that the PHPCR-ODM requires these two fields to be set when
        //       when the GENERATOR_TYPE_PARENT "ID" strategy is used.
        $nameField = $metadata->nodename;
        $parentField = $metadata->parentMapping;

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
                $metadata->setFieldValue($document, $nameField, $candidateName);

                return;
            }

            $candidateName = sprintf('%s-%d', $baseCandidateName, $index);
            ++$index;
        }
    }
}
