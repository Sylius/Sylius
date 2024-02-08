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

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\CatalogPromotion\CommandDispatcher\ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\ProductVariantCatalogPromotionsProcessorInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class ProductVariantCatalogPromotionsProcessorSpec extends ObjectBehavior
{
    function let(ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface $commandDispatcher): void
    {
        $this->beConstructedWith($commandDispatcher);
    }

    function it_implements_product_catalog_promotions_processor_interface(): void
    {
        $this->shouldImplement(ProductVariantCatalogPromotionsProcessorInterface::class);
    }

    function it_reapplies_catalog_promotion_on_variant(
        ProductVariantInterface $variant,
        ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface $commandDispatcher,
    ): void {
        $variant->getCode()->willReturn('VARIANT_CODE');
        $commandDispatcher->updateVariants(['VARIANT_CODE']);

        $this->process($variant);
    }
}
