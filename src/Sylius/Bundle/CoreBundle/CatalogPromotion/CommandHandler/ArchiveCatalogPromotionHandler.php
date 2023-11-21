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

namespace Sylius\Bundle\CoreBundle\CatalogPromotion\CommandHandler;

use DateTimeImmutable;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\ArchiveCatalogPromotion;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionStateProcessorInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Exception\InvalidCatalogPromotionStateException;
use Sylius\Component\Promotion\Model\CatalogPromotionStates;
use Sylius\Component\Promotion\Repository\CatalogPromotionRepositoryInterface;

final class ArchiveCatalogPromotionHandler
{
    public function __construct(
        /** @var CatalogPromotionRepositoryInterface<CatalogPromotionInterface> */
        private CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        private CatalogPromotionStateProcessorInterface $catalogPromotionStateProcessor,
    ) {
    }

    public function __invoke(ArchiveCatalogPromotion $command): void
    {
        /** @var CatalogPromotionInterface|null $catalogPromotion */
        $catalogPromotion = $this->catalogPromotionRepository->findOneBy(['code' => $command->getCode()]);

        if (null === $catalogPromotion) {
            return;
        }

        if ($catalogPromotion->getState() !== CatalogPromotionStates::STATE_PROCESSING) {
            throw new InvalidCatalogPromotionStateException(
                sprintf(
                    'Catalog promotion with code "%s" cannot be archived as it is not in a processing state.',
                    $catalogPromotion->getCode(),
                ),
            );
        }

        $catalogPromotion->setArchivedAt(new DateTimeImmutable());
        $this->catalogPromotionStateProcessor->process($catalogPromotion);
    }
}
