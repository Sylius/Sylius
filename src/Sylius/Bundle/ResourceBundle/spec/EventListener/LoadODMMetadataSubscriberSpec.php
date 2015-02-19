<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\EventListener;

use Doctrine\ODM\MongoDB\Event\LoadClassMetadataEventArgs;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

require_once __DIR__.'/../Fixture/Entity/Foo.php';
require_once __DIR__ . '/../Fixture/Entity/MappedSuperclass.php';
require_once __DIR__ . '/../Fixture/Entity/Bar.php';

/**
 * @author Benoît Burnichon <bburnichon@gmail.com>
 */
class LoadODMMetadataSubscriberSpec extends ObjectBehavior
{
    function let()
    {
        $classes = array(
            'foo' => array(
                'model' => 'spec\Sylius\Bundle\ResourceBundle\Fixture\Entity\Foo',
                'repository' => 'spec\Sylius\Bundle\ResourceBundle\Fixture\Entity\FooRepository',
            ),
        );

        $this->beConstructedWith(
            $classes
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\EventListener\LoadODMMetadataSubscriber');
    }

    function it_is_doctrine_event_subscriber()
    {
        $this->shouldImplement('Doctrine\Common\EventSubscriber');
    }

    function it_subscribes_events()
    {
        $this::getSubscribedEvents()->shouldReturn(array(
            'loadClassMetadata',
        ));
    }

    function it_should_set_custom_repository_class_and_force_model_to_entity(
        LoadClassMetadataEventArgs $eventArgs,
        ClassMetadata $metadata
    ) {
        $metadata->isMappedSuperclass = true;
        $metadata->getName()
            ->willReturn('spec\Sylius\Bundle\ResourceBundle\Fixture\Entity\Foo');
        $metadata->setCustomRepositoryClass('spec\Sylius\Bundle\ResourceBundle\Fixture\Entity\FooRepository')
            ->shouldBeCalled();
        $eventArgs->getClassMetadata()->willReturn($metadata);

        $this->loadClassMetadata($eventArgs);

        expect($metadata->isMappedSuperclass)->toBe(false);
    }

    function it_should_unset_association_mappings_of_mapped_superclasses(
        LoadClassMetadataEventArgs $eventArgs,
        ClassMetadata $metadata
    ) {
        $metadata->isMappedSuperclass = true;
        $metadata->name = 'spec\Sylius\Bundle\ResourceBundle\Fixture\Entity\MappedSuperclass';
        $metadata->associationMappings = array(
            'refOne' => array('association' => ClassMetadata::REFERENCE_ONE),
            'refMany' => array('association' => ClassMetadata::REFERENCE_MANY),
            'embedOne' => array('association' => ClassMetadata::EMBED_ONE),
            'embedMany' => array('association' => ClassMetadata::EMBED_MANY),
        );
        $metadata->getName()
            ->willReturn($metadata->name);
        $eventArgs->getClassMetadata()->willReturn($metadata);

        $this->loadClassMetadata($eventArgs);

        expect($metadata->associationMappings)->toBe(array(
        ));
    }

    function it_should_set_association_mappings_of_mapped_superclasses(
        LoadClassMetadataEventArgs $superClassEventArgs,
        ClassMetadata $superClassMetadata,
        LoadClassMetadataEventArgs $eventArgs,
        ClassMetadata $metadata
    ) {
        $superClassMetadata->isMappedSuperclass = true;
        $superClassMetadata->name = 'spec\Sylius\Bundle\ResourceBundle\Fixture\Entity\MappedSuperclass';
        $superClassMetadata->associationMappings = array(
            'refOne' => array('association' => ClassMetadata::REFERENCE_ONE),
            'refMany' => array('association' => ClassMetadata::REFERENCE_MANY),
            'embedOne' => array('association' => ClassMetadata::EMBED_ONE),
            'embedMany' => array('association' => ClassMetadata::EMBED_MANY),
        );
        $superClassMetadata->getName()
            ->willReturn($superClassMetadata->name);
        $superClassEventArgs->getClassMetadata()->willReturn($superClassMetadata);

        $metadata->isMappedSuperclass = false;
        $metadata->name = 'spec\Sylius\Bundle\ResourceBundle\Fixture\Entity\Bar';
        $metadata->associationMappings = array(
            'BarRefOne' => array('association' => ClassMetadata::REFERENCE_ONE),
            'BarRefMany' => array('association' => ClassMetadata::REFERENCE_MANY),
            'BarEmbedOne' => array('association' => ClassMetadata::EMBED_ONE),
            'BarEmbedMany' => array('association' => ClassMetadata::EMBED_MANY),
        );
        $metadata->getName()
            ->willReturn($metadata->name);
        $eventArgs->getClassMetadata()->willReturn($metadata);

        $this->loadClassMetadata($superClassEventArgs);
        $this->loadClassMetadata($eventArgs);

        expect($metadata->associationMappings)->toBe(array(
            'BarRefOne' => array('association' => ClassMetadata::REFERENCE_ONE),
            'BarRefMany' => array('association' => ClassMetadata::REFERENCE_MANY),
            'BarEmbedOne' => array('association' => ClassMetadata::EMBED_ONE),
            'BarEmbedMany' => array('association' => ClassMetadata::EMBED_MANY),
            'refOne' => array('association' => ClassMetadata::REFERENCE_ONE),
            'refMany' => array('association' => ClassMetadata::REFERENCE_MANY),
            'embedOne' => array('association' => ClassMetadata::EMBED_ONE),
            'embedMany' => array('association' => ClassMetadata::EMBED_MANY),
        ));
    }
}
