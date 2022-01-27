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

use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionScopeInterface;
use Webmozart\Assert\Assert;

final class CatalogPromotionApplicableOnVariantChecker implements CatalogPromotionApplicableOnVariantCheckerInterface
{
    public function __construct(private iterable $variantCheckers)
    {
    }

    public function isApplicableOnVariant(CatalogPromotionInterface $promotion, ProductVariantInterface $variant): bool
    {
        /** @var CatalogPromotionScopeInterface $scope */
        foreach ($promotion->getScopes() as $scope) {
            $checker = $this->getCheckerForScope($scope);
            Assert::notNull($checker, 'There is no supported catalog promotion scope');

            if ($checker->inScope($scope, $variant)) {
                return true;
            }
        }

        return false;
    }

    private function getCheckerForScope(CatalogPromotionScopeInterface $scope): ?VariantInScopeCheckerInterface
    {
        foreach ($this->variantCheckers as $checker) {
            if ($checker->supports($scope)) {
                return $checker;
            }
        }

        return null;
    }
}
