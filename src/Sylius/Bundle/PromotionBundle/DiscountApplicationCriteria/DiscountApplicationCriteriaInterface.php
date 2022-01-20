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

namespace Sylius\Bundle\PromotionBundle\DiscountApplicationCriteria;

use Sylius\Component\Promotion\Model\CatalogPromotionInterface;

interface DiscountApplicationCriteriaInterface
{
    public function isApplicable(CatalogPromotionInterface $catalogPromotion, array $context): bool;
}
