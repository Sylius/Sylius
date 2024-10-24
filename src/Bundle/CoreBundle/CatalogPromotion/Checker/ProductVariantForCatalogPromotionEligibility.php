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
use Sylius\Component\Core\Model\CatalogPromotionScopeInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;

final class ProductVariantForCatalogPromotionEligibility implements ProductVariantForCatalogPromotionEligibilityInterface
{
    public function __construct(private ServiceLocator $locator)
    {
    }

    public function isApplicableOnVariant(CatalogPromotionInterface $promotion, ProductVariantInterface $variant): bool
    {
        /** @var CatalogPromotionScopeInterface $scope */
        foreach ($promotion->getScopes() as $scope) {
            /** @var VariantInScopeCheckerInterface $checker */
            $checker = $this->locator->get($scope->getType());

            if ($checker->inScope($scope, $variant)) {
                return true;
            }
        }

        return false;
    }
}
