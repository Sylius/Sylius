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
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ProductVariantContextSpec extends ObjectBehavior
{
    function let(ProductRepositoryInterface $productRepository, ProductVariantRepositoryInterface $productVariantRepository)
    {
        $this->beConstructedWith($productRepository, $productVariantRepository);
    }
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Transform\ProductVariantContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }

    function it_converts_product_variant_and_product_names_into_product_object(
        ProductRepositoryInterface $productRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
        ProductInterface $product,
        ProductVariantInterface $productVariant
    ) {
        $productRepository->findOneByName('Mug')->willReturn($product);
        $productVariantRepository->findOneBy(['presentation' => 'Eagle Millenium Mug', 'object' => $product])->willReturn($productVariant);

        $this->getProductVariantByNameAndProduct('Eagle Millenium Mug', 'Mug')->shouldReturn($productVariant);
    }

    function it_throws_element_not_found_exception_if_product_variant_has_not_been_found (
        ProductRepositoryInterface $productRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
        ProductInterface $product
    ) {
        $productRepository->findOneByName('T-Shirt')->willReturn($product);
        $productVariantRepository->findOneBy(['presentation' => 'Han Solo T-Shirt', 'object' => $product])->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('getProductVariantByNameAndProduct', ['Han Solo T-Shirt', 'T-Shirt']);
    }
}
