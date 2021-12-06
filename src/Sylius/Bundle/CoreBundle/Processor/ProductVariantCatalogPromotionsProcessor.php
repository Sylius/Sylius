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
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class ProductVariantCatalogPromotionsProcessor implements ProductVariantCatalogPromotionsProcessorInterface
{
    private RepositoryInterface $catalogPromotionRepository;

    private CatalogPromotionClearerInterface $catalogPromotionClearer;

    private CatalogPromotionApplicatorInterface $catalogPromotionApplicator;

    public function __construct(
        RepositoryInterface $catalogPromotionRepository,
        CatalogPromotionClearerInterface $catalogPromotionClearer,
        CatalogPromotionApplicatorInterface $catalogPromotionApplicator
    ) {
        $this->catalogPromotionRepository = $catalogPromotionRepository;
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
        foreach ($appliedPromotions as $appliedPromotion) {
            /** @var CatalogPromotionInterface|null $catalogPromotion */
            $catalogPromotion = $this->catalogPromotionRepository->findOneBy(['code' => $appliedPromotion->getCode(), 'enabled' => true]);
            if ($catalogPromotion === null) {
                continue;
            }

            $this->catalogPromotionApplicator->applyOnChannelPricing($channelPricing, $catalogPromotion);
        }
    }
}
