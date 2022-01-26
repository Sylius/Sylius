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

namespace Sylius\Bundle\CoreBundle\Checker;

use Sylius\Bundle\CoreBundle\Provider\VariantsProviderInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionScopeInterface;

final class CatalogPromotionApplicableOnVariantChecker implements CatalogPromotionApplicableOnVariantCheckerInterface
{
    public function __construct(private iterable $variantsProviders)
    {
    }

    public function isApplicableOnVariant(CatalogPromotionInterface $promotion, ProductVariantInterface $variant): bool
    {
        $variants = [];

        /** @var CatalogPromotionScopeInterface $scope */
        foreach ($promotion->getScopes() as $scope) {
            /** @var VariantsProviderInterface $provider */
            foreach ($this->variantsProviders as $provider) {
                if ($provider->supports($scope)) {
                    $variants = array_merge($variants, $provider->provideEligibleVariants($scope));
                }
            }
        }

        return in_array($variant, $variants);
    }
}
