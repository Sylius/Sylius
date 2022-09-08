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

namespace Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer;

use Sylius\Bundle\CoreBundle\Calculator\DelayStampCalculatorInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\UpdateCatalogPromotionState;
use Sylius\Calendar\Provider\DateTimeProviderInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Event\CatalogPromotionCreated;
use Sylius\Component\Promotion\Event\CatalogPromotionEnded;
use Sylius\Component\Promotion\Event\CatalogPromotionUpdated;
use Symfony\Component\Messenger\MessageBusInterface;

final class CatalogPromotionAnnouncer implements CatalogPromotionAnnouncerInterface
{
    public function __construct(
        private MessageBusInterface $eventBus,
        private MessageBusInterface $commandBus,
        private DelayStampCalculatorInterface $delayStampCalculator,
        private DateTimeProviderInterface $dateTimeProvider,
    ) {
    }

    public function dispatchCatalogPromotionCreatedEvent(CatalogPromotionInterface $catalogPromotion): void
    {
        $this->commandBus->dispatch(
            new UpdateCatalogPromotionState($catalogPromotion->getCode()),
            $this->calculateStartDateStamp($catalogPromotion)
        );

        $this->eventBus->dispatch(
            new CatalogPromotionCreated($catalogPromotion->getCode()),
            $this->calculateStartDateStamp($catalogPromotion),
        );

        $this->dispatchCatalogPromotionEndedEvent($catalogPromotion);
    }

    public function dispatchCatalogPromotionUpdatedEvent(CatalogPromotionInterface $catalogPromotion): void
    {
        if ($catalogPromotion->getStartDate() > $this->dateTimeProvider->now()) {
            $this->commandBus->dispatch(new UpdateCatalogPromotionState($catalogPromotion->getCode()));
            $this->eventBus->dispatch(new CatalogPromotionUpdated($catalogPromotion->getCode()));
        }

        $this->commandBus->dispatch(
            new UpdateCatalogPromotionState($catalogPromotion->getCode()),
            $this->calculateStartDateStamp($catalogPromotion)
        );

        $this->eventBus->dispatch(
            new CatalogPromotionUpdated($catalogPromotion->getCode()),
            $this->calculateStartDateStamp($catalogPromotion),
        );

        $this->dispatchCatalogPromotionEndedEvent($catalogPromotion);
    }

    private function calculateStartDateStamp(CatalogPromotionInterface $catalogPromotion): array
    {
        if ($catalogPromotion->getStartDate() !== null) {
            return [$this->delayStampCalculator->calculate($this->dateTimeProvider->now(), $catalogPromotion->getStartDate())];
        }

        return [];
    }

    private function dispatchCatalogPromotionEndedEvent(CatalogPromotionInterface $catalogPromotion): void
    {
        if ($catalogPromotion->getEndDate() !== null) {
            $delayStamp = $this->delayStampCalculator->calculate(
                $this->dateTimeProvider->now(),
                $catalogPromotion->getEndDate(),
            );

            $this->commandBus->dispatch(new UpdateCatalogPromotionState($catalogPromotion->getCode()), [$delayStamp]);
            $this->eventBus->dispatch(new CatalogPromotionEnded($catalogPromotion->getCode()), [$delayStamp]);
        }
    }
}
