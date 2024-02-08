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

namespace Sylius\Bundle\CoreBundle\CatalogPromotion\Processor;

use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\RemoveInactiveCatalogPromotion;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Event\CatalogPromotionEnded;
use Sylius\Component\Promotion\Exception\CatalogPromotionNotFoundException;
use Sylius\Component\Promotion\Exception\InvalidCatalogPromotionStateException;
use Sylius\Component\Promotion\Model\CatalogPromotionStates;
use Sylius\Component\Promotion\Repository\CatalogPromotionRepositoryInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class CatalogPromotionRemovalProcessor implements CatalogPromotionRemovalProcessorInterface
{
    public function __construct(
        private CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        private MessageBusInterface $commandBus,
        private MessageBusInterface $eventBus,
    ) {
    }

    public function removeCatalogPromotion(string $catalogPromotionCode): void
    {
        /** @var CatalogPromotionInterface|null $catalogPromotion */
        $catalogPromotion = $this->getCatalogPromotion($catalogPromotionCode);

        if ($catalogPromotion->getState() === CatalogPromotionStates::STATE_INACTIVE) {
            $this->announceInactiveCatalogPromotionRemoval($catalogPromotionCode);

            return;
        }

        if ($catalogPromotion->getState() === CatalogPromotionStates::STATE_ACTIVE) {
            $this->disableCatalogPromotion($catalogPromotion);
            $this->announceCatalogPromotionEnd($catalogPromotionCode);
            $this->announceInactiveCatalogPromotionRemoval($catalogPromotionCode);

            return;
        }

        if ($catalogPromotion->getState() === CatalogPromotionStates::STATE_PROCESSING) {
            throw new InvalidCatalogPromotionStateException(
                sprintf(
                    'Catalog promotion with code "%s" cannot be removed as it is now being processed.',
                    $catalogPromotionCode,
                ),
            );
        }

        throw new \DomainException('Invalid catalog promotion state.');
    }

    private function announceCatalogPromotionEnd(string $catalogPromotionCode): void
    {
        $this->eventBus->dispatch(new CatalogPromotionEnded($catalogPromotionCode));
    }

    private function announceInactiveCatalogPromotionRemoval(string $catalogPromotionCode): void
    {
        $this->commandBus->dispatch(new RemoveInactiveCatalogPromotion($catalogPromotionCode));
    }

    private function disableCatalogPromotion(CatalogPromotionInterface $catalogPromotion): void
    {
        $catalogPromotion->setEnabled(false);
    }

    private function getCatalogPromotion(string $catalogPromotionCode): CatalogPromotionInterface
    {
        /** @var CatalogPromotionInterface|null $catalogPromotion */
        $catalogPromotion = $this->catalogPromotionRepository->findOneBy(['code' => $catalogPromotionCode]);

        if (null === $catalogPromotion) {
            throw new CatalogPromotionNotFoundException('Catalog promotion with given code does not exist.');
        }

        return $catalogPromotion;
    }
}
