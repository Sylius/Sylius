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

use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionScopeInterface;
use Webmozart\Assert\Assert;

final class InForProductScopeVariantChecker implements VariantInScopeCheckerInterface
{
    public const TYPE = 'for_products';

    public function inScope(CatalogPromotionScopeInterface $scope, ProductVariantInterface $productVariant): bool
    {
        $configuration = $scope->getConfiguration();
        Assert::keyExists($configuration, 'products', 'This rule should have configured products');

        $variantProductCode = $productVariant->getProduct()->getCode();

        return \in_array($variantProductCode, $scope->getConfiguration()['products'], true);
    }
}
