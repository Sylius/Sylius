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
use Sylius\Component\Promotion\Model\CatalogPromotionTranslationInterface;

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

            $channelPricing->addAppliedPromotion($this->formatAppliedPromotion($catalogPromotion));
        }
    }

    private function formatAppliedPromotion(CatalogPromotionInterface $catalogPromotion): array
    {
        /** @var CatalogPromotionTranslationInterface $translation */
        $translation = $catalogPromotion->getTranslations()->first();

        /** @var string $label */
        $label = $translation->getLabel();
        /** @var string $code */
        $code = $catalogPromotion->getCode();

        return [$code => ['name' => $label]];
    }
}
