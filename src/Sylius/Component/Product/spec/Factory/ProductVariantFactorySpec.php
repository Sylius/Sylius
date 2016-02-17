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
use Sylius\Component\Product\Factory\ProductVariantFactoryInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\VariantInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ProductVariantFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $factory, RepositoryInterface $productRepository)
    {
        $this->beConstructedWith($factory, $productRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Product\Factory\ProductVariantFactory');
    }

    function it_is_a_resource_factory()
    {
        $this->shouldImplement(FactoryInterface::class);
    }

    function it_implements_variant_factory_interface()
    {
        $this->shouldImplement(ProductVariantFactoryInterface::class);
    }

    function it_creates_new_variant(FactoryInterface $factory, VariantInterface $variant)
    {
        $factory->createNew()->willReturn($variant);

        $this->createNew()->shouldReturn($variant);
    }

    function it_throws_an_exception_when_product_is_not_found(RepositoryInterface $productRepository)
    {
        $productRepository->find(15)->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('createForProductWithId', [15])
        ;
    }

    function it_creates_a_variant_and_assigns_a_product_to_id(
        FactoryInterface $factory,
        RepositoryInterface $productRepository,
        ProductInterface $product,
        VariantInterface $variant
    ) {
        $factory->createNew()->willReturn($variant);
        $productRepository->find(13)->willReturn($product);
        $variant->setProduct($product)->shouldBeCalled();

        $this->createForProductWithId(13)->shouldReturn($variant);
    }
}
