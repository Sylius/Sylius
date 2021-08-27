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
use Sylius\Bundle\CoreBundle\Applicator\CatalogPromotionApplicatorInterface;
use Sylius\Bundle\CoreBundle\Processor\CatalogPromotionProcessorInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Provider\CatalogPromotionProductsProviderInterface;

final class DummyCatalogPromotionProcessorSpec extends ObjectBehavior
{
    function let(
        CatalogPromotionProductsProviderInterface $catalogPromotionProductsProvider,
        CatalogPromotionApplicatorInterface $productCatalogPromotionApplicator,
        EntityManagerInterface $entityManager
    ): void {
        $this->beConstructedWith(
            $catalogPromotionProductsProvider,
            $productCatalogPromotionApplicator,
            $entityManager
        );
    }

    function it_implements_catalog_promotion_processor_interface(): void
    {
        $this->shouldImplement(CatalogPromotionProcessorInterface::class);
    }

    function it_always_applies_50_percent_catalog_promotion_on_products_from_eligible_taxon(
        CatalogPromotionProductsProviderInterface $catalogPromotionProductsProvider,
        CatalogPromotionApplicatorInterface $productCatalogPromotionApplicator,
        EntityManagerInterface $entityManager,
        CatalogPromotionInterface $catalogPromotion,
        ProductInterface $firstProduct,
        ProductInterface $secondProduct
    ): void {
        $catalogPromotionProductsProvider
            ->provideEligibleProducts($catalogPromotion)
            ->willReturn([$firstProduct, $secondProduct])
        ;

        $productCatalogPromotionApplicator->applyPercentageDiscount($firstProduct, 0.5)->shouldBeCalled();
        $productCatalogPromotionApplicator->applyPercentageDiscount($secondProduct, 0.5)->shouldBeCalled();

        $entityManager->flush()->shouldBeCalled();

        $this->process($catalogPromotion);
    }

    function it_does_nothing_if_there_is_no_t_shirts_taxon(
        CatalogPromotionProductsProviderInterface $catalogPromotionProductsProvider,
        EntityManagerInterface $entityManager,
        CatalogPromotionInterface $catalogPromotion
    ): void {
        $catalogPromotionProductsProvider->provideEligibleProducts($catalogPromotion)->willReturn([]);

        $entityManager->flush()->shouldNotBeCalled();

        $this->process($catalogPromotion);
    }
}
