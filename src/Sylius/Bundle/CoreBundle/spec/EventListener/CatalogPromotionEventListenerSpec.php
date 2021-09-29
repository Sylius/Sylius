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

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Event\CatalogPromotionUpdated;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class CatalogPromotionEventListenerSpec extends ObjectBehavior
{
    function let(MessageBusInterface $eventBus): void
    {
        $this->beConstructedWith($eventBus);
    }

    function it_dispatches_catalog_promotion_updated_after_updating_catalog_promotion(
        MessageBusInterface $eventBus,
        GenericEvent $event,
        CatalogPromotionInterface $catalogPromotion
    ): void {
        $event->getSubject()->willReturn($catalogPromotion);

        $catalogPromotion->getCode()->willReturn('SALE');

        $message = new CatalogPromotionUpdated('SALE');
        $eventBus->dispatch($message)->willReturn(new Envelope($message))->shouldBeCalled();

        $this->update($event);
    }

    function it_throws_an_exception_if_event_object_is_not_a_catalog_promotion(GenericEvent $event): void
    {
        $event->getSubject()->willReturn('badObject')->shouldBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('update', [$event])
        ;
    }
}
