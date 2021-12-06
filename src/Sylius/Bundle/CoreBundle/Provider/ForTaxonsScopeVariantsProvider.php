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
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Webmozart\Assert\Assert;

final class ForTaxonsScopeVariantsProvider implements VariantsProviderInterface
{
    private TaxonRepositoryInterface $taxonRepository;

    private ProductVariantRepositoryInterface $productVariantRepository;

    public function __construct(
        TaxonRepositoryInterface $taxonRepository,
        ProductVariantRepositoryInterface $productVariantRepository
    ) {
        $this->taxonRepository = $taxonRepository;
        $this->productVariantRepository = $productVariantRepository;
    }

    public function supports(CatalogPromotionScopeInterface $catalogPromotionScopeType): bool
    {
        return $catalogPromotionScopeType->getType() === CatalogPromotionScopeInterface::TYPE_FOR_TAXONS;
    }

    public function provideEligibleVariants(CatalogPromotionScopeInterface $scope): array
    {
        $configuration = $scope->getConfiguration();
        Assert::keyExists($configuration, 'taxons', 'This rule should have configured taxons');

        $variants = [];
        /** @var string $taxonCode */
        foreach ($scope->getConfiguration()['taxons'] as $taxonCode) {
            /** @var TaxonInterface|null $taxon */
            $taxon = $this->taxonRepository->findOneBy(['code' => $taxonCode]);
            if (null === $taxon) {
                continue;
            }

            $variants = array_merge($variants, $this->productVariantRepository->findByTaxon($taxon));
        }

        return $variants;
    }
}
