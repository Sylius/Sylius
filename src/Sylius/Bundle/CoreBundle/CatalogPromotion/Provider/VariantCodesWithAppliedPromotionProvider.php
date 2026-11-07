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

namespace Sylius\Bundle\CoreBundle\CatalogPromotion\Provider;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionInterface;

final class VariantCodesWithAppliedPromotionProvider implements VariantCodesWithAppliedPromotionProviderInterface
{
    /**
     * @param class-string $productVariantClass
     * @param class-string $channelPricingClass
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly string $productVariantClass,
        private readonly string $channelPricingClass,
    ) {
    }

    public function provide(CatalogPromotionInterface $catalogPromotion): array
    {
        return array_values($this->entityManager
            ->createQuery(sprintf(
                'SELECT variant.code
                FROM %s variant
                WHERE EXISTS (
                    SELECT channelPricing.id
                    FROM %s channelPricing
                    WHERE channelPricing.productVariant = variant
                        AND :catalogPromotion MEMBER OF channelPricing.appliedPromotions
                )',
                $this->productVariantClass,
                $this->channelPricingClass,
            ))
            ->setParameter('catalogPromotion', $catalogPromotion)
            ->getSingleColumnResult())
        ;
    }
}
