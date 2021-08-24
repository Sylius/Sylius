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
use Sylius\Bundle\CoreBundle\Applicator\ProductCatalogPromotionApplicatorInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;

final class DummyProductCatalogPromotionProcessor implements ProductCatalogPromotionProcessorInterface
{
    private ProductRepositoryInterface $productRepository;

    private TaxonRepositoryInterface $taxonRepository;

    private ProductCatalogPromotionApplicatorInterface $productCatalogPromotionApplicator;

    private EntityManagerInterface $entityManager;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        TaxonRepositoryInterface $taxonRepository,
        ProductCatalogPromotionApplicatorInterface $productCatalogPromotionApplicator,
        EntityManagerInterface $entityManager
    ) {
        $this->productRepository = $productRepository;
        $this->taxonRepository = $taxonRepository;
        $this->productCatalogPromotionApplicator = $productCatalogPromotionApplicator;
        $this->entityManager = $entityManager;
    }

    public function process(CatalogPromotionInterface $catalogPromotion): void
    {
        /** @var TaxonInterface|null $taxon */
        $taxon = $this->taxonRepository->findOneBy(['code' => 't_shirts']);
        if ($taxon === null) {
            return;
        }

        $products = $this->productRepository->findByTaxon($taxon);
        /** @var ProductInterface $product */
        foreach ($products as $product) {
            $this->productCatalogPromotionApplicator->applyPercentageDiscount($product, 0.5);
        }

        $this->entityManager->flush();
    }
}
