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

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;

final class CatalogPromotionApplicator implements CatalogPromotionApplicatorInterface
{
    public function __construct(
        private ChannelRepositoryInterface $channelRepository,
        private ActionBasedDiscountApplicatorInterface $actionBasedDiscountApplicator
    ) {}

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
        $channel = $this->channelRepository->findOneByCode($channelPricing->getChannelCode());

        if (!$catalogPromotion->hasChannel($channel)) {
            return;
        }

        foreach ($catalogPromotion->getActions() as $action) {
            $this->actionBasedDiscountApplicator->applyDiscountOnChannelPricing($catalogPromotion, $action, $channelPricing);
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

            $this->actionBasedDiscountApplicator->applyDiscountOnChannelPricing($catalogPromotion, $action, $channelPricing);
        }
    }
}
