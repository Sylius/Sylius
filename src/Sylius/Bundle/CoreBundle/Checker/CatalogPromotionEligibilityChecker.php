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

use Sylius\Bundle\PromotionBundle\Provider\EligibleCatalogPromotionProviderInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;

final class CatalogPromotionEligibilityChecker implements CatalogPromotionEligibilityCheckerInterface
{
    public function __construct(private EligibleCatalogPromotionProviderInterface $eligibleCatalogPromotionProvider)
    {
    }

    public function isCatalogPromotionEligible(CatalogPromotionInterface $promotion): bool
    {
        return $this->eligibleCatalogPromotionProvider->provide($promotion) !== null;
    }
}
