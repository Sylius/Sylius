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

use Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionRemovalAnnouncer;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionRemovalAnnouncerInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Exception\CatalogPromotionNotFoundException;
use Sylius\Component\Promotion\Exception\InvalidCatalogPromotionStateException;
use Sylius\Component\Promotion\Model\CatalogPromotionStates;
use Sylius\Component\Promotion\Repository\CatalogPromotionRepositoryInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class CatalogPromotionRemovalProcessor implements CatalogPromotionRemovalProcessorInterface
{
    public function __construct(
        private CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        /** @var CatalogPromotionRemovalAnnouncerInterface $catalogPromotionRemovalAnnouncer */
        private CatalogPromotionRemovalAnnouncerInterface|MessageBusInterface $catalogPromotionRemovalAnnouncer,
        private ?MessageBusInterface $eventBus = null, /** @phpstan-ignore-line */
    ) {
        if ($catalogPromotionRemovalAnnouncer instanceof MessageBusInterface) {
            trigger_deprecation(
                'sylius/core-bundle',
                '1.13',
                'Passing an instance of %s as second constructor argument for %s is deprecated and will be removed in Sylius 2.0. Pass an instance of %s instead.',
                MessageBusInterface::class,
                self::class,
                CatalogPromotionRemovalAnnouncerInterface::class,
            );

            $this->catalogPromotionRemovalAnnouncer = new CatalogPromotionRemovalAnnouncer($catalogPromotionRemovalAnnouncer);
        }

        if (null !== $eventBus) {
            trigger_deprecation(
                'sylius/core-bundle',
                '1.13',
                'Passing third constructor argument for %s is deprecated and will be removed in Sylius 2.0.',
                self::class,
            );
        }
    }

    public function removeCatalogPromotion(string $catalogPromotionCode): void
    {
        /** @var CatalogPromotionInterface|null $catalogPromotion */
        $catalogPromotion = $this->getCatalogPromotion($catalogPromotionCode);

        if ($catalogPromotion->getState() === CatalogPromotionStates::STATE_PROCESSING) {
            throw new InvalidCatalogPromotionStateException(
                sprintf(
                    'Catalog promotion with code "%s" cannot be removed as it is now being processed.',
                    $catalogPromotionCode,
                ),
            );
        }

        if (!in_array($catalogPromotion->getState(), [CatalogPromotionStates::STATE_ACTIVE, CatalogPromotionStates::STATE_INACTIVE], true)) {
            throw new \DomainException('Invalid catalog promotion state.');
        }

        $this->catalogPromotionRemovalAnnouncer->dispatchCatalogPromotionRemoval($catalogPromotion);
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
