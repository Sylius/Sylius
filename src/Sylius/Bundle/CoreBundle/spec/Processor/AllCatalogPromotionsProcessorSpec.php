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
use Sylius\Bundle\CoreBundle\Commander\UpdateVariantsCommanderInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;

final class AllCatalogPromotionsProcessorSpec extends ObjectBehavior
{
    function let(
        ProductVariantRepositoryInterface $productVariantRepository,
        UpdateVariantsCommanderInterface $commander
    ): void {
        $this->beConstructedWith($productVariantRepository, $commander);
    }

    function it_clears_and_processes_catalog_promotions(
        ProductVariantRepositoryInterface $productVariantRepository,
        UpdateVariantsCommanderInterface $commander
    ): void {
        $productVariantRepository->getCodesOfAllVariants()->willReturn(['FIRST_VARIANT_CODE', 'SECOND_VARIANT_CODE']);

        $commander->updateVariants(['FIRST_VARIANT_CODE', 'SECOND_VARIANT_CODE'])->shouldBeCalled();

        $this->process();
    }
}
