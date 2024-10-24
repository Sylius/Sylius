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
use Sylius\Component\Core\Event\ProductCreated;
use Sylius\Component\Core\Event\ProductUpdated;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class ProductEventListenerSpec extends ObjectBehavior
{
    function let(MessageBusInterface $eventBus): void
    {
        $this->beConstructedWith($eventBus);
    }

    function it_dispatches_product_created_after_creating_product(
        MessageBusInterface $eventBus,
        GenericEvent $event,
        ProductInterface $product,
    ): void {
        $event->getSubject()->willReturn($product);

        $product->getCode()->willReturn('MUG');

        $message = new ProductCreated('MUG');
        $eventBus->dispatch($message)->willReturn(new Envelope($message))->shouldBeCalled();

        $this->dispatchProductCreatedEvent($event);
    }

    function it_dispatches_product_updated_after_updating_product(
        MessageBusInterface $eventBus,
        GenericEvent $event,
        ProductInterface $product,
    ): void {
        $event->getSubject()->willReturn($product);

        $product->getCode()->willReturn('MUG');

        $message = new ProductUpdated('MUG');
        $eventBus->dispatch($message)->willReturn(new Envelope($message))->shouldBeCalled();

        $this->dispatchProductUpdatedEvent($event);
    }

    function it_throws_exception_if_event_object_is_not_a_product(GenericEvent $event): void
    {
        $event->getSubject()->willReturn('badObject')->shouldBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('dispatchProductCreatedEvent', [$event])
        ;

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('dispatchProductUpdatedEvent', [$event])
        ;
    }
}
