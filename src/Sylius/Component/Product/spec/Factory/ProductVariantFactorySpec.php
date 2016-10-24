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
use Sylius\Component\Product\Factory\ProductVariantFactory;
use Sylius\Component\Product\Factory\ProductVariantFactoryInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class ProductVariantFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $factory)
    {
        $this->beConstructedWith($factory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ProductVariantFactory::class);
    }

    function it_is_a_resource_factory()
    {
        $this->shouldImplement(FactoryInterface::class);
    }

    function it_implements_variant_factory_interface()
    {
        $this->shouldImplement(ProductVariantFactoryInterface::class);
    }

    function it_creates_new_variant(FactoryInterface $factory, ProductVariantInterface $variant)
    {
        $factory->createNew()->willReturn($variant);

        $this->createNew()->shouldReturn($variant);
    }

    function it_creates_a_variant_and_assigns_a_product_to_it(
        FactoryInterface $factory,
        ProductInterface $product,
        ProductVariantInterface $variant
    ) {
        $factory->createNew()->willReturn($variant);
        $variant->setProduct($product)->shouldBeCalled();

        $this->createForProduct($product)->shouldReturn($variant);
    }
}
