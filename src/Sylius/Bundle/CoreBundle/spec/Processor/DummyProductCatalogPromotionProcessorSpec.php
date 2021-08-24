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

namespace spec\Sylius\Bundle\CoreBundle\Processor;

use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Applicator\ProductCatalogPromotionApplicatorInterface;
use Sylius\Bundle\CoreBundle\Processor\ProductCatalogPromotionProcessorInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;

final class DummyProductCatalogPromotionProcessorSpec extends ObjectBehavior
{
    function let(
        ProductRepositoryInterface $productRepository,
        TaxonRepositoryInterface $taxonRepository,
        ProductCatalogPromotionApplicatorInterface $productCatalogPromotionApplicator,
        EntityManagerInterface $entityManager
    ): void {
        $this->beConstructedWith(
            $productRepository,
            $taxonRepository,
            $productCatalogPromotionApplicator,
            $entityManager
        );
    }

    function it_implements_product_catalog_promotion_processor_interface(): void
    {
        $this->shouldImplement(ProductCatalogPromotionProcessorInterface::class);
    }

    function it_always_applies_50_percent_catalog_promotion_on_t_shirts_products(
        ProductRepositoryInterface $productRepository,
        TaxonRepositoryInterface $taxonRepository,
        ProductCatalogPromotionApplicatorInterface $productCatalogPromotionApplicator,
        EntityManagerInterface $entityManager,
        CatalogPromotionInterface $catalogPromotion,
        TaxonInterface $taxon,
        ProductInterface $firstProduct,
        ProductInterface $secondProduct
    ): void {
        $taxonRepository->findOneBy(['code' => 't_shirts'])->willReturn($taxon);

        $productRepository->findByTaxon($taxon)->willReturn([$firstProduct, $secondProduct]);

        $productCatalogPromotionApplicator->applyPercentageDiscount($firstProduct, 0.5)->shouldBeCalled();
        $productCatalogPromotionApplicator->applyPercentageDiscount($secondProduct, 0.5)->shouldBeCalled();

        $entityManager->flush()->shouldBeCalled();

        $this->process($catalogPromotion);
    }

    function it_does_nothing_if_there_is_not_t_shirts_taxon(
        ProductRepositoryInterface $productRepository,
        TaxonRepositoryInterface $taxonRepository,
        CatalogPromotionInterface $catalogPromotion,
        ProductCatalogPromotionApplicatorInterface $productCatalogPromotionApplicator,
        EntityManagerInterface $entityManager,
        TaxonInterface $taxon,
        ProductInterface $firstProduct,
        ProductInterface $secondProduct
    ): void {
        $taxonRepository->findOneBy(['code' => 't_shirts'])->willReturn(null);

        $productRepository->findByTaxon(Argument::any())->shouldNotBeCalled();

        $this->process($catalogPromotion);
    }
}
