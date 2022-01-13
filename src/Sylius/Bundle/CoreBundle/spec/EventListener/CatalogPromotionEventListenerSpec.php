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
use Sylius\Component\Promotion\Event\CatalogPromotionCreated;
use Sylius\Component\Promotion\Event\CatalogPromotionEnded;
use Sylius\Component\Promotion\Event\CatalogPromotionUpdated;
use Sylius\Component\Promotion\Provider\DateTimeProviderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

final class CatalogPromotionEventListenerSpec extends ObjectBehavior
{
    function let(
        MessageBusInterface $eventBus,
        DelayStampCalculatorInterface $delayStampCalculator,
        DateTimeProviderInterface $dateTimeProvider
    ): void {
        $this->beConstructedWith($eventBus, $delayStampCalculator, $dateTimeProvider);
    }

    function it_dispatches_catalog_promotion_created_and_catalog_promotion_ended_after_creating_catalog_promotion(
        MessageBusInterface $eventBus,
        GenericEvent $event,
        CatalogPromotionInterface $catalogPromotion,
        DelayStampCalculatorInterface $delayStampCalculator,
        DateTimeProviderInterface $dateTimeProvider
    ): void {
        $event->getSubject()->willReturn($catalogPromotion);
        $startDateTime = new \DateTime('2021-10-10');
        $endDateTime = new \DateTime('2021-10-11');

        $dateTimeProvider->now()->willReturn(new \DateTime());

        $catalogPromotion->getCode()->willReturn('SALE');
        $catalogPromotion->getStartDate()->willReturn($startDateTime);
        $catalogPromotion->getEndDate()->willReturn($endDateTime);

        $startDelayStamp = new DelayStamp(200000);
        $endDelayStamp = new DelayStamp(300000);

        $delayStampCalculator->calculate(Argument::any(), $startDateTime)->willReturn($startDelayStamp);
        $delayStampCalculator->calculate(Argument::any(), $endDateTime)->willReturn($endDelayStamp);

        $messageUpdate = new CatalogPromotionCreated('SALE');
        $messageEnd = new CatalogPromotionEnded('SALE');

        $eventBus->dispatch($messageUpdate, [$startDelayStamp])->willReturn(new Envelope($messageUpdate))->shouldBeCalled();
        $eventBus->dispatch($messageEnd, [$endDelayStamp])->willReturn(new Envelope($messageEnd))->shouldBeCalled();

        $this->dispatchCatalogPromotionCreatedEvent($event);
    }

    function it_does_not_dispatch_catalog_promotion_ended_after_creating_catalog_promotion_when_no_end_date_is_provided(
        MessageBusInterface $eventBus,
        GenericEvent $event,
        CatalogPromotionInterface $catalogPromotion,
        DelayStampCalculatorInterface $delayStampCalculator,
        DateTimeProviderInterface $dateTimeProvider
    ): void {
        $event->getSubject()->willReturn($catalogPromotion);
        $startDateTime = new \DateTime('2021-10-10');
        $endDateTime = new \DateTime('2021-10-11');

        $dateTimeProvider->now()->willReturn(new \DateTime());

        $catalogPromotion->getCode()->willReturn('SALE');
        $catalogPromotion->getStartDate()->willReturn($startDateTime);
        $catalogPromotion->getEndDate()->willReturn(null);

        $startDelayStamp = new DelayStamp(200000);
        $endDelayStamp = new DelayStamp(300000);

        $delayStampCalculator->calculate(Argument::any(), $startDateTime)->willReturn($startDelayStamp);
        $delayStampCalculator->calculate(Argument::any(), $endDateTime)->willReturn($endDelayStamp);

        $messageUpdate = new CatalogPromotionCreated('SALE');
        $messageEnd = new CatalogPromotionEnded('SALE');

        $eventBus->dispatch($messageUpdate, [$startDelayStamp])->willReturn(new Envelope($messageUpdate))->shouldBeCalled();
        $eventBus->dispatch($messageEnd, [Argument::any()])->shouldNotBeCalled();

        $this->dispatchCatalogPromotionCreatedEvent($event);
    }

    function it_dispatches_catalog_promotion_updated_and_catalog_promotion_ended_after_updating_catalog_promotion(
        MessageBusInterface $eventBus,
        GenericEvent $event,
        CatalogPromotionInterface $catalogPromotion,
        DelayStampCalculatorInterface $delayStampCalculator,
        DateTimeProviderInterface $dateTimeProvider
    ): void {
        $event->getSubject()->willReturn($catalogPromotion);
        $startDateTime = new \DateTime('2021-10-10');
        $endDateTime = new \DateTime('2021-10-11');

        $dateTimeProvider->now()->willReturn(new \DateTime());

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

    function it_does_not_dispatch_catalog_promotion_ended_after_updating_catalog_promotion_when_no_end_date_is_provided(
        MessageBusInterface $eventBus,
        GenericEvent $event,
        CatalogPromotionInterface $catalogPromotion,
        DelayStampCalculatorInterface $delayStampCalculator,
        DateTimeProviderInterface $dateTimeProvider
    ): void {
        $event->getSubject()->willReturn($catalogPromotion);
        $startDateTime = new \DateTime('2021-10-10');
        $endDateTime = new \DateTime('2021-10-11');

        $dateTimeProvider->now()->willReturn(new \DateTime());

        $catalogPromotion->getCode()->willReturn('SALE');
        $catalogPromotion->getStartDate()->willReturn($startDateTime);
        $catalogPromotion->getEndDate()->willReturn(null);

        $startDelayStamp = new DelayStamp(200000);
        $endDelayStamp = new DelayStamp(300000);

        $delayStampCalculator->calculate(Argument::any(), $startDateTime)->willReturn($startDelayStamp);
        $delayStampCalculator->calculate(Argument::any(), $endDateTime)->willReturn($endDelayStamp);

        $messageUpdate = new CatalogPromotionUpdated('SALE');
        $messageEnd = new CatalogPromotionEnded('SALE');

        $eventBus->dispatch($messageUpdate, [$startDelayStamp])->willReturn(new Envelope($messageUpdate))->shouldBeCalled();
        $eventBus->dispatch($messageEnd, [$endDelayStamp])->willReturn(new Envelope($messageEnd))->shouldNotBeCalled();

        $this->dispatchCatalogPromotionUpdatedEvent($event);
    }

    function it_dispatches_catalog_promotion_started_without_delay_if_start_date_is_not_provided(
        MessageBusInterface $eventBus,
        GenericEvent $event,
        CatalogPromotionInterface $catalogPromotion,
        DelayStampCalculatorInterface $delayStampCalculator,
        DateTimeProviderInterface $dateTimeProvider
    ): void {
        $event->getSubject()->willReturn($catalogPromotion);
        $endDateTime = new \DateTime('2021-10-11');

        $dateTimeProvider->now()->willReturn(new \DateTime());

        $catalogPromotion->getCode()->willReturn('SALE');
        $catalogPromotion->getStartDate()->willReturn(null);
        $catalogPromotion->getEndDate()->willReturn($endDateTime);

        $endDelayStamp = new DelayStamp(300000);

        $delayStampCalculator->calculate(Argument::any(), null)->shouldNotBeCalled();
        $delayStampCalculator->calculate(Argument::any(), $endDateTime)->willReturn($endDelayStamp);

        $messageUpdate = new CatalogPromotionUpdated('SALE');
        $messageEnd = new CatalogPromotionEnded('SALE');

        $eventBus->dispatch($messageUpdate, [])->willReturn(new Envelope($messageUpdate))->shouldBeCalled();
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
