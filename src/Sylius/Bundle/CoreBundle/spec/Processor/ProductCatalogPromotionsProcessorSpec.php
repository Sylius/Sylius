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
use Sylius\Bundle\CoreBundle\Announcer\BatchedVariantsUpdateAnnouncerInterface;
use Sylius\Bundle\CoreBundle\Applicator\CatalogPromotionApplicatorInterface;
use Sylius\Bundle\CoreBundle\Processor\CatalogPromotionClearerInterface;
use Sylius\Bundle\CoreBundle\Processor\ProductCatalogPromotionsProcessorInterface;
use Sylius\Bundle\PromotionBundle\Provider\EligibleCatalogPromotionsProviderInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Provider\CatalogPromotionVariantsProviderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class ProductCatalogPromotionsProcessorSpec extends ObjectBehavior
{
    function let(
        CatalogPromotionClearerInterface $catalogPromotionClearer,
        BatchedVariantsUpdateAnnouncerInterface $announcer
    ): void {
        $this->beConstructedWith(
            $catalogPromotionClearer,
            $announcer
        );
    }

    function it_implements_product_catalog_promotions_processor_interface(): void
    {
        $this->shouldImplement(ProductCatalogPromotionsProcessorInterface::class);
    }

    function it_applies_catalog_promotion_on_products_variants(
        CatalogPromotionClearerInterface $catalogPromotionClearer,
        ProductInterface $product,
        ProductVariantInterface $firstVariant,
        ProductVariantInterface $secondVariant,
        BatchedVariantsUpdateAnnouncerInterface $announcer
    ): void {
        $product->getVariants()->willReturn(new ArrayCollection([
            $firstVariant->getWrappedObject(),
            $secondVariant->getWrappedObject(),
        ]));

        $firstVariant->getCode()->willReturn('PHP_MUG');
        $secondVariant->getCode()->willReturn('SYMFONY_MUG');

        $catalogPromotionClearer->clearVariant($firstVariant)->shouldBeCalled();
        $catalogPromotionClearer->clearVariant($secondVariant)->shouldBeCalled();

        $announcer->dispatchVariantsUpdateCommand(['PHP_MUG', 'SYMFONY_MUG'])->shouldBeCalled();

        $this->process($product);
    }
}
