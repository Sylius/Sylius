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

namespace Sylius\Bundle\CoreBundle\CommandHandler;

use Sylius\Bundle\CoreBundle\Applicator\CatalogPromotionApplicatorInterface;
use Sylius\Bundle\CoreBundle\Command\ApplyCatalogPromotionsOnVariants;
use Sylius\Bundle\CoreBundle\Processor\CatalogPromotionClearerInterface;
use Sylius\Bundle\PromotionBundle\Provider\EligibleCatalogPromotionsProviderInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;

final class ApplyCatalogPromotionsOnVariantsHandler
{
    public function __construct(
        private EligibleCatalogPromotionsProviderInterface $catalogPromotionsProvider,
        private CatalogPromotionApplicatorInterface $catalogPromotionApplicator,
        private ProductVariantRepositoryInterface $productVariantRepository,
        private CatalogPromotionClearerInterface $clearer
    ) {
    }

    public function __invoke(ApplyCatalogPromotionsOnVariants $updateVariants): void
    {
        $catalogPromotions = $this->catalogPromotionsProvider->provide();

//        $variants = $this->productVariantRepository->findBy(['code' => $updateVariants->variantsCodes]);

        foreach ($updateVariants->variantsCodes as $variantCode) {
            $variant = $this->productVariantRepository->findOneBy(['code' => $variantCode]);

            $this->clearer->clearVariant($variant);

            foreach ($catalogPromotions as $promotion) {
                $this->catalogPromotionApplicator->applyOnVariant($variant, $promotion);
            }
        }
    }
}
