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

namespace Tests\Sylius\Bundle\CoreBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Form\DataTransformer\ProductsToCodesTransformer;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\Form\DataTransformerInterface;

final class ProductsToCodesTransformerTest extends TestCase
{
    private MockObject&ProductRepositoryInterface $productRepository;

    private ProductsToCodesTransformer $transformer;

    protected function setUp(): void
    {
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->transformer = new ProductsToCodesTransformer($this->productRepository);
    }

    public function testImplementsDataTransformerInterface(): void
    {
        $this->assertInstanceOf(DataTransformerInterface::class, $this->transformer);
    }

    public function testTransformsArrayOfProductsCodesToProductsCollection(): void
    {
        $bow = $this->createMock(ProductInterface::class);
        $sword = $this->createMock(ProductInterface::class);

        $this->productRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(['code' => ['bow', 'sword']])
            ->willReturn([$bow, $sword])
        ;

        $result = $this->transformer->transform(['bow', 'sword']);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame([$bow, $sword], $result->toArray());
    }

    public function testTransformsOnlyExistingProducts(): void
    {
        $bow = $this->createMock(ProductInterface::class);

        $this->productRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(['code' => ['bow', 'sword']])
            ->willReturn([$bow])
        ;

        $result = $this->transformer->transform(['bow', 'sword']);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame([$bow], $result->toArray());
    }

    public function testTransformsEmptyArrayIntoEmptyCollection(): void
    {
        $result = $this->transformer->transform([]);

        $this->assertInstanceOf(Collection::class, $result);

        $this->assertCount(0, $result);
    }

    public function testThrowsExceptionIfValueToTransformIsNotArray(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->transformer->transform('not-an-array');
    }

    public function testReverseTransformsIntoArrayOfProductsCodes(): void
    {
        $axes = $this->createMock(ProductInterface::class);
        $shields = $this->createMock(ProductInterface::class);

        $axes->method('getCode')->willReturn('axes');
        $shields->method('getCode')->willReturn('shields');

        $collection = new ArrayCollection([$axes, $shields]);
        $result = $this->transformer->reverseTransform($collection);

        $this->assertSame(['axes', 'shields'], $result);
    }

    public function testThrowsExceptionIfReverseTransformedObjectIsNotCollection(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->transformer->reverseTransform('not-a-collection');
    }

    public function testReturnsEmptyArrayIfPassedCollectionIsEmpty(): void
    {
        $collection = new ArrayCollection();
        $result = $this->transformer->reverseTransform($collection);

        $this->assertSame([], $result);
    }
}
