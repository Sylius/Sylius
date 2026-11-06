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

use Sylius\Bundle\TaxonomyBundle\Repository\TaxonTreeRepositoryInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionScopeInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Contracts\Service\ResetInterface;
use Webmozart\Assert\Assert;

final class InForTaxonsScopeVariantChecker implements VariantInScopeCheckerInterface, ResetInterface
{
    public const TYPE = 'for_taxons';

    /** @var array<string, list<string>> */
    private array $taxonCodesWithDescendants = [];

    public function __construct(
        private TaxonRepositoryInterface $taxonRepository,
        private TaxonTreeRepositoryInterface $taxonTreeRepository,
    ) {
    }

    public function inScope(CatalogPromotionScopeInterface $scope, ProductVariantInterface $productVariant): bool
    {
        $configuration = $scope->getConfiguration();
        Assert::keyExists($configuration, 'taxons', 'This rule should have configured taxons');

        /** @var ProductInterface $product */
        $product = $productVariant->getProduct();

        $promotionTaxons = [];
        foreach ($configuration['taxons'] as $taxonCode) {
            $promotionTaxons = array_merge($promotionTaxons, $this->getTaxonCodesWithDescendants($taxonCode));
        }

        return $product->getTaxons()->exists(
            fn ($key, TaxonInterface $taxon): bool => \in_array($taxon->getCode(), $promotionTaxons, true),
        );
    }

    public function reset(): void
    {
        $this->taxonCodesWithDescendants = [];
    }

    /** @return array<string> */
    private function getTaxonCodesWithDescendants(string $taxonCode): array
    {
        if (isset($this->taxonCodesWithDescendants[$taxonCode])) {
            return $this->taxonCodesWithDescendants[$taxonCode];
        }

        /** @var TaxonInterface $taxon */
        $taxon = $this->taxonRepository->findOneBy(['code' => $taxonCode]);
        Assert::notNull($taxon, sprintf('Taxon with code "%s" does not exist', $taxonCode));

        return $this->taxonCodesWithDescendants[$taxonCode] = [$taxonCode, ...$this->getTaxonChildrenCodes($taxon)];
    }

    /** @return array<string> */
    private function getTaxonChildrenCodes(TaxonInterface $taxon): array
    {
        $childrenCodes = [];

        foreach ($this->taxonTreeRepository->children($taxon) as $taxonChild) {
            $childrenCodes[] = $taxonChild->getCode();
        }

        return $childrenCodes;
    }
}
