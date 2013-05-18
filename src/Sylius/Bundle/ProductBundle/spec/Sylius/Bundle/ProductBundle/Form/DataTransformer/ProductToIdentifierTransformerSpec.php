<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ProductBundle\Form\DataTransformer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ProductToIdentifierTransformerSpec extends ObjectBehavior
{
    /**
     * @param Doctrine\Common\Persistence\ObjectRepository $productRepository
     */
    function let($productRepository)
    {
        $this->beConstructedWith($productRepository, 'name');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ProductBundle\Form\DataTransformer\ProductToIdentifierTransformer');
    }

    function it_returns_empty_string_if_null_transormed()
    {
        $this->transform(null)->shouldReturn('');
    }

    function it_throws_exception_if_not_Sylius_product_transformed()
    {
        $this
            ->shouldThrow('Symfony\Component\Form\Exception\UnexpectedTypeException')
            ->duringTransform(new \stdClass())
        ;
    }

    /**
     * @param Sylius\Bundle\ProductBundle\Model\ProductInterface $product
     */
    function it_transforms_product_into_its_identifier_value($product)
    {
        $product->getName()->willReturn('IPHONE5');

        $this->transform($product)->shouldReturn('IPHONE5');
    }

    function it_returns_null_if_empty_string_reverse_transformed()
    {
        $this->reverseTransform('')->shouldReturn(null);
    }

    function it_returns_null_if_product_not_found_on_reverse_transform($productRepository)
    {
        $productRepository
            ->findOneBy(array('name' => 'IPHONE5WHITE'))
            ->shouldBeCalled()
            ->willReturn(null)
        ;

        $this->reverseTransform('IPHONE5WHITE')->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\ProductBundle\Model\ProductInterface $product
     */
    function it_returns_product_if_found_on_reverse_transform($productRepository, $product)
    {
        $productRepository
            ->findOneBy(array('name' => 'IPHONE5'))
            ->shouldBeCalled()
            ->willReturn($product)
        ;

        $this->reverseTransform('IPHONE5')->shouldReturn($product);
    }
}
