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

namespace Tests\Sylius\Bundle\AdminBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AdminBundle\Form\DataTransformer\ProductsToProductAssociationsTransformer;
use Sylius\Component\Product\Model\ProductAssociationInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Symfony\Component\Form\DataTransformerInterface;

final class ProductsToProductAssociationsTransformerTest extends TestCase
{
    private FactoryInterface&MockObject $productAssociationFactoryMock;

    private MockObject&RepositoryInterface $productAssociationTypeRepositoryMock;

    private ProductsToProductAssociationsTransformer $productsToProductAssociationsTransformer;

    protected function setUp(): void
    {
        $this->productAssociationFactoryMock = $this->createMock(FactoryInterface::class);
        $this->productAssociationTypeRepositoryMock = $this->createMock(RepositoryInterface::class);
        $this->productsToProductAssociationsTransformer = new ProductsToProductAssociationsTransformer(
            $this->productAssociationFactoryMock,
            $this->productAssociationTypeRepositoryMock,
        );
    }

    public function testIsDataTransformer(): void
    {
        $this->assertInstanceOf(
            DataTransformerInterface::class,
            $this->productsToProductAssociationsTransformer,
        );
    }

    public function testTransformsAnEmptyCollectionToAnEmptyArray(): void
    {
        $this->assertSame([], $this->productsToProductAssociationsTransformer->transform(new ArrayCollection()));
    }

    public function testItTransformsProductAssociationsToArray(): void
    {
        $productAssociation = $this->createMock(ProductAssociationInterface::class);
        $productAssociationType = $this->createMock(ProductAssociationTypeInterface::class);
        $firstAssociatedProduct = $this->createMock(ProductInterface::class);
        $secondAssociatedProduct = $this->createMock(ProductInterface::class);

        $productAssociation->method('getType')->willReturn($productAssociationType);
        $productAssociation->method('getAssociatedProducts')->willReturn(
            new ArrayCollection([$firstAssociatedProduct, $secondAssociatedProduct]),
        );

        $firstAssociatedProduct->method('getCode')->willReturn('FIRST');
        $secondAssociatedProduct->method('getCode')->willReturn('SECOND');

        $productAssociationType->method('getCode')->willReturn('accessories');

        $result = $this->productsToProductAssociationsTransformer->transform(
            new ArrayCollection([$productAssociation]),
        );

        $this->assertInstanceOf(ArrayCollection::class, $result['accessories']);
        $this->assertSame($firstAssociatedProduct, $result['accessories']->get(0));
        $this->assertSame($secondAssociatedProduct, $result['accessories']->get(1));
    }

    public function testReverseTransformsNullIntoNull(): void
    {
        $this->assertNull($this->productsToProductAssociationsTransformer->reverseTransform(null));
    }

    public function testReverseTransformsEmptyStringIntoNull(): void
    {
        $this->assertNull($this->productsToProductAssociationsTransformer->reverseTransform(''));
    }
}
