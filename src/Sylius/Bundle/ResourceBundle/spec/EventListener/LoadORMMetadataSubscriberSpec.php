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

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

require_once __DIR__.'/../Fixture/Entity/Foo.php';
require_once __DIR__ . '/../Fixture/Entity/MappedSuperclass.php';
require_once __DIR__ . '/../Fixture/Entity/Bar.php';

/**
 * @author Benoît Burnichon <bburnichon@gmail.com>
 */
class LoadORMMetadataSubscriberSpec extends ObjectBehavior
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
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\EventListener\LoadORMMetadataSubscriber');
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
            'oneToOne' => array('type' => ClassMetadata::ONE_TO_ONE),
            'oneToMany' => array('type' => ClassMetadata::ONE_TO_MANY),
            'manyToOne' => array('type' => ClassMetadata::MANY_TO_ONE),
            'manyToMany' => array('type' => ClassMetadata::MANY_TO_MANY),
        );
        $metadata->getName()
            ->willReturn($metadata->name);
        $metadata->getAssociationMappings()
            ->willReturn($metadata->associationMappings);
        $eventArgs->getClassMetadata()->willReturn($metadata);

        $this->loadClassMetadata($eventArgs);

        expect($metadata->associationMappings)->toBe(array(
            'manyToOne' => array('type' => ClassMetadata::MANY_TO_ONE),
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
            'oneToOne' => array('type' => ClassMetadata::ONE_TO_ONE),
            'oneToMany' => array('type' => ClassMetadata::ONE_TO_MANY),
            'manyToOne' => array('type' => ClassMetadata::MANY_TO_ONE),
            'manyToMany' => array('type' => ClassMetadata::MANY_TO_MANY),
        );
        $superClassMetadata->getName()
            ->willReturn($superClassMetadata->name);
        $superClassMetadata->getAssociationMappings()
            ->willReturn($superClassMetadata->associationMappings);
        $superClassEventArgs->getClassMetadata()->willReturn($superClassMetadata);

        $metadata->isMappedSuperclass = false;
        $metadata->name = 'spec\Sylius\Bundle\ResourceBundle\Fixture\Entity\Bar';
        $metadata->associationMappings = array(
            'BarOneToOne' => array('type' => ClassMetadata::ONE_TO_ONE),
            'BarOneToMany' => array('type' => ClassMetadata::ONE_TO_MANY),
            'BarManyToOne' => array('type' => ClassMetadata::MANY_TO_ONE),
            'BarManyToMany' => array('type' => ClassMetadata::MANY_TO_MANY),
        );
        $metadata->getName()
            ->willReturn($metadata->name);
        $metadata->getAssociationMappings()
            ->willReturn($metadata->associationMappings);
        $eventArgs->getClassMetadata()->willReturn($metadata);

        $this->loadClassMetadata($superClassEventArgs);
        $this->loadClassMetadata($eventArgs);

        expect($metadata->associationMappings)->toBe(array(
            'BarOneToOne' => array('type' => ClassMetadata::ONE_TO_ONE),
            'BarOneToMany' => array('type' => ClassMetadata::ONE_TO_MANY),
            'BarManyToOne' => array('type' => ClassMetadata::MANY_TO_ONE),
            'BarManyToMany' => array('type' => ClassMetadata::MANY_TO_MANY),
            'oneToOne' => array('type' => ClassMetadata::ONE_TO_ONE),
            'oneToMany' => array('type' => ClassMetadata::ONE_TO_MANY),
            'manyToMany' => array('type' => ClassMetadata::MANY_TO_MANY),
        ));
    }
}
