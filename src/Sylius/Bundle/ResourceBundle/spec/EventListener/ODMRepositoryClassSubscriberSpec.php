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

namespace spec\Sylius\Bundle\ResourceBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Event\LoadClassMetadataEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Metadata\RegistryInterface;

/**
 * @require Doctrine\ODM\MongoDB\Events
 */
final class ODMRepositoryClassSubscriberSpec extends ObjectBehavior
{
    public function let(RegistryInterface $registry, LoadClassMetadataEventArgs $event, ClassMetadata $classMetadata): void
    {
        $classMetadata->getName()->willReturn('Foo');
        $event->getClassMetadata()->willReturn($classMetadata);

        $this->beConstructedWith($registry);
    }

    public function it_implements_event_subscriber_interface(): void
    {
        $this->shouldImplement(EventSubscriber::class);
    }

    public function it_is_subscribed_to_load_class_metadata_doctrine_orm_event(): void
    {
        $this->getSubscribedEvents()->shouldReturn([Events::loadClassMetadata]);
    }

    public function it_sets_custom_repository_class(LoadClassMetadataEventArgs $event, RegistryInterface $registry, ClassMetadata $classMetadata, MetadataInterface $metadata): void
    {
        $registry->getByClass('Foo')->willReturn($metadata);
        $metadata->hasClass('repository')->willReturn(true);
        $metadata->getClass('repository')->willReturn('FooRepository');

        $classMetadata->setCustomRepositoryClass('FooRepository')->shouldBeCalled();

        $this->loadClassMetadata($event);
    }

    public function it_does_not_set_custom_repository_class_if_not_configured(LoadClassMetadataEventArgs $event, RegistryInterface $registry, ClassMetadata $classMetadata, MetadataInterface $metadata): void
    {
        $registry->getByClass('Foo')->willReturn($metadata);
        $metadata->hasClass('repository')->willReturn(false);

        $classMetadata->setCustomRepositoryClass(Argument::any())->shouldNotBeCalled();

        $this->loadClassMetadata($event);
    }

    public function it_does_not_set_custom_repository_class_if_registry_does_not_have_class(LoadClassMetadataEventArgs $event, RegistryInterface $registry, ClassMetadata $classMetadata): void
    {
        $registry->getByClass('Foo')->willThrow(\InvalidArgumentException::class);

        $classMetadata->setCustomRepositoryClass(Argument::any())->shouldNotBeCalled();

        $this->loadClassMetadata($event);
    }
}
