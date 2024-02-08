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

namespace spec\Sylius\Bundle\ApiBundle\EventSubscriber;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionAnnouncerInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class CatalogPromotionEventSubscriberSpec extends ObjectBehavior
{
    function let(CatalogPromotionAnnouncerInterface $catalogPromotionAnnouncer): void
    {
        $this->beConstructedWith($catalogPromotionAnnouncer);
    }

    function it_uses_announcer_to_dispatch_catalog_promotion_created_event_after_writing_catalog_promotion(
        CatalogPromotionAnnouncerInterface $catalogPromotionAnnouncer,
        CatalogPromotionInterface $catalogPromotion,
        HttpKernelInterface $kernel,
        Request $request,
    ): void {
        $request->getMethod()->willReturn(Request::METHOD_POST);

        $catalogPromotionAnnouncer->dispatchCatalogPromotionCreatedEvent($catalogPromotion)->shouldBeCalled();

        $this->postWrite(new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            $catalogPromotion->getWrappedObject(),
        ));
    }

    function it_uses_announcer_to_dispatch_catalog_promotion_updated_event_after_changing_catalog_promotion(
        CatalogPromotionAnnouncerInterface $catalogPromotionAnnouncer,
        CatalogPromotionInterface $catalogPromotion,
        HttpKernelInterface $kernel,
        Request $request,
    ): void {
        $request->getMethod()->willReturn(Request::METHOD_PUT);

        $catalogPromotionAnnouncer->dispatchCatalogPromotionUpdatedEvent($catalogPromotion)->shouldBeCalled();

        $this->postWrite(new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            $catalogPromotion->getWrappedObject(),
        ));
    }

    function it_does_nothing_after_writing_other_entity(
        CatalogPromotionAnnouncerInterface $catalogPromotionAnnouncer,
        HttpKernelInterface $kernel,
        Request $request,
    ): void {
        $catalogPromotionAnnouncer->dispatchCatalogPromotionCreatedEvent(Argument::any())->shouldNotBeCalled();

        $this->postWrite(new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            new \stdClass(),
        ));
    }

    function it_does_nothing_if_there_is_a_wrong_request_method(
        CatalogPromotionAnnouncerInterface $catalogPromotionAnnouncer,
        CatalogPromotionInterface $catalogPromotion,
        HttpKernelInterface $kernel,
        Request $request,
    ): void {
        $request->getMethod()->willReturn(Request::METHOD_GET);

        $catalogPromotionAnnouncer->dispatchCatalogPromotionCreatedEvent($catalogPromotion)->shouldNotBeCalled();

        $this->postWrite(new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            $catalogPromotion->getWrappedObject(),
        ));
    }
}
