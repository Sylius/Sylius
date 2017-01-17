<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Product\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Product\Factory\ProductFactory;
use Sylius\Component\Product\Factory\ProductFactoryInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
final class ProductFactorySpec extends ObjectBehavior
{
    function let(
        FactoryInterface $factory,
        FactoryInterface $variantFactory
    ) {
        $this->beConstructedWith($factory, $variantFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ProductFactory::class);
    }

    function it_implements_product_factory_interface()
    {
        $this->shouldImplement(ProductFactoryInterface::class);
    }

    function it_creates_new_product(FactoryInterface $factory, ProductInterface $product)
    {
        $factory->createNew()->willReturn($product);

        $this->createNew()->shouldReturn($product);
    }

    function it_creates_new_product_with_variant(
        FactoryInterface $factory,
        FactoryInterface $variantFactory,
        ProductInterface $product,
        ProductVariantInterface $variant
    ) {
        $variantFactory->createNew()->willReturn($variant);

        $factory->createNew()->willReturn($product);
        $product->addVariant($variant)->shouldBeCalled();

        $this->createWithVariant()->shouldReturn($product);
    }
}
