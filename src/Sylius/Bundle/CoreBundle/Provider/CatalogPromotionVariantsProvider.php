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

namespace Sylius\Bundle\CoreBundle\Provider;

use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionScopeInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Provider\CatalogPromotionVariantsProviderInterface;

final class CatalogPromotionVariantsProvider implements CatalogPromotionVariantsProviderInterface
{
    private iterable $variantsProviders;

    public function __construct(iterable $variantsProviders)
    {
        $this->variantsProviders = $variantsProviders;
    }

    public function provideEligibleVariants(CatalogPromotionInterface $catalogPromotion): array
    {
        $variants = [];

        /** @var CatalogPromotionScopeInterface $scope */
        foreach ($catalogPromotion->getScopes() as $scope) {
            /** @var VariantsProviderInterface $provider */
            foreach ($this->variantsProviders as $provider) {
                if ($provider->supports($scope)) {
                    $variants = array_merge($variants, $provider->provideEligibleVariants($scope));
                }
            }
        }

        return $this->prepareUniqueVariants($variants);
    }

    private function prepareUniqueVariants(array $variants): array
    {
        $codes = array_map(function(ProductVariantInterface $variant) {
            return $variant->getCode();
        }, $variants);

        return array_values(array_intersect_key($variants, array_unique($codes)));
    }
}
