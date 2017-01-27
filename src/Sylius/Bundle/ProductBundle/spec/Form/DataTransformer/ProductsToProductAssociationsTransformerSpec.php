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

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ProductBundle\Form\DataTransformer\ProductsToProductAssociationsTransformer;
use Sylius\Component\Product\Model\ProductAssociationInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Repository\ProductRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ProductsToProductAssociationsTransformerSpec extends ObjectBehavior
{
    function let(
        FactoryInterface $productAssociationFactory,
        ProductRepositoryInterface $productRepository,
        RepositoryInterface $productAssociationTypeRepository
    ) {
        $this->beConstructedWith(
            $productAssociationFactory,
            $productRepository,
            $productAssociationTypeRepository
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ProductsToProductAssociationsTransformer::class);
    }

    function it_is_a_data_transformer()
    {
        $this->shouldImplement(DataTransformerInterface::class);
    }

    function it_transforms_null_to_empty_string()
    {
        $this->transform(null)->shouldReturn('');
    }

    function it_transforms_product_associations_to_array(
        ProductAssociationInterface $productAssociation,
        ProductAssociationTypeInterface $productAssociationType,
        ProductInterface $firstAssociatedProduct,
        ProductInterface $secondAssociatedProduct
    ) {
        $productAssociation->getType()->willReturn($productAssociationType);
        $productAssociation->getAssociatedProducts()->willReturn(
            new ArrayCollection([
                $firstAssociatedProduct->getWrappedObject(),
                $secondAssociatedProduct->getWrappedObject()
            ])
        );

        $firstAssociatedProduct->getCode()->willReturn('FIRST');
        $secondAssociatedProduct->getCode()->willReturn('SECOND');

        $productAssociationType->getCode()->willReturn('accessories');

        $this->transform([$productAssociation])->shouldReturn([
            'accessories' => 'FIRST,SECOND',
        ]);
    }

    function it_reverse_transforms_null_into_null()
    {
        $this->reverseTransform(null)->shouldReturn(null);
    }

    function it_reverse_transforms_empty_string_into_null()
    {
        $this->reverseTransform('')->shouldReturn(null);
    }
}
