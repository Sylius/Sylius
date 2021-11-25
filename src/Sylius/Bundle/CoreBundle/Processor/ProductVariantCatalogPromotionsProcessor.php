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

namespace Sylius\Bundle\CoreBundle\Processor;

use Sylius\Bundle\CoreBundle\Applicator\CatalogPromotionApplicatorInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class ProductVariantCatalogPromotionsProcessor implements ProductVariantCatalogPromotionsProcessorInterface
{
    private CatalogPromotionClearerInterface $catalogPromotionClearer;

    private CatalogPromotionApplicatorInterface $catalogPromotionApplicator;

    public function __construct(
        CatalogPromotionClearerInterface $catalogPromotionClearer,
        CatalogPromotionApplicatorInterface $catalogPromotionApplicator
    ) {
        $this->catalogPromotionClearer = $catalogPromotionClearer;
        $this->catalogPromotionApplicator = $catalogPromotionApplicator;
    }

    public function process(ProductVariantInterface $variant): void
    {
        foreach ($variant->getChannelPricings() as $channelPricing) {
            $this->reapplyOnChannelPricing($channelPricing);
        }
    }

    private function reapplyOnChannelPricing(ChannelPricingInterface $channelPricing): void
    {
        $appliedPromotions = $channelPricing->getAppliedPromotions()->toArray();
        if (empty($appliedPromotions)) {
            return;
        }
        $this->catalogPromotionClearer->clearChannelPricing($channelPricing);
        foreach ($appliedPromotions as $catalogPromotion) {
            /** @var CatalogPromotionInterface|null $catalogPromotion */
            if (!$catalogPromotion->isEnabled()) {
                continue;
            }

            $this->catalogPromotionApplicator->applyOnChannelPricing($channelPricing, $catalogPromotion);
        }
    }
}
