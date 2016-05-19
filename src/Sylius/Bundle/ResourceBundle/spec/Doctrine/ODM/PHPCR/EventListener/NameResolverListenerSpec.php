<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Doctrine\ODM\PHPCR\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Doctrine\ODM\PHPCR\EventListener\NameResolverListener;
use Doctrine\ODM\PHPCR\Mapping\ClassMetadata;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Doctrine\ODM\PHPCR\DocumentManagerInterface;
use PHPCR\NodeInterface;

/**
 * @author Daniel Leech <daniel@dantleech.com>
 */
class NameResolverListenerSpec extends ObjectBehavior
{
    function let(
        DocumentManagerInterface $documentManager
    )
    {
        $this->beConstructedWith(
            $documentManager
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(NameResolverListener::class);
    }

    function it_throws_an_exception_when_the_generator_type_is_not_parent(
        DocumentManagerInterface $documentManager,
        ResourceControllerEvent $event,
        ClassMetadata $metadata
    )
    {
        $document = new \stdClass();
        $event->getSubject()->willReturn($document);
        $documentManager->getClassMetadata('stdClass')->willReturn($metadata);
        $metadata->idGenerator = 'foo';

        $this->shouldThrow(new \RuntimeException('Document of class "stdClass" must be using the GENERATOR_TYPE_PARENT identificatio strategy (value 3), it is current using "foo" (this may be an automatic configuration: be sure to map both the `nodename` and the `parentDocument`).'))->during(
            'onEvent', [ $event ]
        );
    }

    function it_should_retain_the_original_name_when_no_conflict_exists(
        DocumentManagerInterface $documentManager,
        ResourceControllerEvent $event,
        ClassMetadata $metadata,
        NodeInterface $node
    )
    {
        $document = new \stdClass();
        $parentDocument = new \stdClass();
        $event->getSubject()->willReturn($document);
        $documentManager->getClassMetadata('stdClass')->willReturn($metadata);
        $metadata->idGenerator = ClassMetadata::GENERATOR_TYPE_PARENT;
        $metadata->nodename = 'title';
        $metadata->parentMapping = 'parent';
        $metadata->getFieldValue($document, 'parent')->willReturn($parentDocument);
        $documentManager->getNodeForDocument($parentDocument)
            ->willReturn($node);
        $node->getPath()->willReturn('/path/to');
        $metadata->getFieldValue($document, 'title')->willReturn('Hello World');

        $documentManager->find(null, '/path/to/Hello World')->willReturn(null);
        $metadata->setFieldValue($document, 'title', 'Hello World')->shouldBeCalled();

        $this->onEvent($event);
    }

    function it_should_auto_increment_the_name_if_a_conflict_exists(
        DocumentManagerInterface $documentManager,
        ResourceControllerEvent $event,
        ClassMetadata $metadata,
        NodeInterface $node
    )
    {
        $document = new \stdClass();
        $parentDocument = new \stdClass();
        $existingDocument = new \stdClass();

        $event->getSubject()->willReturn($document);
        $documentManager->getClassMetadata('stdClass')->willReturn($metadata);
        $metadata->idGenerator = ClassMetadata::GENERATOR_TYPE_PARENT;
        $metadata->nodename = 'title';
        $metadata->parentMapping = 'parent';
        $metadata->getFieldValue($document, 'parent')->willReturn($parentDocument);
        $documentManager->getNodeForDocument($parentDocument)
            ->willReturn($node);
        $node->getPath()->willReturn('/path/to');
        $metadata->getFieldValue($document, 'title')->willReturn('Hello World');

        $documentManager->find(null, '/path/to/Hello World')->willReturn(
            $existingDocument
        );
        $documentManager->find(null, '/path/to/Hello World-1')->willReturn(
            $existingDocument
        );
        $documentManager->find(null, '/path/to/Hello World-2')->willReturn(
            $existingDocument
        );
        $documentManager->find(null, '/path/to/Hello World-3')->willReturn(
            null
        );

        $metadata->setFieldValue($document, 'title', 'Hello World-3')->shouldBeCalled();

        $this->onEvent($event);
    }
}
