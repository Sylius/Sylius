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

namespace spec\Sylius\Bundle\CoreBundle\CommandHandler;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Applicator\CatalogPromotionApplicatorInterface;
use Sylius\Bundle\CoreBundle\Command\ApplyCatalogPromotionsOnVariants;
use Sylius\Bundle\CoreBundle\Processor\CatalogPromotionClearerInterface;
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
        CatalogPromotionClearerInterface $clearer
    ): void {
        $this->beConstructedWith(
            $catalogPromotionsProvider,
            $catalogPromotionApplicator,
            $productVariantRepository,
            $clearer
        );
    }

    public function it_applies_catalog_promotion_on_provided_variants(
        CatalogPromotionInterface $firstPromotion,
        CatalogPromotionInterface $secondPromotion,
        EligibleCatalogPromotionsProviderInterface $provider,
        ProductVariantRepositoryInterface $repository,
        ProductVariantInterface $firstVariant,
        ProductVariantInterface $secondVariant,
        CatalogPromotionClearerInterface $clearer,
        CatalogPromotionApplicatorInterface $catalogPromotionApplicator,
    ): void {
        $provider->provide()->willReturn([$firstPromotion->getWrappedObject(), $secondPromotion->getWrappedObject()]);

        $repository->findOneBy(['code' => 'FIRST_VARIANT'])->willReturn($firstVariant);
        $clearer->clearVariant($firstVariant)->shouldBeCalled();

        $catalogPromotionApplicator->applyOnVariant($firstVariant, $firstPromotion)->shouldBeCalled();
        $catalogPromotionApplicator->applyOnVariant($firstVariant, $secondPromotion)->shouldBeCalled();

        $repository->findOneBy(['code' => 'SECOND_VARIANT'])->willReturn($secondVariant);
        $clearer->clearVariant($secondVariant)->shouldBeCalled();

        $catalogPromotionApplicator->applyOnVariant($secondVariant, $secondPromotion)->shouldBeCalled();
        $catalogPromotionApplicator->applyOnVariant($secondVariant, $secondPromotion)->shouldBeCalled();

        $this->__invoke(new ApplyCatalogPromotionsOnVariants(['FIRST_VARIANT', 'SECOND_VARIANT']));
    }
}
