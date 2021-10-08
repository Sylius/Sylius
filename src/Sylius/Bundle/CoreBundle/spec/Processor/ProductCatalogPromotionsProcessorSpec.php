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

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Applicator\CatalogPromotionApplicatorInterface;
use Sylius\Bundle\CoreBundle\Processor\CatalogPromotionClearerInterface;
use Sylius\Bundle\CoreBundle\Processor\ProductCatalogPromotionsProcessorInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Provider\CatalogPromotionVariantsProviderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class ProductCatalogPromotionsProcessorSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $catalogPromotionRepository,
        CatalogPromotionClearerInterface $catalogPromotionClearer,
        CatalogPromotionVariantsProviderInterface $catalogPromotionVariantsProvider,
        CatalogPromotionApplicatorInterface $catalogPromotionApplicator
    ): void {
        $this->beConstructedWith(
            $catalogPromotionRepository,
            $catalogPromotionClearer,
            $catalogPromotionVariantsProvider,
            $catalogPromotionApplicator
        );
    }

    function it_implements_product_catalog_promotions_processor_interface(): void
    {
        $this->shouldImplement(ProductCatalogPromotionsProcessorInterface::class);
    }

    function it_reapplies_catalog_promotion_on_products_variants(
        RepositoryInterface $catalogPromotionRepository,
        CatalogPromotionClearerInterface $catalogPromotionClearer,
        CatalogPromotionVariantsProviderInterface $catalogPromotionVariantsProvider,
        CatalogPromotionApplicatorInterface $catalogPromotionApplicator,
        ProductInterface $product,
        ProductVariantInterface $firstVariant,
        ProductVariantInterface $secondVariant,
        CatalogPromotionInterface $catalogPromotion
    ): void {
        $product->getVariants()->willReturn(new ArrayCollection([
            $firstVariant->getWrappedObject(),
            $secondVariant->getWrappedObject(),
        ]));

        $firstVariant->__toString()->willReturn('PHP MUG');
        $secondVariant->__toString()->willReturn('Symfony Mug');

        $catalogPromotionClearer->clearVariant($firstVariant)->shouldBeCalled();
        $catalogPromotionClearer->clearVariant($secondVariant)->shouldBeCalled();

        $catalogPromotionRepository->findBy(['enabled' => true])->willReturn([$catalogPromotion->getWrappedObject()]);
        $catalogPromotionVariantsProvider->provideEligibleVariants($catalogPromotion)->willReturn([$secondVariant->getWrappedObject()]);

        $catalogPromotionApplicator->applyOnVariant($firstVariant, $catalogPromotion)->shouldNotBeCalled();
        $catalogPromotionApplicator->applyOnVariant($secondVariant, $catalogPromotion)->shouldBeCalled();

        $this->process($product);
    }

    function it_does_not_apply_promotion_if_there_are_no_eligible_variants(
        RepositoryInterface $catalogPromotionRepository,
        CatalogPromotionClearerInterface $catalogPromotionClearer,
        CatalogPromotionVariantsProviderInterface $catalogPromotionVariantsProvider,
        CatalogPromotionApplicatorInterface $catalogPromotionApplicator,
        ProductInterface $product,
        ProductVariantInterface $firstVariant,
        ProductVariantInterface $secondVariant,
        CatalogPromotionInterface $catalogPromotion
    ): void {
        $product->getVariants()->willReturn(new ArrayCollection([
            $firstVariant->getWrappedObject(),
            $secondVariant->getWrappedObject(),
        ]));

        $firstVariant->__toString()->willReturn('PHP MUG');
        $secondVariant->__toString()->willReturn('Symfony Mug');

        $catalogPromotionClearer->clearVariant($firstVariant)->shouldBeCalled();
        $catalogPromotionClearer->clearVariant($secondVariant)->shouldBeCalled();

        $catalogPromotionRepository->findBy(['enabled' => true])->willReturn([$catalogPromotion->getWrappedObject()]);
        $catalogPromotionVariantsProvider->provideEligibleVariants($catalogPromotion)->willReturn([]);

        $catalogPromotionApplicator->applyOnVariant($firstVariant, $catalogPromotion)->shouldNotBeCalled();
        $catalogPromotionApplicator->applyOnVariant($secondVariant, $catalogPromotion)->shouldNotBeCalled();

        $this->process($product);
    }
}
