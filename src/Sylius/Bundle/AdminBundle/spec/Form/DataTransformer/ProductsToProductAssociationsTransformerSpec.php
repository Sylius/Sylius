<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\AdminBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Product\Model\ProductAssociationInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\DataTransformerInterface;

final class ProductsToProductAssociationsTransformerSpec extends ObjectBehavior
{
    function let(
        FactoryInterface $productAssociationFactory,
        RepositoryInterface $productAssociationTypeRepository,
    ): void {
        $this->beConstructedWith(
            $productAssociationFactory,
            $productAssociationTypeRepository,
        );
    }

    function it_is_a_data_transformer(): void
    {
        $this->shouldImplement(DataTransformerInterface::class);
    }

    function it_transforms_an_empty_collection_to_an_empty_array(): void
    {
        $this->transform(new ArrayCollection())->shouldReturn([]);
    }

    function it_transforms_product_associations_to_array(
        ProductAssociationInterface $productAssociation,
        ProductAssociationTypeInterface $productAssociationType,
        ProductInterface $firstAssociatedProduct,
        ProductInterface $secondAssociatedProduct,
    ): void {
        $productAssociation->getType()->willReturn($productAssociationType);
        $productAssociation->getAssociatedProducts()->willReturn(
            new ArrayCollection([
                $firstAssociatedProduct->getWrappedObject(),
                $secondAssociatedProduct->getWrappedObject(),
            ]),
        );

        $firstAssociatedProduct->getCode()->willReturn('FIRST');
        $secondAssociatedProduct->getCode()->willReturn('SECOND');

        $productAssociationType->getCode()->willReturn('accessories');

        $this->transform(new ArrayCollection([$productAssociation->getWrappedObject()]))->shouldBeLike([
            'accessories' => new ArrayCollection([
                $firstAssociatedProduct->getWrappedObject(),
                $secondAssociatedProduct->getWrappedObject(),
            ]),
        ]);
    }

    function it_reverse_transforms_null_into_null(): void
    {
        $this->reverseTransform(null)->shouldReturn(null);
    }

    function it_reverse_transforms_empty_string_into_null(): void
    {
        $this->reverseTransform('')->shouldReturn(null);
    }
}
