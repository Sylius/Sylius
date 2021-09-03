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
use Sylius\Component\Core\Provider\CatalogPromotionVariantsProviderInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionRuleInterface;

final class CatalogPromotionVariantsProvider implements CatalogPromotionVariantsProviderInterface
{
    private ProductVariantRepositoryInterface $productVariantRepository;

    public function __construct(ProductVariantRepositoryInterface $productVariantRepository)
    {
        $this->productVariantRepository = $productVariantRepository;
    }

    public function provideEligibleVariants(CatalogPromotionInterface $catalogPromotion): array
    {
        $variants = [];

        /** @var CatalogPromotionRuleInterface $rule */
        foreach ($catalogPromotion->getRules() as $rule) {
            $configuration = $rule->getConfiguration();

            /** We can do that for now, as we have only one rule */
            $variants = $this->getVariantsProducts($configuration, $variants);
        }

        return $variants;
    }

    private function getVariantsProducts(array $configuration, array $variants): array
    {
        /** @var string $variantCode */
        foreach ($configuration as $variantCode) {
            /** @var ProductVariantInterface|null $variant */
            $variant = $this->productVariantRepository->findOneBy(['code' => $variantCode]);
            if ($variant === null) {
                continue;
            }

            if (!isset($variants[$variant->getCode()])) {
                $variants[$variant->getCode()] = $variant;
            }
        }

        return array_values($variants);
    }
}
