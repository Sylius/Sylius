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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Sylius\Bundle\CoreBundle\Form\DataTransformer\ProductTaxonToTaxonTransformer;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class ProductTaxonToTaxonTransformerTest extends TestCase
{
    private FactoryInterface&MockObject $productTaxonFactory;

    private MockObject&RepositoryInterface $productTaxonRepository;

    private MockObject&ProductInterface $product;

    private ProductTaxonToTaxonTransformer $productTaxonToTaxonTransformer;

    protected function setUp(): void
    {
        $this->productTaxonFactory = $this->createMock(FactoryInterface::class);
        $this->productTaxonRepository = $this->createMock(RepositoryInterface::class);
        $this->product = $this->createMock(ProductInterface::class);
        $this->productTaxonToTaxonTransformer = new ProductTaxonToTaxonTransformer(
            $this->productTaxonFactory,
            $this->productTaxonRepository,
            $this->product,
        );
    }

    public function testImplementsDataTransformerInterface(): void
    {
        $this->assertInstanceOf(DataTransformerInterface::class, $this->productTaxonToTaxonTransformer);
    }

    public function testTransformsProductTaxonToTaxon(): void
    {
        $productTaxon = $this->createMock(ProductTaxonInterface::class);
        $taxon = $this->createMock(TaxonInterface::class);

        $productTaxon->expects($this->once())->method('getTaxon')->willReturn($taxon);

        $this->assertSame($taxon, $this->productTaxonToTaxonTransformer->transform($productTaxon));
    }

    public function testReturnsNullDuringTransform(): void
    {
        $this->assertNull($this->productTaxonToTaxonTransformer->transform(null));
    }

    public function testTransformsTaxonToNewProductTaxon(): void
    {
        $productTaxon = $this->createMock(ProductTaxonInterface::class);
        $taxon = $this->createMock(TaxonInterface::class);

        $this->productTaxonRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['taxon' => $taxon, 'product' => $this->product])
            ->willReturn(null)
        ;

        $this->productTaxonFactory->expects($this->once())->method('createNew')->willReturn($productTaxon);
        $productTaxon->expects($this->once())->method('setTaxon')->with($taxon);
        $productTaxon->expects($this->once())->method('setProduct')->with($this->product);

        $this->assertSame($productTaxon, $this->productTaxonToTaxonTransformer->reverseTransform($taxon));
    }

    public function testTransformsTaxonToExistingProductTaxon(): void
    {
        $productTaxon = $this->createMock(ProductTaxonInterface::class);
        $taxon = $this->createMock(TaxonInterface::class);

        $this->productTaxonRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['taxon' => $taxon, 'product' => $this->product])
            ->willReturn($productTaxon)
        ;

        $this->assertSame($productTaxon, $this->productTaxonToTaxonTransformer->reverseTransform($taxon));
    }

    public function testReturnsNullDuringReverseTransform(): void
    {
        $this->assertNull($this->productTaxonToTaxonTransformer->reverseTransform(null));
    }

    public function testThrowsTransformationFailedExceptionDuringTransforms(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectException(TransformationFailedException::class);

        $this->productTaxonToTaxonTransformer->reverseTransform(new stdClass());

        $this->expectException(TransformationFailedException::class);

        $this->productTaxonToTaxonTransformer->reverseTransform(new stdClass());
    }
}
