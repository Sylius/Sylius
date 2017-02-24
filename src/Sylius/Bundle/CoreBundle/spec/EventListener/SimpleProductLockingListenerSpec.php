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
use Sylius\Bundle\CoreBundle\EventListener\SimpleProductLockingListener;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class SimpleProductLockingListenerSpec extends ObjectBehavior
{
    function let(EntityManagerInterface $manager, ProductVariantResolverInterface $variantResolver)
    {
        $this->beConstructedWith($manager, $variantResolver);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SimpleProductLockingListener::class);
    }

    function it_locks_variant_of_a_simple_product_entity(
        EntityManagerInterface $manager,
        ProductVariantResolverInterface $variantResolver,
        GenericEvent $event,
        ProductInterface $product,
        ProductVariantInterface $productVariant
    ) {
        $event->getSubject()->willReturn($product);
        $product->isSimple()->willReturn(true);
        $variantResolver->getVariant($product)->willReturn($productVariant);
        $productVariant->getVersion()->willReturn(7);

        $manager->lock($productVariant, LockMode::OPTIMISTIC, 7);

        $this->lock($event);
    }

    function it_does_not_lock_variant_of_a_configurable_product_entity(
        GenericEvent $event,
        ProductInterface $product
    ) {
        $event->getSubject()->willReturn($product);
        $product->isSimple()->willReturn(false);

        $this->lock($event);
    }

    function it_throws_an_invalid_argument_exception_if_event_subject_is_not_a_product(GenericEvent $event)
    {
        $event->getSubject()->willReturn('badObject');

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('lock', [$event])
        ;
    }
}
