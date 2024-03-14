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

namespace Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer;

use Sylius\Bundle\CoreBundle\Calculator\DelayStampCalculatorInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Event\CatalogPromotionCreated;
use Sylius\Component\Promotion\Event\CatalogPromotionEnded;
use Sylius\Component\Promotion\Event\CatalogPromotionUpdated;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class CatalogPromotionAnnouncer implements CatalogPromotionAnnouncerInterface
{
    public function __construct(
        private MessageBusInterface $eventBus,
        private DelayStampCalculatorInterface $delayStampCalculator,
        private ClockInterface $clock,
    ) {
    }

    public function dispatchCatalogPromotionCreatedEvent(CatalogPromotionInterface $catalogPromotion): void
    {
        $this->eventBus->dispatch(
            new CatalogPromotionCreated($catalogPromotion->getCode()),
            $this->calculateStartDateStamp($catalogPromotion),
        );

        $this->dispatchCatalogPromotionEndedEvent($catalogPromotion);
    }

    public function dispatchCatalogPromotionUpdatedEvent(CatalogPromotionInterface $catalogPromotion): void
    {
        if ($catalogPromotion->getStartDate() > $this->clock->now()) {
            $this->eventBus->dispatch(new CatalogPromotionUpdated($catalogPromotion->getCode()), []);
        }

        $this->eventBus->dispatch(
            new CatalogPromotionUpdated($catalogPromotion->getCode()),
            $this->calculateStartDateStamp($catalogPromotion),
        );

        $this->dispatchCatalogPromotionEndedEvent($catalogPromotion);
    }

    private function calculateStartDateStamp(CatalogPromotionInterface $catalogPromotion): array
    {
        if ($catalogPromotion->getStartDate() !== null) {
            return [$this->delayStampCalculator->calculate($this->clock->now(), $catalogPromotion->getStartDate())];
        }

        return [];
    }

    private function dispatchCatalogPromotionEndedEvent(CatalogPromotionInterface $catalogPromotion): void
    {
        if ($catalogPromotion->getEndDate() !== null) {
            $this->eventBus->dispatch(
                new CatalogPromotionEnded($catalogPromotion->getCode()),
                [$this->delayStampCalculator->calculate($this->clock->now(), $catalogPromotion->getEndDate())],
            );
        }
    }
}
