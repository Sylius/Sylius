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

namespace Sylius\Bundle\PromotionBundle\Provider;

use Sylius\Component\Promotion\Repository\CatalogPromotionRepositoryInterface;

final class EligibleCatalogPromotionsProvider implements EligibleCatalogPromotionsProviderInterface
{
    public function __construct(
        private CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        private iterable $defaultCriteria = [],
    ) {
    }

    public function provide(): array
    {
        return $this->catalogPromotionRepository->findByCriteria($this->defaultCriteria);
    }
}
