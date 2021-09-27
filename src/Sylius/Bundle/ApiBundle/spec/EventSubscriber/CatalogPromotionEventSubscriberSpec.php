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
use Sylius\Component\Core\Event\ProductVariantUpdated;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Promotion\Event\CatalogPromotionUpdated;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class CatalogPromotionEventSubscriberSpec extends ObjectBehavior
{
    function let(MessageBusInterface $eventBus): void
    {
        $this->beConstructedWith($eventBus);
    }

    function it_dispatches_catalog_promotion_updated_after_writing_catalog_promotion(
        MessageBusInterface $eventBus,
        CatalogPromotionInterface $catalogPromotion,
        HttpKernelInterface $kernel,
        Request $request
    ): void {
        $request->getMethod()->willReturn(Request::METHOD_PUT);

        $catalogPromotion->getCode()->willReturn('Winter_sale');

        $message = new CatalogPromotionUpdated('Winter_sale');
        $eventBus->dispatch($message)->willReturn(new Envelope($message))->shouldBeCalled();

        $this->postWrite(new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            $catalogPromotion->getWrappedObject()
        ));
    }

    function it_dispatches_catalog_promotion_updated_after_writing_catalog_promotion_action(
        MessageBusInterface $eventBus,
        CatalogPromotionActionInterface $catalogPromotionAction,
        CatalogPromotionInterface $catalogPromotion,
        HttpKernelInterface $kernel,
        Request $request
    ): void {
        $request->getMethod()->willReturn(Request::METHOD_PUT);

        $catalogPromotionAction->getCatalogPromotion()->willReturn($catalogPromotion);
        $catalogPromotion->getCode()->willReturn('Winter_sale');

        $message = new CatalogPromotionUpdated('Winter_sale');
        $eventBus->dispatch($message)->willReturn(new Envelope($message))->shouldBeCalled();

        $this->postWrite(new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            $catalogPromotionAction->getWrappedObject()
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
}
