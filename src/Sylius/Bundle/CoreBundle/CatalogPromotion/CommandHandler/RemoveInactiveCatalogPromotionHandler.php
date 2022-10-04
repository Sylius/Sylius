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

namespace Sylius\Bundle\CoreBundle\CatalogPromotion\CommandHandler;

use Doctrine\ORM\EntityManager;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\RemoveInactiveCatalogPromotion;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Exception\InvalidCatalogPromotionStateException;
use Sylius\Component\Promotion\Model\CatalogPromotionStates;
use Sylius\Component\Promotion\Repository\CatalogPromotionRepositoryInterface;

final class RemoveInactiveCatalogPromotionHandler
{
    public function __construct(
        private CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        private EntityManager $entityManager,
    ) {
    }

    public function __invoke(RemoveInactiveCatalogPromotion $command): void
    {
        /** @var CatalogPromotionInterface|null $catalogPromotion */
        $catalogPromotion = $this->catalogPromotionRepository->findOneBy(['code' => $command->code]);

        if (null === $catalogPromotion) {
            return;
        }

        if ($catalogPromotion->getState() !== CatalogPromotionStates::STATE_INACTIVE) {
            throw new InvalidCatalogPromotionStateException(
                sprintf(
                    'Catalog promotion with code "%s" cannot be removed as it is not in an inactive state.',
                    $catalogPromotion->getCode(),
                ),
            );
        }

        $this->entityManager->remove($catalogPromotion);
    }
}
