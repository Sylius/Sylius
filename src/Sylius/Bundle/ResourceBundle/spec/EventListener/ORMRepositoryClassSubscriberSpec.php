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

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\EventListener\ORMRepositoryClassSubscriber;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Metadata\RegistryInterface;

/**
 * @author Ben Davies <ben.davies@gmail.com>
 */
final class ORMRepositoryClassSubscriberSpec extends ObjectBehavior
{
    function let(RegistryInterface $registry, LoadClassMetadataEventArgs $event, ClassMetadata $classMetadata)
    {
        $classMetadata->getName()->willReturn('Foo');
        $event->getClassMetadata()->willReturn($classMetadata);

        $this->beConstructedWith($registry);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ORMRepositoryClassSubscriber::class);
    }

    function it_implements_event_subscriber_interface()
    {
        $this->shouldImplement(EventSubscriber::class);
    }

    function it_is_subscribed_to_load_class_metadata_doctrine_orm_event()
    {
        $this->getSubscribedEvents()->shouldReturn([Events::loadClassMetadata]);
    }

    function it_sets_custom_repository_class(LoadClassMetadataEventArgs $event, RegistryInterface $registry, ClassMetadata $classMetadata, MetadataInterface $metadata)
    {
        $registry->getByClass('Foo')->willReturn($metadata);
        $metadata->hasClass('repository')->willReturn(true);
        $metadata->getClass('repository')->willReturn('FooRepository');

        $classMetadata->setCustomRepositoryClass('FooRepository')->shouldBeCalled();

        $this->loadClassMetadata($event);
    }

    function it_does_not_set_custom_repository_class_if_not_configured(LoadClassMetadataEventArgs $event, RegistryInterface $registry, ClassMetadata $classMetadata, MetadataInterface $metadata)
    {
        $registry->getByClass('Foo')->willReturn($metadata);
        $metadata->hasClass('repository')->willReturn(false);

        $classMetadata->setCustomRepositoryClass(Argument::any())->shouldNotBeCalled();

        $this->loadClassMetadata($event);
    }

    function it_does_not_set_custom_repository_class_if_registry_does_not_have_class(LoadClassMetadataEventArgs $event, RegistryInterface $registry, ClassMetadata $classMetadata)
    {
        $registry->getByClass('Foo')->willThrow(\InvalidArgumentException::class);

        $classMetadata->setCustomRepositoryClass(Argument::any())->shouldNotBeCalled();

        $this->loadClassMetadata($event);
    }
}
