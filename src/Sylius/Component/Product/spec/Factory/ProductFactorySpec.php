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
use Sylius\Component\Archetype\Builder\ArchetypeBuilderInterface;
use Sylius\Component\Product\Factory\ProductFactoryInterface;
use Sylius\Component\Product\Model\ArchetypeInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Variation\Model\VariantInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
class ProductFactorySpec extends ObjectBehavior
{
    function let(
        FactoryInterface $factory,
        RepositoryInterface $archetypeRepository,
        ArchetypeBuilderInterface $archetypeBuilder,
        FactoryInterface $variantFactory
    ) {
        $this->beConstructedWith($factory, $archetypeRepository, $archetypeBuilder, $variantFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Product\Factory\ProductFactory');
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
        ProductInterface $product,
        VariantInterface $variant,
        FactoryInterface $variantFactory
    ) {
        $variantFactory->createNew()->willReturn($variant);

        $factory->createNew()->willReturn($product);
        $product->addVariant($variant)->shouldBeCalled();

        $this->createWithVariant()->shouldReturn($product);
    }

    function it_creates_new_product_from_archetype(
        FactoryInterface $factory,
        ProductInterface $product,
        RepositoryInterface $archetypeRepository,
        ArchetypeBuilderInterface $archetypeBuilder,
        ArchetypeInterface $archetype
    ) {
        $factory->createNew()->willReturn($product);

        $archetypeRepository->findOneBy(['code' => 'book'])->willReturn($archetype);
        $product->setArchetype($archetype)->shouldBeCalled();
        $archetypeBuilder->build($product)->shouldBeCalled();

        $this->createFromArchetype('book')->shouldReturn($product);
    }

    function it_throws_an_exception_if_archetype_does_not_exist(RepositoryInterface $archetypeRepository)
    {
        $archetypeRepository->findOneBy(['code' => 'book'])->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('createFromArchetype', ['book'])
        ;
    }
}
