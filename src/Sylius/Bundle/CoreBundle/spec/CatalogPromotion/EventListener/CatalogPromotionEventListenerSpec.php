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
use Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionAnnouncerInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class CatalogPromotionEventListenerSpec extends ObjectBehavior
{
    function let(CatalogPromotionAnnouncerInterface $catalogPromotionAnnouncer): void
    {
        $this->beConstructedWith($catalogPromotionAnnouncer);
    }

    function it_uses_announcer_to_dispatch_catalog_promotion_created_event_after_creating_catalog_promotion(
        CatalogPromotionAnnouncerInterface $catalogPromotionAnnouncer,
        GenericEvent $event,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $event->getSubject()->willReturn($catalogPromotion);

        $catalogPromotionAnnouncer->dispatchCatalogPromotionCreatedEvent($catalogPromotion)->shouldBeCalled();

        $this->handleCatalogPromotionCreatedEvent($event);
    }

    function it_uses_announcer_to_dispatch_catalog_promotion_updated_event_after_updating_catalog_promotion(
        CatalogPromotionAnnouncerInterface $catalogPromotionAnnouncer,
        GenericEvent $event,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $event->getSubject()->willReturn($catalogPromotion);

        $catalogPromotionAnnouncer->dispatchCatalogPromotionUpdatedEvent($catalogPromotion)->shouldBeCalled();

        $this->handleCatalogPromotionUpdatedEvent($event);
    }

    function it_throws_an_exception_if_event_object_is_not_a_catalog_promotion(GenericEvent $event): void
    {
        $event->getSubject()->willReturn('badObject')->shouldBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('handleCatalogPromotionUpdatedEvent', [$event])
        ;
    }
}
