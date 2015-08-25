<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ReviewBundle\EventListener;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use PhpSpec\ObjectBehavior;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class LoadMetadataSubscriberSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(array('subject'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ReviewBundle\EventListener\LoadMetadataSubscriber');
    }
    
    function it_implements_event_subscriber()
    {
        $this->shouldImplement('Doctrine\Common\EventSubscriber');
    }

    function it_has_subscribed_events()
    {
        $this->getSubscribedEvents()->shouldReturn(array('loadClassMetadata'));
    }

    function it_loads_class_metadata(
        LoadClassMetadataEventArgs $eventArgs,
        ClassMetadata $metadata,
        EntityManager $entityManager,
        ClassMetadataFactory $mappetadataFactory
    ) {
        $eventArgs->getClassMetadata()->willReturn($metadata)->shouldBeCalled();
        $eventArgs->getEntityManager()->willReturn($entityManager)->shouldBeCalled();
        $entityManager->getMetadataFactory()->willReturn($metadataFactory)->shouldBeCalled();


    }
}
