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

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Exception\ResourceDeleteException;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;

final class ProductOptionValueDeletionListenerSpec extends ObjectBehavior
{
    function let(ProductVariantRepositoryInterface $productVariantRepository): void
    {
        $this->beConstructedWith($productVariantRepository);
    }

    function it_throws_resource_delete_exception_if_product_variants_exist_for_option_value(
        ProductVariantRepositoryInterface $productVariantRepository,
        ProductOptionValueInterface $optionValue,
    ): void {
        $optionValue->getId()->willReturn(1);
        $productVariantRepository->countByProductOptionValueId(1)->willReturn(1);

        $this->shouldThrow(ResourceDeleteException::class)->during('preRemove', [$optionValue]);
    }

    function it_does_nothing_if_no_product_variants_exist_for_option_value(
        ProductVariantRepositoryInterface $productVariantRepository,
        ProductOptionValueInterface $optionValue,
    ): void {
        $optionValue->getId()->willReturn(1);
        $productVariantRepository->countByProductOptionValueId(1)->willReturn(0);

        $this->preRemove($optionValue)->shouldReturn(null);
    }
}
