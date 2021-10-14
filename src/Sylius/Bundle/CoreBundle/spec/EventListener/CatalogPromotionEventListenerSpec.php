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
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Calculator\DelayStampCalculatorInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Event\CatalogPromotionEnded;
use Sylius\Component\Promotion\Event\CatalogPromotionUpdated;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

final class CatalogPromotionEventListenerSpec extends ObjectBehavior
{
    function let(MessageBusInterface $eventBus, DelayStampCalculatorInterface $delayStampCalculator): void
    {
        $this->beConstructedWith($eventBus, $delayStampCalculator);
    }

    function it_dispatches_catalog_promotion_updated_and_catalog_promotion_ended_after_updating_catalog_promotion(
        MessageBusInterface $eventBus,
        GenericEvent $event,
        CatalogPromotionInterface $catalogPromotion,
        DelayStampCalculatorInterface $delayStampCalculator
    ): void {
        $event->getSubject()->willReturn($catalogPromotion);
        $startDateTime = new \DateTime('@1634083200');
        $endDateTime = new \DateTime('@1634085200');

        $catalogPromotion->getCode()->willReturn('SALE');
        $catalogPromotion->getStartDate()->willReturn($startDateTime);
        $catalogPromotion->getEndDate()->willReturn($endDateTime);

        $startDelayStamp = new DelayStamp(200000);
        $endDelayStamp = new DelayStamp(300000);

        $delayStampCalculator->calculate(Argument::any(), $startDateTime)->willReturn($startDelayStamp);
        $delayStampCalculator->calculate(Argument::any(), $endDateTime)->willReturn($endDelayStamp);

        $messageUpdate = new CatalogPromotionUpdated('SALE');
        $messageEnd = new CatalogPromotionEnded('SALE');

        $eventBus->dispatch($messageUpdate, [$startDelayStamp])->willReturn(new Envelope($messageUpdate))->shouldBeCalled();
        $eventBus->dispatch($messageEnd, [$endDelayStamp])->willReturn(new Envelope($messageEnd))->shouldBeCalled();

        $this->dispatchCatalogPromotionUpdatedEvent($event);
    }

    function it_throws_an_exception_if_event_object_is_not_a_catalog_promotion(GenericEvent $event): void
    {
        $event->getSubject()->willReturn('badObject')->shouldBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('dispatchCatalogPromotionUpdatedEvent', [$event])
        ;
    }
}
