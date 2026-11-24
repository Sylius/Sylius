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

namespace Sylius\Bundle\CoreBundle\CatalogPromotion\Provider;

use Sylius\Component\Promotion\Model\CatalogPromotionScopeInterface;
use Webmozart\Assert\Assert;

final class InForVariantsScopeVariantCodesProvider implements VariantCodesInScopeProviderInterface
{
    public function provide(CatalogPromotionScopeInterface $scope): array
    {
        $configuration = $scope->getConfiguration();
        Assert::keyExists($configuration, 'variants', 'This rule should have configured variants');

        return array_values($configuration['variants']);
    }
}
