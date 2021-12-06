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

use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ChannelPricingRepositoryInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionTransitions;

final class CatalogPromotionClearer implements CatalogPromotionClearerInterface
{
    private ChannelPricingRepositoryInterface $channelPricingRepository;

    private FactoryInterface $stateMachine;

    public function __construct(
        ChannelPricingRepositoryInterface $channelPricingRepository,
        FactoryInterface $stateMachine
    ) {
        $this->channelPricingRepository = $channelPricingRepository;
        $this->stateMachine = $stateMachine;
    }

    public function clear(): void
    {
        $channelPricings = $this->channelPricingRepository->findWithDiscountedPrice();
        $catalogPromotions = [];

        foreach ($channelPricings as $channelPricing) {
            $catalogPromotions = array_merge($catalogPromotions, $channelPricing->getAppliedPromotions()->toArray());
            $this->clearChannelPricing($channelPricing);
        }

        foreach ($catalogPromotions as $catalogPromotion) {
            $stateMachine = $this->stateMachine->get($catalogPromotion, CatalogPromotionTransitions::GRAPH);
            if ($stateMachine->can(CatalogPromotionTransitions::TRANSITION_DEACTIVATE)) {
                $stateMachine->apply(CatalogPromotionTransitions::TRANSITION_DEACTIVATE);
            }
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
        if ($channelPricing->getAppliedPromotions()->isEmpty()) {
            return;
        }

        if ($channelPricing->getOriginalPrice() !== null) {
            $channelPricing->setPrice($channelPricing->getOriginalPrice());
        }
        $channelPricing->clearAppliedPromotions();
    }
}
