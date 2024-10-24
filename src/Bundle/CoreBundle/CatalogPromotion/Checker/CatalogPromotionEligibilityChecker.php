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

namespace Sylius\Bundle\CoreBundle\CatalogPromotion\Checker;

use Sylius\Component\Core\Model\CatalogPromotionInterface;

final class CatalogPromotionEligibilityChecker implements CatalogPromotionEligibilityCheckerInterface
{
    public function __construct(private iterable $defaultCriteria = [])
    {
    }

    public function isCatalogPromotionEligible(CatalogPromotionInterface $catalogPromotion): bool
    {
        foreach ($this->defaultCriteria as $criterion) {
            if (!$criterion->verify($catalogPromotion)) {
                return false;
            }
        }

        return true;
    }
}
