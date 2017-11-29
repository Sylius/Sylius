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
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\Form\DataTransformerInterface;

final class ProductsToCodesTransformerSpec extends ObjectBehavior
{
    public function let(ProductRepositoryInterface $productRepository): void
    {
        $this->beConstructedWith($productRepository);
    }

    public function it_implements_data_transformer_interface(): void
    {
        $this->shouldImplement(DataTransformerInterface::class);
    }

    public function it_transforms_array_of_products_codes_to_products_collection(
        ProductRepositoryInterface $productRepository,
        ProductInterface $bow,
        ProductInterface $sword
    ): void {
        $productRepository->findBy(['code' => ['bow', 'sword']])->willReturn([$bow, $sword]);

        $this->transform(['bow', 'sword'])->shouldIterateAs([$bow, $sword]);
    }

    public function it_transforms_only_existing_products(
        ProductRepositoryInterface $productRepository,
        ProductInterface $bow
    ): void {
        $productRepository->findBy(['code' => ['bow', 'sword']])->willReturn([$bow]);

        $this->transform(['bow', 'sword'])->shouldIterateAs([$bow]);
    }

    public function it_transforms_empty_array_into_empty_collection(): void
    {
        $this->transform([])->shouldIterateAs([]);
    }

    public function it_throws_exception_if_value_to_transform_is_not_array(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('transform', ['badObject'])
        ;
    }

    public function it_reverse_transforms_into_array_of_products_codes(
        ProductInterface $axes,
        ProductInterface $shields
    ): void {
        $axes->getCode()->willReturn('axes');
        $shields->getCode()->willReturn('shields');

        $this
            ->reverseTransform(new ArrayCollection([$axes->getWrappedObject(), $shields->getWrappedObject()]))
            ->shouldReturn(['axes', 'shields'])
        ;
    }

    public function it_throws_exception_if_reverse_transformed_object_is_not_collection(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('reverseTransform', ['badObject'])
        ;
    }

    public function it_returns_empty_array_if_passed_collection_is_empty(): void
    {
        $this->reverseTransform(new ArrayCollection())->shouldReturn([]);
    }
}
