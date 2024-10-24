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

use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\CatalogPromotionEligibilityCheckerInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\ProductVariantForCatalogPromotionEligibilityInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;

final class CatalogPromotionApplicator implements CatalogPromotionApplicatorInterface
{
    public function __construct(
        private ActionBasedDiscountApplicatorInterface $actionBasedDiscountApplicator,
        private ProductVariantForCatalogPromotionEligibilityInterface $checker,
        private CatalogPromotionEligibilityCheckerInterface $catalogPromotionEligibilityChecker,
    ) {
    }

    public function applyOnVariant(
        ProductVariantInterface $variant,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        if (!$this->catalogPromotionEligibilityChecker->isCatalogPromotionEligible($catalogPromotion)) {
            return;
        }

        if (!$this->checker->isApplicableOnVariant($catalogPromotion, $variant)) {
            return;
        }

        foreach ($catalogPromotion->getActions() as $action) {
            $this->applyDiscountFromAction($catalogPromotion, $action, $variant);
        }
    }

    private function applyDiscountFromAction(
        CatalogPromotionInterface $catalogPromotion,
        CatalogPromotionActionInterface $action,
        ProductVariantInterface $variant,
    ): void {
        /** @var ChannelInterface $channel */
        foreach ($catalogPromotion->getChannels() as $channel) {
            $channelPricing = $variant->getChannelPricingForChannel($channel);
            if ($channelPricing === null) {
                continue;
            }

            $this->actionBasedDiscountApplicator->applyDiscountOnChannelPricing($catalogPromotion, $action, $channelPricing);
        }
    }
}
