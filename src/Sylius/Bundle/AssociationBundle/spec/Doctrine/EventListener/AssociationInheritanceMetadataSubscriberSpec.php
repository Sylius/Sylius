<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AssociationBundle\Doctrine\EventListener;

use Doctrine\Common\Persistence\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;


/**
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class AssociationInheritanceMetadataSubscriberSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(array(
            'product' => 'Sylius\Component\Core\Model\ProductAssociation',
            'group'   => 'Cocoders\GroupProductAssociation\Model\GroupAssociation'
        ));
    }
    function it_implements_doctrine_event_subscriber()
    {
        $this->shouldHaveType('Doctrine\Common\EventSubscriber');
    }

    function it_subscribed_load_class_metadata_event()
    {
        $this->getSubscribedEvents()->shouldReturn(array(
            'loadClassMetadata',
        ));
    }


    function it_does_not_set_discriminator_map_to_other_objects(
        LoadClassMetadataEventArgs $eventArguments,
        ClassMetadataInfo $metadata
    )
    {
        $eventArguments->getClassMetadata()->willReturn($metadata);
        $metadata->getName()->willReturn('Sylius\Component\Product\Model\Product');

        $metadata->setDiscriminatorMap(Argument::any())->shouldNotBeCalled();

        $this->loadClassMetadata($eventArguments);
    }
}
