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

namespace spec\Sylius\Component\Product\Resolver;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Product\Resolver\AvailableProductOptionValuesResolverInterface;

class AvailableProductOptionValuesResolverSpec extends ObjectBehavior
{
    private const PRODUCT_CODE = 'PRODUCT_CODE';

    private const PRODUCT_OPTION_CODE = 'PRODUCT_OPTION_CODE';

    function let(
        ProductInterface $product,
        ProductOptionInterface $productOption
    ) {
        $product->getCode()->willReturn(self::PRODUCT_CODE);
        $productOption->getCode()->willReturn(self::PRODUCT_OPTION_CODE);
        $product->hasOption($productOption)->willReturn(true);
    }

    function it_implements_available_product_options_resolver_interface()
    {
        $this->shouldHaveType(AvailableProductOptionValuesResolverInterface::class);
    }

    function it_throws_if_option_does_not_belong_to_product(
        ProductInterface $product,
        ProductOptionInterface $productOption
    ) {
        $product->hasOption($productOption)->willReturn(false);

        $this->shouldThrow(
            new \InvalidArgumentException(
                sprintf(
                    'Cannot resolve available product option values. Option "%s" does not belong to product "%s".',
                    self::PRODUCT_CODE,
                    self::PRODUCT_OPTION_CODE
                )
            )
        )->during('resolve', [$product, $productOption]);
    }

    function it_filters_out_values_without_related_enabled_variants(
        ProductInterface $product,
        ProductOptionInterface $productOption,
        ProductOptionValueInterface $productOptionValue1,
        ProductOptionValueInterface $productOptionValue2,
        ProductVariantInterface $productVariant
    ) {
        $productOption->getValues()->willReturn(
            new ArrayCollection(
                [
                    $productOptionValue1->getWrappedObject(),
                    $productOptionValue2->getWrappedObject(),
                ]
            )
        );
        $product->getEnabledVariants()->willReturn(new ArrayCollection([$productVariant->getWrappedObject()]));
        $productVariant->hasOptionValue($productOptionValue1)->willReturn(true);
        $productVariant->hasOptionValue($productOptionValue2)->willReturn(false);

        $this->resolve($product, $productOption)->shouldHaveCount(1);
    }
}
