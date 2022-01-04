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

namespace spec\Sylius\Bundle\ApiBundle\EventSubscriber;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Calculator\DelayStampCalculatorInterface;
use Sylius\Component\Core\Event\ProductVariantUpdated;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Promotion\Event\CatalogPromotionEnded;
use Sylius\Component\Promotion\Event\CatalogPromotionUpdated;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;
use Sylius\Component\Promotion\Provider\DateTimeProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

final class CatalogPromotionEventSubscriberSpec extends ObjectBehavior
{
    function let(
        MessageBusInterface $eventBus,
        DelayStampCalculatorInterface $delayStampCalculator,
        DateTimeProviderInterface $dateTimeProvider
    ): void {
        $this->beConstructedWith($eventBus, $delayStampCalculator, $dateTimeProvider);
    }

    function it_dispatches_catalog_promotion_updated_and_catalog_promotion_ended_after_writing_catalog_promotion(
        MessageBusInterface $eventBus,
        CatalogPromotionInterface $catalogPromotion,
        HttpKernelInterface $kernel,
        Request $request,
        DelayStampCalculatorInterface $delayStampCalculator,
        DateTimeProviderInterface $dateTimeProvider
    ): void {
        $request->getMethod()->willReturn(Request::METHOD_PATCH);

        $startDateTime = new \DateTime('2021-10-09');
        $endDateTime = new \DateTime('2021-10-10');
        $dateTimeProvider->now()->willReturn(new \DateTime());

        $catalogPromotion->getCode()->willReturn('Winter_sale');

        $messageUpdate = new CatalogPromotionUpdated('Winter_sale');
        $messageEnd = new CatalogPromotionEnded('Winter_sale');

        $catalogPromotion->getStartDate()->willReturn($startDateTime);
        $catalogPromotion->getEndDate()->willReturn($endDateTime);

        $startDelayStamp = new DelayStamp(200000);
        $endDelayStamp = new DelayStamp(300000);

        $delayStampCalculator->calculate(Argument::any(), $startDateTime)->willReturn($startDelayStamp);
        $delayStampCalculator->calculate(Argument::any(), $endDateTime)->willReturn($endDelayStamp);

        $eventBus->dispatch($messageUpdate, [$startDelayStamp])->willReturn(new Envelope($messageUpdate))->shouldBeCalled();
        $eventBus->dispatch($messageEnd, [$endDelayStamp])->willReturn(new Envelope($messageEnd))->shouldBeCalled();

        $this->postWrite(new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            $catalogPromotion->getWrappedObject()
        ));
    }

    function it_dispatches_catalog_promotion_started_without_delay_if_start_date_is_not_provided(
        MessageBusInterface $eventBus,
        CatalogPromotionInterface $catalogPromotion,
        HttpKernelInterface $kernel,
        Request $request,
        DelayStampCalculatorInterface $delayStampCalculator,
        DateTimeProviderInterface $dateTimeProvider
    ): void {
        $request->getMethod()->willReturn(Request::METHOD_PATCH);

        $endDateTime = new \DateTime('2021-10-10');
        $dateTimeProvider->now()->willReturn(new \DateTime());

        $catalogPromotion->getCode()->willReturn('Winter_sale');

        $messageUpdate = new CatalogPromotionUpdated('Winter_sale');
        $messageEnd = new CatalogPromotionEnded('Winter_sale');

        $catalogPromotion->getStartDate()->willReturn(null);
        $catalogPromotion->getEndDate()->willReturn($endDateTime);

        $endDelayStamp = new DelayStamp(300000);

        $delayStampCalculator->calculate(Argument::any(), null)->shouldNotBeCalled();
        $delayStampCalculator->calculate(Argument::any(), $endDateTime)->willReturn($endDelayStamp);

        $eventBus->dispatch($messageUpdate)->willReturn(new Envelope($messageUpdate))->shouldBeCalled();
        $eventBus->dispatch($messageEnd, [$endDelayStamp])->willReturn(new Envelope($messageEnd))->shouldBeCalled();

        $this->postWrite(new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            $catalogPromotion->getWrappedObject()
        ));
    }

    function it_does_not_dispatch_catalog_promotion_ended_when_end_date_is_not_provided(
        MessageBusInterface $eventBus,
        CatalogPromotionInterface $catalogPromotion,
        HttpKernelInterface $kernel,
        Request $request,
        DelayStampCalculatorInterface $delayStampCalculator,
        DateTimeProviderInterface $dateTimeProvider
    ): void {
        $request->getMethod()->willReturn(Request::METHOD_PATCH);

        $startDateTime = new \DateTime('2021-10-10');
        $endDateTime = null;
        $dateTimeProvider->now()->willReturn(new \DateTime());

        $catalogPromotion->getCode()->willReturn('Winter_sale');

        $messageUpdate = new CatalogPromotionUpdated('Winter_sale');
        $messageEnd = new CatalogPromotionEnded('Winter_sale');

        $catalogPromotion->getStartDate()->willReturn($startDateTime);
        $catalogPromotion->getEndDate()->willReturn($endDateTime);

        $startDelayStamp = new DelayStamp(200000);

        $delayStampCalculator->calculate(Argument::any(), $startDateTime)->willReturn($startDelayStamp);
        $delayStampCalculator->calculate(Argument::any(), $endDateTime)->shouldNotBeCalled();

        $eventBus->dispatch($messageUpdate, [$startDelayStamp])->willReturn(new Envelope($messageUpdate))->shouldBeCalled();
        $eventBus->dispatch($messageEnd, Argument::any())->willReturn(new Envelope($messageEnd))->shouldNotBeCalled();

        $this->postWrite(new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            $catalogPromotion->getWrappedObject()
        ));
    }

    function it_does_nothing_after_writing_other_entity(
        MessageBusInterface $eventBus,
        HttpKernelInterface $kernel,
        Request $request
    ): void {
        $eventBus->dispatch(Argument::any())->shouldNotBeCalled();

        $this->postWrite(new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            new \stdClass()
        ));
    }

    function it_does_nothing_if_there_is_a_wrong_request_method(
        MessageBusInterface $eventBus,
        CatalogPromotionInterface $catalogPromotion,
        HttpKernelInterface $kernel,
        Request $request
    ): void {
        $request->getMethod()->willReturn(Request::METHOD_GET);

        $eventBus->dispatch(Argument::any())->shouldNotBeCalled();

        $this->postWrite(new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            $catalogPromotion->getWrappedObject()
        ));
    }
}
