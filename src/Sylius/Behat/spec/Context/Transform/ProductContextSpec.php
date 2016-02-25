<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ProductContextSpec extends ObjectBehavior
{
    function let(RepositoryInterface $productRepository, RepositoryInterface $productVariantRepository)
    {
        $this->beConstructedWith($productRepository, $productVariantRepository);
    }
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Transform\ProductContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }

    function it_converts_product_name_into_product_object(
        RepositoryInterface $productRepository,
        ProductInterface $product
    ) {
        $productRepository->findOneBy(['name' => 'Mug'])->willReturn($product);

        $this->getProductByName('Mug')->shouldReturn($product);
    }

    function it_throws_element_not_found_exception_if_product_has_not_been_found (
        RepositoryInterface $productRepository
    ) {
        $productRepository->findOneBy(['name' => 'T-Shirt'])->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('getProductByName', ['T-Shirt']);
    }

    function it_converts_product_variant_and_product_names_into_product_object(
        RepositoryInterface $productRepository,
        RepositoryInterface $productVariantRepository,
        ProductInterface $product,
        ProductVariantInterface $productVariant
    ) {
        $productRepository->findOneBy(['name' => 'Mug'])->willReturn($product);
        $productVariantRepository->findOneBy(['presentation' => 'Eagle Millenium Mug', 'object' => $product])->willReturn($productVariant);

        $this->getProductVariantByNameAndProduct('Eagle Millenium Mug', 'Mug')->shouldReturn($productVariant);
    }

    function it_throws_element_not_found_exception_if_product_variant_has_not_been_found (
        RepositoryInterface $productRepository,
        RepositoryInterface $productVariantRepository,
        ProductInterface $product
    ) {
        $productRepository->findOneBy(['name' => 'T-Shirt'])->willReturn($product);
        $productVariantRepository->findOneBy(['presentation' => 'Han Solo T-Shirt', 'object' => $product])->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('getProductVariantByNameAndProduct', ['Han Solo T-Shirt', 'T-Shirt']);
    }
}
