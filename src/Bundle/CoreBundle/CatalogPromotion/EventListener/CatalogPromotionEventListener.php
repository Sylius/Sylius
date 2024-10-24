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

namespace Sylius\Bundle\CoreBundle\CatalogPromotion\EventListener;

use Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionAnnouncerInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

final class CatalogPromotionEventListener
{
    public function __construct(private CatalogPromotionAnnouncerInterface $catalogPromotionAnnouncer)
    {
    }

    public function handleCatalogPromotionCreatedEvent(GenericEvent $event): void
    {
        /** @var CatalogPromotionInterface $catalogPromotion */
        $catalogPromotion = $event->getSubject();
        Assert::isInstanceOf($catalogPromotion, CatalogPromotionInterface::class);

        $this->catalogPromotionAnnouncer->dispatchCatalogPromotionCreatedEvent($catalogPromotion);
    }

    public function handleCatalogPromotionUpdatedEvent(GenericEvent $event): void
    {
        /** @var CatalogPromotionInterface $catalogPromotion */
        $catalogPromotion = $event->getSubject();
        Assert::isInstanceOf($catalogPromotion, CatalogPromotionInterface::class);

        $this->catalogPromotionAnnouncer->dispatchCatalogPromotionUpdatedEvent($catalogPromotion);
    }
}
