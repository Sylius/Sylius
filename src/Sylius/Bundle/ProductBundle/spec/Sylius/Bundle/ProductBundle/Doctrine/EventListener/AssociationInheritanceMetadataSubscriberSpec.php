<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ProductBundle\Doctrine\EventListener;

use Doctrine\Common\Persistence\Event\LoadClassMetadataEventArgs;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AssociationInheritanceMetadataSubscriberSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(array(
            'product' => 'Sylius\Component\Product\Model\ProductAssociation',
            'group'   => 'Cocoders\GroupProductAssociation\Model\GroupAssociation'
        ));
    }
    function it_is_doctrine_event_subcriber()
    {
        $this->shouldHaveType('Doctrine\Common\EventSubscriber');
    }

    function it_subscribe_loadClassMetadata_event()
    {
        $this->getSubscribedEvents()->shouldBe(array(
            'loadClassMetadata'
        ));
    }

    function it_set_discriminator_map_to_parent_association_mapping(
        LoadClassMetadataEventArgs $eventArgs,
        ClassMetadata $metadata
    )
    {
        $eventArgs->getClassMetadata()->willReturn($metadata);
        $metadata->getName()->willReturn('Sylius\Component\Product\Model\Association');

        $metadata
            ->setDiscriminatorMap(array(
                'product' => 'Sylius\Component\Product\Model\ProductAssociation',
                'group'   => 'Cocoders\GroupProductAssociation\Model\GroupAssociation'
            ))
            ->shouldBeCalled()
        ;

        $this->loadClassMetadata($eventArgs);
    }

    function it_does_not_set_discriminator_map_to_other_objects(
        LoadClassMetadataEventArgs $eventArgs,
        ClassMetadata $metadata
    )
    {
        $eventArgs->getClassMetadata()->willReturn($metadata);
        $metadata->getName()->willReturn('Sylius\Component\Product\Model\Product');

        $metadata->setDiscriminatorMap(Argument::any())->shouldNotBeCalled();

        $this->loadClassMetadata($eventArgs);
    }
}

/**
 * Mock object - setDiscriminatorMap method is in ODM and ORM ClassMetadata but there is not such method in Common one.
 */
abstract class ClassMetadata implements \Doctrine\Common\Persistence\Mapping\ClassMetadata
{
    abstract public function setDiscriminatorMap(array $map);
}
