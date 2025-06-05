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

namespace Tests\Sylius\Bundle\ProductBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ProductBundle\Form\DataTransformer\ProductsToProductAssociationsTransformer;
use Sylius\Component\Product\Model\ProductAssociationInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Repository\ProductRepositoryInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Symfony\Component\Form\DataTransformerInterface;

final class ProductsToProductAssociationsTransformerTest extends TestCase
{
    private FactoryInterface&MockObject $productAssociationFactory;

    private MockObject&ProductRepositoryInterface $productRepository;

    private MockObject&RepositoryInterface $productAssociationTypeRepository;

    private ProductsToProductAssociationsTransformer $productsToProductAssociationsTransformer;

    protected function setUp(): void
    {
        $this->productAssociationFactory = $this->createMock(FactoryInterface::class);
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->productAssociationTypeRepository = $this->createMock(RepositoryInterface::class);

        $this->productsToProductAssociationsTransformer = new ProductsToProductAssociationsTransformer(
            $this->productAssociationFactory,
            $this->productRepository,
            $this->productAssociationTypeRepository,
        );
    }

    public function testDataTransformer(): void
    {
        $this->assertInstanceOf(DataTransformerInterface::class, $this->productsToProductAssociationsTransformer);
    }

    public function testTransformsNullToEmptyString(): void
    {
        $this->assertSame('', $this->productsToProductAssociationsTransformer->transform(null));
    }

    public function testTransformsProductAssociationsToArray(): void
    {
        /** @var ProductAssociationInterface&MockObject $productAssociation */
        $productAssociation = $this->createMock(ProductAssociationInterface::class);
        /** @var ProductAssociationTypeInterface&MockObject $productAssociationType */
        $productAssociationType = $this->createMock(ProductAssociationTypeInterface::class);
        /** @var ProductInterface&MockObject $firstAssociatedProduct */
        $firstAssociatedProduct = $this->createMock(ProductInterface::class);
        /** @var ProductInterface&MockObject $secondAssociatedProduct */
        $secondAssociatedProduct = $this->createMock(ProductInterface::class);

        $productAssociation->expects($this->once())->method('getType')->willReturn($productAssociationType);
        $productAssociation
            ->expects($this->once())
            ->method('getAssociatedProducts')
            ->willReturn(new ArrayCollection([$firstAssociatedProduct, $secondAssociatedProduct]))
        ;
        $firstAssociatedProduct->expects($this->once())->method('getCode')->willReturn('FIRST');
        $secondAssociatedProduct->expects($this->once())->method('getCode')->willReturn('SECOND');
        $productAssociationType->expects($this->once())->method('getCode')->willReturn('accessories');

        $this->assertSame(
            ['accessories' => 'FIRST,SECOND'],
            $this->productsToProductAssociationsTransformer->transform(new ArrayCollection([$productAssociation])),
        );
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
