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

namespace spec\Sylius\Bundle\CoreBundle\CatalogPromotion\CommandHandler;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Applicator\CatalogPromotionApplicatorInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\ApplyCatalogPromotionsOnVariants;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionClearerInterface;
use Sylius\Bundle\PromotionBundle\Provider\EligibleCatalogPromotionsProviderInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;

final class ApplyCatalogPromotionsOnVariantsHandlerSpec extends ObjectBehavior
{
    public function let(
        EligibleCatalogPromotionsProviderInterface $catalogPromotionsProvider,
        CatalogPromotionApplicatorInterface $catalogPromotionApplicator,
        ProductVariantRepositoryInterface $productVariantRepository,
        CatalogPromotionClearerInterface $clearer,
    ): void {
        $this->beConstructedWith(
            $catalogPromotionsProvider,
            $catalogPromotionApplicator,
            $productVariantRepository,
            $clearer,
        );
    }

    public function it_applies_catalog_promotion_on_provided_variants(
        CatalogPromotionInterface $firstPromotion,
        CatalogPromotionInterface $secondPromotion,
        EligibleCatalogPromotionsProviderInterface $catalogPromotionsProvider,
        ProductVariantRepositoryInterface $productVariantRepository,
        ProductVariantInterface $firstVariant,
        ProductVariantInterface $secondVariant,
        CatalogPromotionClearerInterface $clearer,
        CatalogPromotionApplicatorInterface $catalogPromotionApplicator,
    ): void {
        $catalogPromotionsProvider->provide()
            ->willReturn([$firstPromotion->getWrappedObject(), $secondPromotion->getWrappedObject()])
        ;

        $productVariantRepository->findByCodes(['FIRST_VARIANT', 'SECOND_VARIANT'])
            ->willReturn([$firstVariant->getWrappedObject(), $secondVariant->getWrappedObject()])
        ;

        $clearer->clearVariant($firstVariant)->shouldBeCalled();

        $catalogPromotionApplicator->applyOnVariant($firstVariant, $firstPromotion)->shouldBeCalled();
        $catalogPromotionApplicator->applyOnVariant($firstVariant, $secondPromotion)->shouldBeCalled();

        $clearer->clearVariant($secondVariant)->shouldBeCalled();

        $catalogPromotionApplicator->applyOnVariant($secondVariant, $firstPromotion)->shouldBeCalled();
        $catalogPromotionApplicator->applyOnVariant($secondVariant, $secondPromotion)->shouldBeCalled();

        $this(new ApplyCatalogPromotionsOnVariants(['FIRST_VARIANT', 'SECOND_VARIANT']));
    }
}
