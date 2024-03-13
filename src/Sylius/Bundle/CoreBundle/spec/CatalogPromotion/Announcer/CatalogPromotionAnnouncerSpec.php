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

namespace spec\Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Calculator\DelayStampCalculatorInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionAnnouncerInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Event\CatalogPromotionCreated;
use Sylius\Component\Promotion\Event\CatalogPromotionEnded;
use Sylius\Component\Promotion\Event\CatalogPromotionUpdated;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

final class CatalogPromotionAnnouncerSpec extends ObjectBehavior
{
    function let(
        MessageBusInterface $eventBus,
        DelayStampCalculatorInterface $delayStampCalculator,
        ClockInterface $clock,
    ): void {
        $this->beConstructedWith($eventBus, $delayStampCalculator, $clock);
    }

    function it_implements_catalog_promotion_announcer_interface(): void
    {
        $this->shouldImplement(CatalogPromotionAnnouncerInterface::class);
    }

    function it_dispatches_catalog_promotion_created_and_catalog_promotion_ended_events(
        MessageBusInterface $eventBus,
        DelayStampCalculatorInterface $delayStampCalculator,
        ClockInterface $clock,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $startDateTime = new \DateTime('2021-10-10');
        $endDateTime = new \DateTime('2021-10-11');

        $clock->now()->willReturn(new \DateTimeImmutable());

        $catalogPromotion->getCode()->willReturn('SALE');
        $catalogPromotion->getStartDate()->willReturn($startDateTime);
        $catalogPromotion->getEndDate()->willReturn($endDateTime);

        $startDelayStamp = new DelayStamp(200000);
        $endDelayStamp = new DelayStamp(300000);

        $delayStampCalculator->calculate(Argument::any(), $startDateTime)->willReturn($startDelayStamp);
        $delayStampCalculator->calculate(Argument::any(), $endDateTime)->willReturn($endDelayStamp);

        $messageCreate = new CatalogPromotionCreated('SALE');
        $messageEnd = new CatalogPromotionEnded('SALE');

        $eventBus->dispatch($messageCreate, [$startDelayStamp])->willReturn(new Envelope($messageCreate))->shouldBeCalled();
        $eventBus->dispatch($messageEnd, [$endDelayStamp])->willReturn(new Envelope($messageEnd))->shouldBeCalled();

        $this->dispatchCatalogPromotionCreatedEvent($catalogPromotion);
    }

    function it_does_not_dispatch_catalog_promotion_ended_when_catalog_promotion_has_no_end_date_configured(
        MessageBusInterface $eventBus,
        DelayStampCalculatorInterface $delayStampCalculator,
        ClockInterface $clock,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $startDateTime = new \DateTime('2021-10-10');

        $clock->now()->willReturn(new \DateTimeImmutable());

        $catalogPromotion->getCode()->willReturn('SALE');
        $catalogPromotion->getStartDate()->willReturn($startDateTime);
        $catalogPromotion->getEndDate()->willReturn(null);

        $startDelayStamp = new DelayStamp(200000);

        $delayStampCalculator->calculate(Argument::any(), $startDateTime)->willReturn($startDelayStamp);

        $messageUpdate = new CatalogPromotionCreated('SALE');
        $messageEnd = new CatalogPromotionEnded('SALE');

        $eventBus->dispatch($messageUpdate, [$startDelayStamp])->willReturn(new Envelope($messageUpdate))->shouldBeCalled();
        $eventBus->dispatch($messageEnd, [Argument::any()])->shouldNotBeCalled();

        $this->dispatchCatalogPromotionCreatedEvent($catalogPromotion);
    }

    function it_dispatches_catalog_promotion_updated_and_catalog_promotion_ended_events(
        MessageBusInterface $eventBus,
        DelayStampCalculatorInterface $delayStampCalculator,
        ClockInterface $clock,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $startDateTime = new \DateTime('2021-10-10');
        $endDateTime = new \DateTime('2021-10-11');

        $clock->now()->willReturn(new \DateTimeImmutable());

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

        $this->dispatchCatalogPromotionUpdatedEvent($catalogPromotion);
    }

    function it_dispatches_catalog_promotion_updated_twice_if_catalog_promotion_is_updated_with_delayed_start(
        MessageBusInterface $eventBus,
        DelayStampCalculatorInterface $delayStampCalculator,
        ClockInterface $clock,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $startDateTime = new \DateTime('2021-10-10');
        $endDateTime = new \DateTime('2021-10-11');

        $clock->now()->willReturn(new \DateTimeImmutable('2021-10-09'));

        $catalogPromotion->getCode()->willReturn('SALE');
        $catalogPromotion->getStartDate()->willReturn($startDateTime);
        $catalogPromotion->getEndDate()->willReturn($endDateTime);

        $startDelayStamp = new DelayStamp(200000);
        $endDelayStamp = new DelayStamp(300000);

        $delayStampCalculator->calculate(Argument::any(), $startDateTime)->willReturn($startDelayStamp);
        $delayStampCalculator->calculate(Argument::any(), $endDateTime)->willReturn($endDelayStamp);

        $messageUpdate = new CatalogPromotionUpdated('SALE');
        $messageEnd = new CatalogPromotionEnded('SALE');

        $eventBus->dispatch($messageUpdate, [])->willReturn(new Envelope($messageUpdate))->shouldBeCalled();
        $eventBus->dispatch($messageUpdate, [$startDelayStamp])->willReturn(new Envelope($messageUpdate))->shouldBeCalled();
        $eventBus->dispatch($messageEnd, [$endDelayStamp])->willReturn(new Envelope($messageEnd))->shouldBeCalled();

        $this->dispatchCatalogPromotionUpdatedEvent($catalogPromotion);
    }
}
