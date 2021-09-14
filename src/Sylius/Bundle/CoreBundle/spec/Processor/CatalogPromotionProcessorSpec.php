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
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Provider\CatalogPromotionVariantsProviderInterface;

final class CatalogPromotionProcessorSpec extends ObjectBehavior
{
    function let(
        CatalogPromotionVariantsProviderInterface $catalogPromotionVariantsProvider,
        CatalogPromotionApplicatorInterface $productCatalogPromotionApplicator,
        EntityManagerInterface $entityManager
    ): void {
        $this->beConstructedWith(
            $catalogPromotionVariantsProvider,
            $productCatalogPromotionApplicator,
            $entityManager
        );
    }

    function it_implements_catalog_promotion_processor_interface(): void
    {
        $this->shouldImplement(CatalogPromotionProcessorInterface::class);
    }

    function it_applies_catalog_promotion_on_eligible_variants(
        CatalogPromotionVariantsProviderInterface $catalogPromotionVariantsProvider,
        CatalogPromotionApplicatorInterface $productCatalogPromotionApplicator,
        EntityManagerInterface $entityManager,
        CatalogPromotionInterface $catalogPromotion,
        ProductVariantInterface $firstVariant,
        ProductVariantInterface $secondVariant
    ): void {
        $catalogPromotionVariantsProvider
            ->provideEligibleVariants($catalogPromotion)
            ->willReturn([$firstVariant, $secondVariant])
        ;

        $productCatalogPromotionApplicator->applyCatalogPromotion($firstVariant, $catalogPromotion)->shouldBeCalled();
        $productCatalogPromotionApplicator->applyCatalogPromotion($secondVariant, $catalogPromotion)->shouldBeCalled();

        $entityManager->flush()->shouldBeCalled();

        $this->process($catalogPromotion);
    }

    function it_does_nothing_if_there_are_no_eligible_variants(
        CatalogPromotionVariantsProviderInterface $catalogPromotionVariantsProvider,
        EntityManagerInterface $entityManager,
        CatalogPromotionInterface $catalogPromotion
    ): void {
        $catalogPromotionVariantsProvider->provideEligibleVariants($catalogPromotion)->willReturn([]);

        $entityManager->flush()->shouldNotBeCalled();

        $this->process($catalogPromotion);
    }
}
