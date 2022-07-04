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

namespace Sylius\Bundle\CoreBundle\CatalogPromotion\Checker;

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionScopeInterface;
use Webmozart\Assert\Assert;

final class InForTaxonsScopeVariantChecker implements VariantInScopeCheckerInterface
{
    public const TYPE = 'for_taxons';

    public function inScope(CatalogPromotionScopeInterface $scope, ProductVariantInterface $productVariant): bool
    {
        $configuration = $scope->getConfiguration();
        Assert::keyExists($configuration, 'taxons', 'This rule should have configured taxons');

        /** @var ProductInterface $product */
        $product = $productVariant->getProduct();

        return $product->getTaxons()->exists(
            fn ($key, TaxonInterface $taxon): bool => \in_array($taxon->getCode(), $scope->getConfiguration()['taxons'], true),
        );
    }
}
