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
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;

final class AllProductVariantsCatalogPromotionsProcessorSpec extends ObjectBehavior
{
    function let(
        ProductVariantRepositoryInterface $productVariantRepository,
        ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface $commandDispatcher,
    ): void {
        $this->beConstructedWith($productVariantRepository, $commandDispatcher);
    }

    function it_clears_and_processes_catalog_promotions(
        ProductVariantRepositoryInterface $productVariantRepository,
        ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface $commandDispatcher,
    ): void {
        $productVariantRepository->getCodesOfAllVariants()->willReturn(['FIRST_VARIANT_CODE', 'SECOND_VARIANT_CODE']);

        $commandDispatcher->updateVariants(['FIRST_VARIANT_CODE', 'SECOND_VARIANT_CODE'])->shouldBeCalled();

        $this->process();
    }
}
