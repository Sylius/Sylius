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

namespace Sylius\Bundle\CoreBundle\Applicator;

use Sylius\Bundle\CoreBundle\Calculator\CatalogPromotionPriceCalculatorInterface;
use Sylius\Bundle\CoreBundle\DiscountApplicationCriteria\DiscountApplicationCriteriaInterface;
use Sylius\Component\Core\Exception\ActionBasedPriceCalculatorNotFoundException;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;

final class ActionBasedDiscountApplicator implements ActionBasedDiscountApplicatorInterface
{
    private CatalogPromotionPriceCalculatorInterface $priceCalculator;

    private iterable $discountApplicatorCriteria;

    public function __construct(
        CatalogPromotionPriceCalculatorInterface $priceCalculator,
        iterable $discountApplicatorCriteria
    ) {
        $this->priceCalculator = $priceCalculator;
        $this->discountApplicatorCriteria = $discountApplicatorCriteria;
    }

    public function applyDiscountOnChannelPricing(
        CatalogPromotionInterface $catalogPromotion,
        CatalogPromotionActionInterface $action,
        ChannelPricingInterface $channelPricing
    ): void {
        /** @var DiscountApplicationCriteriaInterface $applicatorCriterion  */
        foreach ($this->discountApplicatorCriteria as $applicatorCriterion) {
            if (!$applicatorCriterion->isApplicable($catalogPromotion, $action, $channelPricing)) {
                return;
            }
        }

        if ($channelPricing->getOriginalPrice() === null) {
            $channelPricing->setOriginalPrice($channelPricing->getPrice());
        }

        try {
            $price = $this->priceCalculator->calculate($channelPricing, $action);
        } catch (ActionBasedPriceCalculatorNotFoundException $exception) {
            return;
        }

        $channelPricing->setPrice($price);
        $channelPricing->addAppliedPromotion($catalogPromotion);
    }
}
