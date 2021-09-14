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

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Repository\ChannelPricingRepositoryInterface;

final class CatalogPromotionClearer implements CatalogPromotionClearerInterface
{
    private ChannelPricingRepositoryInterface $channelPricingRepository;

    private EntityManagerInterface $entityManager;

    public function __construct(
        ChannelPricingRepositoryInterface $channelPricingRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->channelPricingRepository = $channelPricingRepository;
        $this->entityManager = $entityManager;
    }

    public function clear(): void
    {
        $channelPricings = $this->channelPricingRepository->findWithDiscountedPrice();
        foreach ($channelPricings as $channelPricing) {
            if (empty($channelPricing->getAppliedPromotions())) {
                continue;
            }

            $channelPricing->setPrice($channelPricing->getOriginalPrice());
            $channelPricing->clearAppliedPromotions();
        }

        $this->entityManager->flush();
    }
}
