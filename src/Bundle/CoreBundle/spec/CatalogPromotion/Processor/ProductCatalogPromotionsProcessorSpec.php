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

namespace spec\Sylius\Bundle\CoreBundle\CatalogPromotion\Processor;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\CatalogPromotion\CommandDispatcher\ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\ProductCatalogPromotionsProcessorInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class ProductCatalogPromotionsProcessorSpec extends ObjectBehavior
{
    function let(ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface $commandDispatcher): void
    {
        $this->beConstructedWith($commandDispatcher);
    }

    function it_implements_product_catalog_promotions_processor_interface(): void
    {
        $this->shouldImplement(ProductCatalogPromotionsProcessorInterface::class);
    }

    function it_applies_catalog_promotion_on_products_variants(
        ProductInterface $product,
        ProductVariantInterface $firstVariant,
        ProductVariantInterface $secondVariant,
        ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface $commandDispatcher,
    ): void {
        $product->getVariants()->willReturn(new ArrayCollection([
            $firstVariant->getWrappedObject(),
            $secondVariant->getWrappedObject(),
        ]));

        $firstVariant->getCode()->willReturn('PHP_MUG');
        $secondVariant->getCode()->willReturn('SYMFONY_MUG');

        $commandDispatcher->updateVariants(['PHP_MUG', 'SYMFONY_MUG'])->shouldBeCalled();

        $this->process($product);
    }
}
