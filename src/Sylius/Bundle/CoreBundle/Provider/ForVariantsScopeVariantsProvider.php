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

use Sylius\Component\Core\Model\CatalogPromotionScopeInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Webmozart\Assert\Assert;

final class ForVariantsScopeVariantsProvider implements VariantsProviderInterface
{
    private ProductVariantRepositoryInterface $productVariantRepository;

    public function __construct(ProductVariantRepositoryInterface $productVariantRepository)
    {
        $this->productVariantRepository = $productVariantRepository;
    }

    public function supports(CatalogPromotionScopeInterface $catalogPromotionScopeType): bool
    {
        return $catalogPromotionScopeType->getType() === CatalogPromotionScopeInterface::TYPE_FOR_VARIANTS;
    }

    public function provideEligibleVariants(CatalogPromotionScopeInterface $scope): array
    {
        $configuration = $scope->getConfiguration();

        Assert::keyExists($configuration, 'variants', 'This rule should have configured variants');

        return $this->getVariants($configuration['variants']);
    }

    private function getVariants(array $configuration): array
    {
        $variants = array_map(function(string $variantCode): ?ProductVariantInterface {
            return $this->productVariantRepository->findOneBy(['code' => $variantCode]);
        }, $configuration);

        return array_filter($variants, static fn(?ProductVariantInterface $value) => $value !== null);
    }
}
