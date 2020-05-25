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

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class SimpleProductLockingListenerSpec extends ObjectBehavior
{
    function let(EntityManagerInterface $manager): void
    {
        $this->beConstructedWith($manager);
    }

    function it_locks_variant_of_a_simple_product_entity(
        EntityManagerInterface $manager,
        GenericEvent $event,
        ProductInterface $product,
        ProductVariantInterface $productVariant
    ): void {
        $event->getSubject()->willReturn($product);
        $product->isSimple()->willReturn(true);
        $product->getVariants()->willReturn(new ArrayCollection([$productVariant->getWrappedObject()]));
        $productVariant->getVersion()->willReturn(7);

        $manager->lock($productVariant, LockMode::OPTIMISTIC, 7);

        $this->lock($event);
    }

    function it_does_not_lock_variant_of_a_configurable_product_entity(
        GenericEvent $event,
        ProductInterface $product
    ): void {
        $event->getSubject()->willReturn($product);
        $product->isSimple()->willReturn(false);

        $this->lock($event);
    }

    function it_throws_an_invalid_argument_exception_if_event_subject_is_not_a_product(GenericEvent $event): void
    {
        $event->getSubject()->willReturn('badObject');

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('lock', [$event])
        ;
    }
}
