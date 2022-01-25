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

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Announcer\BatchedVariantsUpdateAnnouncerInterface;
use Sylius\Bundle\CoreBundle\Processor\CatalogPromotionClearerInterface;
use Sylius\Bundle\CoreBundle\Processor\ProductVariantCatalogPromotionsProcessorInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class ProductVariantCatalogPromotionsProcessorSpec extends ObjectBehavior
{
    function let(
        CatalogPromotionClearerInterface $catalogPromotionClearer,
        BatchedVariantsUpdateAnnouncerInterface $announcer
    ): void {
        $this->beConstructedWith($catalogPromotionClearer, $announcer);
    }

    function it_implements_product_catalog_promotions_processor_interface(): void
    {
        $this->shouldImplement(ProductVariantCatalogPromotionsProcessorInterface::class);
    }

    function it_reapplies_catalog_promotion_on_variant(
        CatalogPromotionClearerInterface $clearer,
        ProductVariantInterface $variant,
        BatchedVariantsUpdateAnnouncerInterface $announcer
    ): void {
        $clearer->clearVariant($variant);
        $variant->getCode()->willReturn('VARIANT_CODE');
        $announcer->dispatchVariantsUpdateCommand(['VARIANT_CODE']);

        $this->process($variant);
    }
}
