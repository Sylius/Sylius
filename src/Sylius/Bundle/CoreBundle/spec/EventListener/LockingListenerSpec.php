<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\EventListener\LockingListener;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Sylius\Component\Resource\Model\VersionedInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class LockingListenerSpec extends ObjectBehavior
{
    function let(EntityManagerInterface $manager, ProductVariantResolverInterface $variantResolver)
    {
        $this->beConstructedWith($manager, $variantResolver);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(LockingListener::class);
    }

    function it_locks_versioned_entity(
        EntityManagerInterface $manager,
        GenericEvent $event,
        VersionedInterface $subject
    ) {
        $event->getSubject()->willReturn($subject);
        $subject->getVersion()->willReturn(7);

        $manager->lock($subject, LockMode::OPTIMISTIC, 7);

        $this->lock($event);
    }

    function it_throws_an_invalid_argument_exception_if_event_subject_is_not_versioned(GenericEvent $event)
    {
        $event->getSubject()->willReturn('badObject');

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('lock', [$event])
        ;
    }
}
