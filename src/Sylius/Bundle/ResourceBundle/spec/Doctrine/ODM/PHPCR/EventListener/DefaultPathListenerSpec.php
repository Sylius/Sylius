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
use Sylius\Bundle\ResourceBundle\Doctrine\ODM\PHPCR\EventListener\DefaultPathListener;
use Doctrine\ODM\PHPCR\Mapping\ClassMetadata;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Resource\Metadata\Registry;
use Sylius\Component\Resource\Metadata\Metadata;
use Doctrine\ODM\PHPCR\DocumentManagerInterface;
use PHPCR\NodeInterface;
use PHPCR\SessionInterface;

/**
 * @author Daniel Leech <daniel@dantleech.com>
 */
class DefaultPathListenerSpec extends ObjectBehavior
{
    function let(
        Registry $registry,
        DocumentManagerInterface $documentManager
    )
    {
        $this->beConstructedWith(
            $registry,
            $documentManager
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(DefaultPathListener::class);
    }

    function it_should_return_early_if_default_parent_path_is_null(
        ResourceControllerEvent $event,
        Registry $registry,
        Metadata $resourceMetadata,
        DocumentManagerInterface $documentManager
    )
    {
        $event->getSubject()->willReturn(new \stdClass());
        $registry->getByClass(\stdClass::class)->willReturn(
            $resourceMetadata
        );

        $resourceMetadata->getParameter('options')->willReturn([
        ]);

        $this->onPreCreate($event);
        $documentManager->getClassMetadata(Argument::cetera())->shouldNotHaveBeenCalled();
    }

    function it_should_throw_an_exception_if_no_parent_mapping_exists(
        ResourceControllerEvent $event,
        Registry $registry,
        Metadata $resourceMetadata,
        ClassMetadata $documentMetadata,
        DocumentManagerInterface $documentManager
    )
    {
        $event->getSubject()->willReturn(new \stdClass());
        $registry->getByClass(\stdClass::class)->willReturn(
            $resourceMetadata
        );
        $resourceMetadata->getParameter('options')->willReturn([
            'default_parent_path' => '/path/to',
        ]);
        $documentManager->getClassMetadata(\stdClass::class)->willReturn(
            $documentMetadata
        );
        $documentMetadata->parentMapping = null;

        $this->shouldThrow(new \RuntimeException(
            'A default parent path has been specified, but no parent mapping has been applied to document "stdClass"'
        ))->during(
            'onPreCreate',
            [ $event ]
        );
    }

    function it_should_throw_an_exception_if_the_parent_does_not_exist_and_autocreate_is_false(
        ResourceControllerEvent $event,
        Registry $registry,
        Metadata $resourceMetadata,
        ClassMetadata $documentMetadata,
        DocumentManagerInterface $documentManager
    )
    {
        $event->getSubject()->willReturn(new \stdClass());
        $registry->getByClass(\stdClass::class)->willReturn(
            $resourceMetadata
        );
        $resourceMetadata->getParameter('options')->willReturn([
            'default_parent_path' => '/path/to',
        ]);
        $documentManager->getClassMetadata(\stdClass::class)->willReturn(
            $documentMetadata
        );
        $documentMetadata->parentMapping = 'parent';
        $documentManager->find(null, '/path/to')->willReturn(null);

        $this->shouldThrow(new \RuntimeException(
            'Document at default parent path "/path/to" does not exist. `autocreate` was set to "false"'
        ))->during(
            'onPreCreate',
            [ $event ]
        );
    }

    function it_should_set_the_parent_document(
        ResourceControllerEvent $event,
        Registry $registry,
        Metadata $resourceMetadata,
        ClassMetadata $documentMetadata,
        DocumentManagerInterface $documentManager
    )
    {
        $subjectDocument = new \stdClass();
        $parentDocument = new \stdClass();

        $event->getSubject()->willReturn($subjectDocument);
        $registry->getByClass(\stdClass::class)->willReturn(
            $resourceMetadata
        );
        $resourceMetadata->getParameter('options')->willReturn([
            'default_parent_path' => '/path/to',
        ]);
        $documentManager->getClassMetadata(\stdClass::class)->willReturn(
            $documentMetadata
        );
        $documentMetadata->parentMapping = 'parent';
        $documentManager->find(null, '/path/to')->willReturn($parentDocument);
        $documentMetadata->setFieldValue($subjectDocument, 'parent', $parentDocument);
        $this->onPreCreate($event);
    }

    function it_should_autocreate_and_set_the_parent_document(
        ResourceControllerEvent $event,
        Registry $registry,
        Metadata $resourceMetadata,
        ClassMetadata $documentMetadata,
        DocumentManagerInterface $documentManager,
        SessionInterface $session,
        NodeInterface $node
    )
    {
        $subjectDocument = new \stdClass();
        $parentDocument = new \stdClass();

        $event->getSubject()->willReturn($subjectDocument);
        $registry->getByClass(\stdClass::class)->willReturn(
            $resourceMetadata
        );
        $resourceMetadata->getParameter('options')->willReturn([
            'default_parent_path' => '/path/to',
            'autocreate' => true,
        ]);
        $documentManager->getClassMetadata(\stdClass::class)->willReturn(
            $documentMetadata
        );
        $documentMetadata->parentMapping = 'parent';
        $documentManager->find(null, '/path/to')->willReturn(null, $parentDocument);
        $documentManager->getPhpcrSession()->willReturn($session);
        $session->getRootNode()->willReturn($node);

        // we need to mock the behavior of the node helper
        // see: https://github.com/phpcr/phpcr-utils/issues/106
        $node->hasNode(Argument::cetera())->willReturn(true);
        $node->getNode(Argument::cetera())
            ->willReturn($node)
            ->shouldBeCalledTimes(2);


        $documentMetadata->setFieldValue($subjectDocument, 'parent', $parentDocument);
        $this->onPreCreate($event);
    }

    function it_should_set_the_parent_document_if_force_is_true_and_the_parent_is_already_set(
        ResourceControllerEvent $event,
        Registry $registry,
        Metadata $resourceMetadata,
        ClassMetadata $documentMetadata,
        DocumentManagerInterface $documentManager
    )
    {
        $subjectDocument = new \stdClass();
        $parentDocument = new \stdClass();

        $event->getSubject()->willReturn($subjectDocument);
        $registry->getByClass(\stdClass::class)->willReturn(
            $resourceMetadata
        );
        $resourceMetadata->getParameter('options')->willReturn([
            'default_parent_path' => '/path/to',
            'force' => true,
        ]);

        $documentManager->getClassMetadata(\stdClass::class)->willReturn(
            $documentMetadata
        );

        $documentMetadata->getFieldValue($subjectDocument, 'parent')->shouldNotBeCalled();
        $documentMetadata->setFieldValue($subjectDocument, 'parent', $parentDocument)->shouldBeCalled();

        $documentMetadata->parentMapping = 'parent';
        $documentManager->find(null, '/path/to')->willReturn($parentDocument);
        $this->onPreCreate($event);
    }

    function it_should_return_early_if_force_is_false_and_subject_already_has_a_parent(
        ResourceControllerEvent $event,
        Registry $registry,
        Metadata $resourceMetadata,
        ClassMetadata $documentMetadata,
        DocumentManagerInterface $documentManager
    )
    {
        $subjectDocument = new \stdClass();

        $event->getSubject()->willReturn($subjectDocument);
        $registry->getByClass(\stdClass::class)->willReturn(
            $resourceMetadata
        );
        $resourceMetadata->getParameter('options')->willReturn([
            'default_parent_path' => '/path/to',
        ]);

        $documentManager->getClassMetadata(\stdClass::class)->willReturn(
            $documentMetadata
        );
        $documentMetadata->parentMapping = 'parent';
        $documentMetadata->getFieldValue($subjectDocument, 'parent')
            ->willReturn(new \stdClass());

        $documentManager->find(null, '/path/to')->shouldNotBeCalled();

        $this->onPreCreate($event);
    }
}

