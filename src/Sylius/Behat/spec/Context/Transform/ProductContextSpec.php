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
use Sylius\Component\Core\Repository\ProductRepositoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ProductContextSpec extends ObjectBehavior
{
    function let(ProductRepositoryInterface $productRepository)
    {
        $this->beConstructedWith($productRepository);
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
        ProductRepositoryInterface $productRepository,
        ProductInterface $product
    ) {
        $productRepository->findOneByName('Mug')->willReturn($product);

        $this->getProductByName('Mug')->shouldReturn($product);
    }

    function it_throws_element_not_found_exception_if_product_has_not_been_found (
        ProductRepositoryInterface $productRepository
    ) {
        $productRepository->findOneByName('T-Shirt')->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('getProductByName', ['T-Shirt']);
    }
}
