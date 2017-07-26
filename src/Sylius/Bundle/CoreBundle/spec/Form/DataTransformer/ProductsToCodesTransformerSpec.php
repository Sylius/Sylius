<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Form\DataTransformer\ProductsToCodesTransformer;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class ProductsToCodesTransformerSpec extends ObjectBehavior
{
    function let(ProductRepositoryInterface $productRepository)
    {
        $this->beConstructedWith($productRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ProductsToCodesTransformer::class);
    }

    function it_implements_data_transformer_interface()
    {
        $this->shouldImplement(DataTransformerInterface::class);
    }

    function it_transforms_array_of_products_codes_to_products_collection(
        ProductRepositoryInterface $productRepository,
        ProductInterface $bow,
        ProductInterface $sword
    ) {
        $productRepository->findBy(['code' => ['bow', 'sword']])->willReturn([$bow, $sword]);

        $this->transform(['bow', 'sword'])->shouldIterateAs([$bow, $sword]);
    }

    function it_transforms_only_existing_products(
        ProductRepositoryInterface $productRepository,
        ProductInterface $bow
    ) {
        $productRepository->findBy(['code' => ['bow', 'sword']])->willReturn([$bow]);

        $this->transform(['bow', 'sword'])->shouldIterateAs([$bow]);
    }

    function it_transforms_empty_array_into_empty_collection()
    {
        $this->transform([])->shouldIterateAs([]);
    }

    function it_throws_exception_if_value_to_transform_is_not_array()
    {
        $this
            ->shouldThrow(new UnexpectedTypeException('badObject', 'array'))
            ->during('transform', ['badObject'])
        ;
    }

    function it_reverse_transforms_into_array_of_products_codes(
        ProductInterface $axes,
        ProductInterface $shields
    ) {
        $axes->getCode()->willReturn('axes');
        $shields->getCode()->willReturn('shields');

        $this
            ->reverseTransform(new ArrayCollection([$axes->getWrappedObject(), $shields->getWrappedObject()]))
            ->shouldReturn(['axes', 'shields'])
        ;
    }

    function it_throws_exception_if_reverse_transformed_object_is_not_collection()
    {
        $this
            ->shouldThrow(new UnexpectedTypeException('badObject', Collection::class))
            ->during('reverseTransform', ['badObject'])
        ;
    }

    function it_returns_empty_array_if_passed_collection_is_empty()
    {
        $this->reverseTransform(new ArrayCollection())->shouldReturn([]);
    }
}
