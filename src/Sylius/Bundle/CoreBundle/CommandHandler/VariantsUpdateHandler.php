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
use Sylius\Bundle\PromotionBundle\Provider\EligibleCatalogPromotionsProviderInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Product\Command\UpdateBatchedVariants;
use Webmozart\Assert\Assert;

final class VariantsUpdateHandler
{
    public function __construct(
        private EligibleCatalogPromotionsProviderInterface $catalogPromotionsProvider,
        private CatalogPromotionApplicatorInterface $catalogPromotionApplicator,
        private ProductVariantRepositoryInterface $productVariantRepository,
        private iterable $defaultCriteria
    ) {
    }

    public function __invoke(UpdateBatchedVariants $updateBatchedVariants): void
    {
        $catalogPromotions = $this->catalogPromotionsProvider->provide($this->defaultCriteria);

        foreach ($updateBatchedVariants->batch as $variantCode) {
            $variant = $this->productVariantRepository->findOneBy(['code' => $variantCode]);
            Assert::notNull($variant);

            foreach ($catalogPromotions as $promotion) {
                $this->catalogPromotionApplicator->applyOnVariant($variant, $promotion);
            }
        }
    }
}
