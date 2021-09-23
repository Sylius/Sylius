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

namespace Sylius\Bundle\CoreBundle\Listener;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\CoreBundle\Applicator\CatalogPromotionApplicatorInterface;
use Sylius\Bundle\CoreBundle\Processor\CatalogPromotionClearerInterface;
use Sylius\Component\Core\Event\ProductVariantUpdated;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class ProductVariantUpdateListener
{
    private ProductVariantRepositoryInterface $productVariantRepository;

    private RepositoryInterface $catalogPromotionRepository;

    private CatalogPromotionClearerInterface $catalogPromotionClearer;

    private CatalogPromotionApplicatorInterface $catalogPromotionApplicator;

    private EntityManagerInterface $entityManager;

    public function __construct(
        ProductVariantRepositoryInterface $productVariantRepository,
        RepositoryInterface $catalogPromotionRepository,
        CatalogPromotionClearerInterface $catalogPromotionClearer,
        CatalogPromotionApplicatorInterface $catalogPromotionApplicator,
        EntityManagerInterface $entityManager
    ) {
        $this->productVariantRepository = $productVariantRepository;
        $this->catalogPromotionRepository = $catalogPromotionRepository;
        $this->catalogPromotionClearer = $catalogPromotionClearer;
        $this->catalogPromotionApplicator = $catalogPromotionApplicator;
        $this->entityManager = $entityManager;
    }

    public function __invoke(ProductVariantUpdated $event): void
    {
        /** @var ProductVariantInterface|null $variant */
        $variant = $this->productVariantRepository->findOneBy(['code' => $event->code]);
        if ($variant === null) {
            return;
        }

        foreach ($variant->getChannelPricings() as $channelPricing) {
            $this->reapplyOnVariantsOnChannelPricing($channelPricing);
        }

        $this->entityManager->flush();
    }

    private function reapplyOnVariantsOnChannelPricing(ChannelPricingInterface $channelPricing): void
    {
        $appliedPromotions = $channelPricing->getAppliedPromotions();
        if (empty($appliedPromotions)) {
            return;
        }

        $this->catalogPromotionClearer->clearChannelPricing($channelPricing);
        foreach ($appliedPromotions as $promotionCode => $promotionData) {
            /** @var CatalogPromotionInterface|null $catalogPromotion */
            $catalogPromotion = $this->catalogPromotionRepository->findOneBy(['code' => $promotionCode]);
            if ($catalogPromotion === null) {
                continue;
            }

            $this->catalogPromotionApplicator->applyOnChannelPricing($channelPricing, $catalogPromotion);
        }
    }
}
