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
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Provider\CatalogPromotionVariantsProviderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class ProductCatalogPromotionsProcessor implements ProductCatalogPromotionsProcessorInterface
{
    private RepositoryInterface $catalogPromotionRepository;

    private CatalogPromotionClearerInterface $catalogPromotionClearer;

    private CatalogPromotionVariantsProviderInterface $catalogPromotionVariantsProvider;

    private CatalogPromotionApplicatorInterface $catalogPromotionApplicator;

    public function __construct(
        RepositoryInterface $catalogPromotionRepository,
        CatalogPromotionClearerInterface $catalogPromotionClearer,
        CatalogPromotionVariantsProviderInterface $catalogPromotionVariantsProvider,
        CatalogPromotionApplicatorInterface $catalogPromotionApplicator
    ) {
        $this->catalogPromotionRepository = $catalogPromotionRepository;
        $this->catalogPromotionClearer = $catalogPromotionClearer;
        $this->catalogPromotionVariantsProvider = $catalogPromotionVariantsProvider;
        $this->catalogPromotionApplicator = $catalogPromotionApplicator;
    }

    public function process(ProductInterface $product): void
    {
        $variants = $product->getVariants()->toArray();
        $this->clearVariants($variants);

        foreach ($this->catalogPromotionRepository->findBy(['enabled' => true]) as $catalogPromotion) {
            $this->processCatalogPromotionOnVariants($catalogPromotion, $variants);
        }
    }

    private function clearVariants(array $variants): void
    {
        foreach ($variants as $variant) {
            $this->catalogPromotionClearer->clearVariant($variant);
        }
    }

    private function processCatalogPromotionOnVariants(CatalogPromotionInterface $catalogPromotion, array $variants): void
    {
        $eligibleVariants = $this->catalogPromotionVariantsProvider->provideEligibleVariants($catalogPromotion);
        $variantsToApplication = array_intersect($variants, $eligibleVariants);

        foreach ($variantsToApplication as $variant) {
            $this->catalogPromotionApplicator->applyOnVariant($variant, $catalogPromotion);
        }
    }
}
