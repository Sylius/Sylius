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
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Provider\CatalogPromotionProductsProviderInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionRuleInterface;

final class CatalogPromotionProductsProvider implements CatalogPromotionProductsProviderInterface
{
    private ProductVariantRepositoryInterface $productVariantRepository;

    public function __construct(ProductVariantRepositoryInterface $productVariantRepository)
    {
        $this->productVariantRepository = $productVariantRepository;
    }

    public function provideEligibleProducts(CatalogPromotionInterface $catalogPromotion): array
    {
        $products = [];

        /** @var CatalogPromotionRuleInterface $rule */
        foreach ($catalogPromotion->getRules() as $rule) {
            $configuration = $rule->getConfiguration();

            /** We can do that for now, as we have only one rule */
            $products = $this->getVariantsProducts($configuration, $products);
        }

        return $products;
    }

    private function getVariantsProducts(array $configuration, array $products): array
    {
        /** @var string $variantCode */
        foreach ($configuration as $variantCode) {
            /** @var ProductVariantInterface|null $variant */
            $variant = $this->productVariantRepository->findOneBy(['code' => $variantCode]);
            if ($variant === null) {
                continue;
            }

            /** @var ProductInterface $product */
            $product = $variant->getProduct();
            if (!in_array($product, $products)) {
                $products[] = $product;
            }
        }

        return $products;
    }
}
