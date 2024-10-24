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

namespace Sylius\Bundle\CoreBundle\CatalogPromotion\Applicator;

use Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\CatalogPromotionPriceCalculatorInterface;
use Sylius\Bundle\PromotionBundle\DiscountApplicationCriteria\DiscountApplicationCriteriaInterface;
use Sylius\Component\Core\Exception\ActionBasedPriceCalculatorNotFoundException;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;

final class ActionBasedDiscountApplicator implements ActionBasedDiscountApplicatorInterface
{
    public function __construct(
        private CatalogPromotionPriceCalculatorInterface $priceCalculator,
        private iterable $discountApplicatorCriteria,
    ) {
    }

    public function applyDiscountOnChannelPricing(
        CatalogPromotionInterface $catalogPromotion,
        CatalogPromotionActionInterface $action,
        ChannelPricingInterface $channelPricing,
    ): void {
        /** @var DiscountApplicationCriteriaInterface $applicatorCriterion */
        foreach ($this->discountApplicatorCriteria as $applicatorCriterion) {
            if (!$applicatorCriterion->isApplicable($catalogPromotion, ['channelPricing' => $channelPricing, 'action' => $action])) {
                return;
            }
        }

        if ($channelPricing->getAppliedPromotions()->isEmpty() && $channelPricing->getOriginalPrice() !== null) {
            $channelPricing->setPrice($channelPricing->getOriginalPrice());
        }

        try {
            $price = $this->priceCalculator->calculate($channelPricing, $action);
        } catch (ActionBasedPriceCalculatorNotFoundException) {
            return;
        }

        if (null === $channelPricing->getOriginalPrice()) {
            $channelPricing->setOriginalPrice($channelPricing->getPrice());
        }

        $channelPricing->setPrice($price);
        $channelPricing->addAppliedPromotion($catalogPromotion);
    }
}
