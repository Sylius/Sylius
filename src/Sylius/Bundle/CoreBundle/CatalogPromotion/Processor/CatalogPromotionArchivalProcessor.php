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

use Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionArchivalAnnouncer;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionArchivalAnnouncerInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Exception\CatalogPromotionAlreadyArchivedException;
use Sylius\Component\Promotion\Exception\CatalogPromotionAlreadyRestoredException;
use Sylius\Component\Promotion\Exception\CatalogPromotionNotFoundException;
use Sylius\Component\Promotion\Exception\InvalidCatalogPromotionStateException;
use Sylius\Component\Promotion\Model\CatalogPromotionStates;
use Sylius\Component\Promotion\Repository\CatalogPromotionRepositoryInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class CatalogPromotionArchivalProcessor implements CatalogPromotionArchivalProcessorInterface
{
    public function __construct(
        /** @var CatalogPromotionRepositoryInterface<CatalogPromotionInterface> */
        private CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        /** @var CatalogPromotionArchivalAnnouncerInterface $catalogPromotionArchivalAnnouncer */
        private CatalogPromotionArchivalAnnouncerInterface|MessageBusInterface $catalogPromotionArchivalAnnouncer,
    ) {
        if ($catalogPromotionArchivalAnnouncer instanceof MessageBusInterface) {
            trigger_deprecation('sylius/core-bundle', '1.13', sprintf('Passing an instance of %s as second constructor argument for %s is deprecated as of Sylius 1.13 and will be removed in 2.0. Pass an instance of %s instead.', MessageBusInterface::class, self::class, CatalogPromotionArchivalAnnouncerInterface::class));

            $this->catalogPromotionArchivalAnnouncer = new CatalogPromotionArchivalAnnouncer($catalogPromotionArchivalAnnouncer);
        }
    }

    public function archiveCatalogPromotion(string $catalogPromotionCode): void
    {
        /** @var CatalogPromotionInterface|null $catalogPromotion */
        $catalogPromotion = $this->getCatalogPromotion($catalogPromotionCode);
        if (null !== $catalogPromotion->getArchivedAt()) {
            throw new CatalogPromotionAlreadyArchivedException(
                sprintf(
                    'Catalog promotion with code "%s" is already archived.',
                    $catalogPromotionCode,
                ),
            );
        }

        if ($catalogPromotion->getState() === CatalogPromotionStates::STATE_PROCESSING) {
            throw new InvalidCatalogPromotionStateException(
                sprintf(
                    'Catalog promotion with code "%s" cannot be archived as it is now being processed.',
                    $catalogPromotionCode,
                ),
            );
        }

        if (!in_array($catalogPromotion->getState(), [CatalogPromotionStates::STATE_ACTIVE, CatalogPromotionStates::STATE_INACTIVE], true)) {
            throw new \DomainException('Invalid catalog promotion state.');
        }

        $this->catalogPromotionArchivalAnnouncer->dispatchCatalogPromotionArchival($catalogPromotion);
    }

    public function restoreCatalogPromotion(string $catalogPromotionCode): void
    {
        /** @var CatalogPromotionInterface|null $catalogPromotion */
        $catalogPromotion = $this->getCatalogPromotion($catalogPromotionCode);

        if (null === $catalogPromotion->getArchivedAt()) {
            throw new CatalogPromotionAlreadyRestoredException(
                sprintf(
                    'Catalog promotion with code "%s" is already restored.',
                    $catalogPromotionCode,
                ),
            );
        }

        if ($catalogPromotion->getState() === CatalogPromotionStates::STATE_PROCESSING) {
            throw new InvalidCatalogPromotionStateException(
                sprintf(
                    'Catalog promotion with code "%s" cannot be restored as it is now being processed.',
                    $catalogPromotionCode,
                ),
            );
        }

        if (CatalogPromotionStates::STATE_INACTIVE !== $catalogPromotion->getState()) {
            throw new \DomainException('Invalid catalog promotion state.');
        }

        $this->catalogPromotionArchivalAnnouncer->dispatchCatalogPromotionRestoral($catalogPromotion);
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
