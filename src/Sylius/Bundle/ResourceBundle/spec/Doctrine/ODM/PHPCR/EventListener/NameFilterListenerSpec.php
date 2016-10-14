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

use Doctrine\ODM\PHPCR\DocumentManagerInterface;
use Doctrine\ODM\PHPCR\Mapping\ClassMetadata;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Doctrine\ODM\PHPCR\EventListener\NameFilterListener;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;

/**
 * @author Daniel Leech <daniel@dantleech.com>
 */
final class NameFilterListenerSpec extends ObjectBehavior
{
    function let(DocumentManagerInterface $documentManager)
    {
        $this->beConstructedWith($documentManager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(NameFilterListener::class);
    }

    function it_throws_an_exception_if_nodename_is_not_mapped(
        ResourceControllerEvent $event,
        DocumentManagerInterface $documentManager,
        ClassMetadata $metadata
    ) {
        $document = new \stdClass();
        $event->getSubject()->willReturn($document);
        $documentManager->getClassMetadata('stdClass')->willReturn($metadata);
        $metadata->nodename = null;

        $this->shouldThrow(new \RuntimeException('In order to use the node name filter on "stdClass" it is necessary to map a field as the "nodename"'))->during('onEvent', [ $event ]);
    }

    function it_should_clean_the_name(
        ResourceControllerEvent $event,
        DocumentManagerInterface $documentManager,
        ClassMetadata $metadata
    ) {
        $document = new \stdClass();
        $event->getSubject()->willReturn($document);
        $documentManager->getClassMetadata('stdClass')->willReturn($metadata);
        $metadata->nodename = 'foobar';
        $metadata->getFieldValue($document, 'foobar')->willReturn('Hello//Foo');
        $metadata->setFieldValue($document, 'foobar', 'Hello  Foo')->shouldBeCalled();

        $this->onEvent($event);
    }

    function it_should_use_the_given_replacement_char(
        ResourceControllerEvent $event,
        DocumentManagerInterface $documentManager,
        ClassMetadata $metadata
    ) {
        $this->beConstructedWith($documentManager, '_');

        $document = new \stdClass();
        $event->getSubject()->willReturn($document);
        $documentManager->getClassMetadata('stdClass')->willReturn($metadata);
        $metadata->nodename = 'foobar';
        $metadata->getFieldValue($document, 'foobar')->willReturn('Hello//Foo');
        $metadata->setFieldValue($document, 'foobar', 'Hello__Foo')->shouldBeCalled();

        $this->onEvent($event);
    }
}
