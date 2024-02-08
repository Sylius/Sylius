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

use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\UpdateCatalogPromotionState;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionStateProcessorInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class UpdateCatalogPromotionStateHandler
{
    public function __construct(
        private CatalogPromotionStateProcessorInterface $catalogPromotionStateProcessor,
        private RepositoryInterface $catalogPromotionRepository,
    ) {
    }

    public function __invoke(UpdateCatalogPromotionState $command): void
    {
        /** @var CatalogPromotionInterface|null $catalogPromotion */
        $catalogPromotion = $this->catalogPromotionRepository->findOneBy(['code' => $command->code]);
        if (null === $catalogPromotion) {
            return;
        }

        $this->catalogPromotionStateProcessor->process($catalogPromotion);
    }
}
