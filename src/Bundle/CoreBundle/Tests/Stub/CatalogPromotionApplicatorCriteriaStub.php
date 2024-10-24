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

namespace Sylius\Bundle\CoreBundle\Tests\Stub;

use Sylius\Bundle\CoreBundle\Attribute\AsCatalogPromotionApplicatorCriteria;
use Sylius\Bundle\PromotionBundle\DiscountApplicationCriteria\DiscountApplicationCriteriaInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionInterface;

#[AsCatalogPromotionApplicatorCriteria(priority: 20)]
final class CatalogPromotionApplicatorCriteriaStub implements DiscountApplicationCriteriaInterface
{
    public function isApplicable(CatalogPromotionInterface $catalogPromotion, array $context): bool
    {
        return true;
    }
}
