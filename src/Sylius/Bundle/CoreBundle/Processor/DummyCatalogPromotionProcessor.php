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
use Sylius\Bundle\CoreBundle\Applicator\CatalogPromotionApplicatorInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Provider\CatalogPromotionProductsProviderInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionInterface;

final class DummyCatalogPromotionProcessor implements CatalogPromotionProcessorInterface
{
    private CatalogPromotionProductsProviderInterface $catalogPromotionProductsProvider;

    private CatalogPromotionApplicatorInterface $catalogPromotionApplicator;

    private EntityManagerInterface $entityManager;

    public function __construct(
        CatalogPromotionProductsProviderInterface $catalogPromotionProductsProvider,
        CatalogPromotionApplicatorInterface $catalogPromotionApplicator,
        EntityManagerInterface $entityManager
    ) {
        $this->catalogPromotionProductsProvider = $catalogPromotionProductsProvider;
        $this->catalogPromotionApplicator = $catalogPromotionApplicator;
        $this->entityManager = $entityManager;
    }

    public function process(CatalogPromotionInterface $catalogPromotion): void
    {
        $products = $this->catalogPromotionProductsProvider->provideEligibleProducts($catalogPromotion);
        if (empty($products)) {
            return;
        }

        /** @var ProductInterface $product */
        foreach ($products as $product) {
            $this->catalogPromotionApplicator->applyPercentageDiscount($product, 0.5);
        }

        $this->entityManager->flush();
    }
}
