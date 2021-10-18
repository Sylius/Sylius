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

use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ChannelPricingRepositoryInterface;

final class CatalogPromotionClearer implements CatalogPromotionClearerInterface
{
    private ChannelPricingRepositoryInterface $channelPricingRepository;

    public function __construct(ChannelPricingRepositoryInterface $channelPricingRepository)
    {
        $this->channelPricingRepository = $channelPricingRepository;
    }

    public function clear(): void
    {
        $channelPricings = $this->channelPricingRepository->findWithDiscountedPrice();
        foreach ($channelPricings as $channelPricing) {
            $this->clearChannelPricing($channelPricing);
        }
    }

    public function clearVariant(ProductVariantInterface $variant): void
    {
        foreach ($variant->getChannelPricings() as $channelPricing) {
            $this->clearChannelPricing($channelPricing);
        }
    }

    public function clearChannelPricing(ChannelPricingInterface $channelPricing): void
    {
        if (empty($channelPricing->getAppliedPromotions())) {
            return;
        }

        if ($channelPricing->getOriginalPrice() !== null) {
            $channelPricing->setPrice($channelPricing->getOriginalPrice());
        }
        $channelPricing->clearAppliedPromotions();
    }
}
