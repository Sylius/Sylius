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

namespace spec\Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Calculator\DelayStampCalculatorInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionAnnouncerInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\UpdateCatalogPromotionState;
use Sylius\Calendar\Provider\DateTimeProviderInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Event\CatalogPromotionCreated;
use Sylius\Component\Promotion\Event\CatalogPromotionEnded;
use Sylius\Component\Promotion\Event\CatalogPromotionUpdated;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

final class CatalogPromotionAnnouncerSpec extends ObjectBehavior
{
    function let(
        MessageBusInterface $eventBus,
        MessageBusInterface $commandBus,
        DelayStampCalculatorInterface $delayStampCalculator,
        DateTimeProviderInterface $dateTimeProvider,
    ): void {
        $this->beConstructedWith($eventBus, $commandBus, $delayStampCalculator, $dateTimeProvider);
    }

    function it_implements_catalog_promotion_announcer_interface(): void
    {
        $this->shouldImplement(CatalogPromotionAnnouncerInterface::class);
    }

    function it_dispatches_catalog_promotion_created_and_catalog_promotion_ended_events(
        MessageBusInterface $eventBus,
        MessageBusInterface $commandBus,
        DelayStampCalculatorInterface $delayStampCalculator,
        DateTimeProviderInterface $dateTimeProvider,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
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

        $messageUpdateState = new UpdateCatalogPromotionState('SALE');
        $messageCreate = new CatalogPromotionCreated('SALE');
        $messageEnd = new CatalogPromotionEnded('SALE');

        $commandBus->dispatch($messageUpdateState, [$startDelayStamp])->willReturn(new Envelope($messageUpdateState))->shouldBeCalled();
        $eventBus->dispatch($messageCreate, [$startDelayStamp])->willReturn(new Envelope($messageCreate))->shouldBeCalled();
        $commandBus->dispatch($messageUpdateState, [$endDelayStamp])->willReturn(new Envelope($messageUpdateState))->shouldBeCalled();
        $eventBus->dispatch($messageEnd, [$endDelayStamp])->willReturn(new Envelope($messageEnd))->shouldBeCalled();

        $this->dispatchCatalogPromotionCreatedEvent($catalogPromotion);
    }

    function it_does_not_dispatch_catalog_promotion_ended_when_catalog_promotion_has_no_end_date_configured(
        MessageBusInterface $eventBus,
        MessageBusInterface $commandBus,
        DelayStampCalculatorInterface $delayStampCalculator,
        DateTimeProviderInterface $dateTimeProvider,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $startDateTime = new \DateTime('2021-10-10');

        $dateTimeProvider->now()->willReturn(new \DateTime());

        $catalogPromotion->getCode()->willReturn('SALE');
        $catalogPromotion->getStartDate()->willReturn($startDateTime);
        $catalogPromotion->getEndDate()->willReturn(null);

        $startDelayStamp = new DelayStamp(200000);

        $delayStampCalculator->calculate(Argument::any(), $startDateTime)->willReturn($startDelayStamp);

        $messageUpdateState = new UpdateCatalogPromotionState('SALE');
        $messageUpdate = new CatalogPromotionCreated('SALE');
        $messageEnd = new CatalogPromotionEnded('SALE');

        $commandBus->dispatch($messageUpdateState, [$startDelayStamp])->willReturn(new Envelope($messageUpdateState))->shouldBeCalled();
        $eventBus->dispatch($messageUpdate, [$startDelayStamp])->willReturn(new Envelope($messageUpdate))->shouldBeCalled();
        $eventBus->dispatch($messageEnd, [Argument::any()])->shouldNotBeCalled();

        $this->dispatchCatalogPromotionCreatedEvent($catalogPromotion);
    }

    function it_dispatches_catalog_promotion_updated_and_catalog_promotion_ended_events(
        MessageBusInterface $eventBus,
        MessageBusInterface $commandBus,
        DelayStampCalculatorInterface $delayStampCalculator,
        DateTimeProviderInterface $dateTimeProvider,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
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

        $messageUpdateState = new UpdateCatalogPromotionState('SALE');
        $messageUpdate = new CatalogPromotionUpdated('SALE');
        $messageEnd = new CatalogPromotionEnded('SALE');

        $commandBus->dispatch($messageUpdateState, [$startDelayStamp])->willReturn(new Envelope($messageUpdateState))->shouldBeCalled();
        $eventBus->dispatch($messageUpdate, [$startDelayStamp])->willReturn(new Envelope($messageUpdate))->shouldBeCalled();
        $commandBus->dispatch($messageUpdateState, [$endDelayStamp])->willReturn(new Envelope($messageUpdateState))->shouldBeCalled();
        $eventBus->dispatch($messageEnd, [$endDelayStamp])->willReturn(new Envelope($messageEnd))->shouldBeCalled();

        $this->dispatchCatalogPromotionUpdatedEvent($catalogPromotion);
    }

    function it_dispatches_catalog_promotion_updated_twice_if_catalog_promotion_is_updated_with_delayed_start(
        MessageBusInterface $eventBus,
        MessageBusInterface $commandBus,
        DelayStampCalculatorInterface $delayStampCalculator,
        DateTimeProviderInterface $dateTimeProvider,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $startDateTime = new \DateTime('2021-10-10');
        $endDateTime = new \DateTime('2021-10-11');

        $dateTimeProvider->now()->willReturn(new \DateTime('2021-10-09'));

        $catalogPromotion->getCode()->willReturn('SALE');
        $catalogPromotion->getStartDate()->willReturn($startDateTime);
        $catalogPromotion->getEndDate()->willReturn($endDateTime);

        $startDelayStamp = new DelayStamp(200000);
        $endDelayStamp = new DelayStamp(300000);

        $delayStampCalculator->calculate(Argument::any(), $startDateTime)->willReturn($startDelayStamp);
        $delayStampCalculator->calculate(Argument::any(), $endDateTime)->willReturn($endDelayStamp);

        $messageUpdateState = new UpdateCatalogPromotionState('SALE');
        $messageUpdate = new CatalogPromotionUpdated('SALE');
        $messageEnd = new CatalogPromotionEnded('SALE');

        $commandBus->dispatch($messageUpdateState)->willReturn(new Envelope($messageUpdateState))->shouldBeCalled();
        $eventBus->dispatch($messageUpdate)->willReturn(new Envelope($messageUpdate))->shouldBeCalled();
        $commandBus->dispatch($messageUpdateState, [$startDelayStamp])->willReturn(new Envelope($messageUpdateState))->shouldBeCalled();
        $eventBus->dispatch($messageUpdate, [$startDelayStamp])->willReturn(new Envelope($messageUpdate))->shouldBeCalled();
        $commandBus->dispatch($messageUpdateState, [$endDelayStamp])->willReturn(new Envelope($messageEnd))->shouldBeCalled();
        $eventBus->dispatch($messageEnd, [$endDelayStamp])->willReturn(new Envelope($messageEnd))->shouldBeCalled();

        $this->dispatchCatalogPromotionUpdatedEvent($catalogPromotion);
    }
}
