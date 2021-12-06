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
use Sylius\Component\Core\Exception\ActionBasedPriceCalculatorNotFoundException;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;

final class CatalogPromotionApplicator implements CatalogPromotionApplicatorInterface
{
    private CatalogPromotionPriceCalculatorInterface $priceCalculator;

    public function __construct(CatalogPromotionPriceCalculatorInterface $priceCalculator) {
        $this->priceCalculator = $priceCalculator;
    }

    public function applyOnVariant(
        ProductVariantInterface $variant,
        CatalogPromotionInterface $catalogPromotion
    ): void {
        foreach ($catalogPromotion->getActions() as $action) {
            $this->applyDiscountFromAction($catalogPromotion, $action, $variant);
        }
    }

    public function applyOnChannelPricing(
        ChannelPricingInterface $channelPricing,
        CatalogPromotionInterface $catalogPromotion
    ): void {
        if (!$this->hasCatalogPromotionChannelWithCode($catalogPromotion, $channelPricing->getChannelCode())) {
            return;
        }

        foreach ($catalogPromotion->getActions() as $action) {
            $this->applyDiscountFromActionOnChannelPricing($catalogPromotion, $action, $channelPricing);
        }
    }

    private function applyDiscountFromAction(
        CatalogPromotionInterface $catalogPromotion,
        CatalogPromotionActionInterface $action,
        ProductVariantInterface $variant
    ): void {
        foreach ($catalogPromotion->getChannels() as $channel) {
            $channelPricing = $variant->getChannelPricingForChannel($channel);
            if ($channelPricing === null) {
                continue;
            }

            $this->applyDiscountFromActionOnChannelPricing($catalogPromotion, $action, $channelPricing);
        }
    }

    private function applyDiscountFromActionOnChannelPricing(
        CatalogPromotionInterface $catalogPromotion,
        CatalogPromotionActionInterface $action,
        ChannelPricingInterface $channelPricing
    ): void {
        if ($channelPricing->hasExclusiveCatalogPromotionApplied()) {
            return;
        }

        if ($channelPricing->getOriginalPrice() === null) {
            $channelPricing->setOriginalPrice($channelPricing->getPrice());
        }

        if ($channelPricing->getPrice() === $channelPricing->getMinimumPrice()) {
            return;
        }

        try {
            $price = $this->priceCalculator->calculate($channelPricing, $action);
        } catch (ActionBasedPriceCalculatorNotFoundException $exception) {
            return;
        }

        $channelPricing->setPrice($price);
        $channelPricing->addAppliedPromotion($catalogPromotion);
    }

    private function hasCatalogPromotionChannelWithCode(
        CatalogPromotionInterface $catalogPromotion,
        string $channelCode
    ): bool {
        $channels = $catalogPromotion->getChannels()->filter(function (ChannelInterface $channel) use ($channelCode) {
            return $channel->getCode() === $channelCode;
        });

        return count($channels) === 1;
    }
}
