<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\CatalogPromotion\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Event\ProductVariantCreated;
use Sylius\Component\Core\Event\ProductVariantUpdated;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class ProductVariantEventListenerSpec extends ObjectBehavior
{
    function let(MessageBusInterface $eventBus): void
    {
        $this->beConstructedWith($eventBus);
    }

    function it_dispatches_product_variant_created_after_creating_product_variant(
        MessageBusInterface $eventBus,
        GenericEvent $event,
        ProductVariantInterface $variant,
    ): void {
        $event->getSubject()->willReturn($variant);

        $variant->getCode()->willReturn('MUG');

        $message = new ProductVariantCreated('MUG');
        $eventBus->dispatch($message)->willReturn(new Envelope($message))->shouldBeCalled();

        $this->dispatchProductVariantCreatedEvent($event);
    }

    function it_dispatches_product_variant_updated_after_updating_product_variant(
        MessageBusInterface $eventBus,
        GenericEvent $event,
        ProductVariantInterface $variant,
    ): void {
        $event->getSubject()->willReturn($variant);

        $variant->getCode()->willReturn('MUG');

        $message = new ProductVariantUpdated('MUG');
        $eventBus->dispatch($message)->willReturn(new Envelope($message))->shouldBeCalled();

        $this->dispatchProductVariantUpdatedEvent($event);
    }

    function it_throws_exception_if_event_object_is_not_a_product_variant(GenericEvent $event): void
    {
        $event->getSubject()->willReturn('badObject')->shouldBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('dispatchProductVariantCreatedEvent', [$event])
        ;

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('dispatchProductVariantUpdatedEvent', [$event])
        ;
    }
}
