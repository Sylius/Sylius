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

use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;

final class CatalogPromotionApplicator implements CatalogPromotionApplicatorInterface
{
    public function applyCatalogPromotion(
        ProductVariantInterface $variant,
        CatalogPromotionInterface $catalogPromotion
    ): void {
        foreach ($catalogPromotion->getActions() as $action) {
            $this->applyDiscountFromAction($catalogPromotion, $action, $variant);
        }
    }

    private function applyDiscountFromAction(
        CatalogPromotionInterface $catalogPromotion,
        CatalogPromotionActionInterface $action,
        ProductVariantInterface $variant
    ) {
        $discount = $action->getConfiguration()['amount'];

        /** @var ChannelPricingInterface $channelPricing */
        foreach ($variant->getChannelPricings() as $channelPricing) {
            if ($channelPricing->getOriginalPrice() === null) {
                $channelPricing->setOriginalPrice($channelPricing->getPrice());
            }

            $channelPricing->setPrice((int) ($channelPricing->getPrice() - ($channelPricing->getPrice() * $discount)));

            /** @var string $label */
            $label = $catalogPromotion->getLabel();
            /** @var string $code */
            $code = $catalogPromotion->getCode();

            $channelPricing->addAppliedPromotion([$code => ['name' => $label]]);
        }
    }
}
