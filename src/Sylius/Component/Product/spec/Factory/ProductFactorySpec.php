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
use Prophecy\Argument;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Translation\Factory\TranslatableFactoryInterface;
use Sylius\Component\Variation\Model\VariantInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
class ProductFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $variantFactory, TranslatableFactoryInterface $translatableFactory)
    {
        $this->beConstructedWith($translatableFactory, $variantFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Product\Factory\ProductFactory');
    }

    function it_implements_factory_interface()
    {
        $this->shouldImplement(FactoryInterface::class);
    }

    function it_creates_new_product_with_variant(
        ProductInterface $product,
        VariantInterface $variant,
        FactoryInterface $variantFactory,
        TranslatableFactoryInterface $translatableFactory
    ) {
        $variantFactory->createNew()->shouldBeCalled()->willReturn($variant);
        $variant->setMaster(true)->shouldBeCalled();

        $translatableFactory->createNew()->shouldBeCalled()->willReturn($product);
        $product->setMasterVariant($variant)->shouldBeCalled();

        $this->createNew()->shouldReturn($product);
    }
}
