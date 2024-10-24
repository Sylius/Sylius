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

namespace Sylius\Bundle\CoreBundle\CatalogPromotion\CommandHandler;

use Sylius\Bundle\CoreBundle\CatalogPromotion\Applicator\CatalogPromotionApplicatorInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\ApplyCatalogPromotionsOnVariants;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionClearerInterface;
use Sylius\Bundle\PromotionBundle\Provider\EligibleCatalogPromotionsProviderInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Webmozart\Assert\Assert;

final class ApplyCatalogPromotionsOnVariantsHandler
{
    public function __construct(
        private EligibleCatalogPromotionsProviderInterface $catalogPromotionsProvider,
        private CatalogPromotionApplicatorInterface $catalogPromotionApplicator,
        private ProductVariantRepositoryInterface $productVariantRepository,
        private CatalogPromotionClearerInterface $clearer,
    ) {
    }

    public function __invoke(ApplyCatalogPromotionsOnVariants $updateVariants): void
    {
        $catalogPromotions = $this->catalogPromotionsProvider->provide();

        $variants = $this->productVariantRepository->findByCodes($updateVariants->variantsCodes);

        foreach ($variants as $variant) {
            Assert::isInstanceOf($variant, ProductVariantInterface::class);
            $this->clearer->clearVariant($variant);

            foreach ($catalogPromotions as $promotion) {
                $this->catalogPromotionApplicator->applyOnVariant($variant, $promotion);
            }
        }
    }
}
