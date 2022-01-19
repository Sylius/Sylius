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

namespace Sylius\Bundle\CoreBundle\EventListener;

use Sylius\Bundle\CoreBundle\Announcer\CatalogPromotionAnnouncerInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

final class CatalogPromotionEventListener
{
    private CatalogPromotionAnnouncerInterface $catalogPromotionAnnouncer;

    public function __construct(CatalogPromotionAnnouncerInterface $catalogPromotionAnnouncer)
    {
        $this->catalogPromotionAnnouncer = $catalogPromotionAnnouncer;
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
