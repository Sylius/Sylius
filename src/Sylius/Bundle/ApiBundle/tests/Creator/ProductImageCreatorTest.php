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

namespace Tests\Sylius\Bundle\ApiBundle\Creator;

use ApiPlatform\Metadata\IriConverterInterface;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Sylius\Bundle\ApiBundle\Creator\ProductImageCreator;
use Sylius\Bundle\ApiBundle\Exception\NoFileUploadedException;
use Sylius\Bundle\ApiBundle\Exception\ProductNotFoundException;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Sylius\Resource\Factory\FactoryInterface;

final class ProductImageCreatorTest extends TestCase
{
    private FactoryInterface&MockObject $productImageFactory;

    private MockObject&ProductRepositoryInterface $productRepository;

    private ImageUploaderInterface&MockObject $imageUploader;

    private IriConverterInterface&MockObject $iriConverter;

    private ProductImageCreator $productImageCreator;

    private MockObject&ProductInterface $product;

    private MockObject&ProductImageInterface $productImage;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productImageFactory = $this->createMock(FactoryInterface::class);
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->imageUploader = $this->createMock(ImageUploaderInterface::class);
        $this->iriConverter = $this->createMock(IriConverterInterface::class);
        $this->productImageCreator = new ProductImageCreator(
            $this->productImageFactory,
            $this->productRepository,
            $this->imageUploader,
            $this->iriConverter,
        );
        $this->product = $this->createMock(ProductInterface::class);
        $this->productImage = $this->createMock(ProductImageInterface::class);
    }

    public function testCreatesAProductImage(): void
    {
        /** @var ProductVariantInterface&MockObject $productVariant */
        $productVariant = $this->createMock(ProductVariantInterface::class);

        $file = new SplFileInfo(__FILE__);

        $this->productRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['code' => 'CODE'])
            ->willReturn($this->product);

        $this->iriConverter->expects(self::once())
            ->method('getResourceFromIri')
            ->with('/api/v2/product-variants/CODE')
            ->willReturn($productVariant);

        $this->productImageFactory->expects(self::once())
            ->method('createNew')
            ->willReturn($this->productImage);

        $this->productImage->expects(self::once())->method('setFile')->with($file);

        $this->productImage->expects(self::once())->method('setType')->with('banner');

        $this->productImage->expects(self::once())->method('addProductVariant')->with($productVariant);

        $this->product->expects(self::once())->method('addImage')->with($this->productImage);

        $this->imageUploader->expects(self::once())->method('upload')->with($this->productImage);

        self::assertSame($this->productImage, $this->productImageCreator
            ->create(
                'CODE',
                $file,
                'banner',
                ['productVariants' => ['/api/v2/product-variants/CODE']],
            ))
        ;
    }

    public function testThrowsAnExceptionIfProductIsNotFound(): void
    {
        $file = new SplFileInfo(__FILE__);

        $this->productRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['code' => 'CODE'])
            ->willReturn(null);

        $this->productImageFactory->expects(self::never())->method('createNew');

        $this->iriConverter->expects(self::never())->method('getResourceFromIri');

        $this->imageUploader->expects(self::never())->method('upload');

        self::expectException(ProductNotFoundException::class);

        $this->productImageCreator->create('CODE', $file, 'banner', []);
    }

    public function testThrowsAnExceptionIfThereIsNoUploadedFile(): void
    {
        $this->productRepository->expects(self::never())->method('findOneBy')->with(['code' => 'CODE']);

        $this->productImageFactory->expects(self::never())->method('createNew');

        $this->iriConverter->expects(self::never())->method('getResourceFromIri');

        $this->imageUploader->expects(self::never())->method('upload');

        self::expectException(NoFileUploadedException::class);

        $this->productImageCreator->create('CODE', null, 'banner', []);
    }

    public function testThrowsAnExceptionIfThereIsAnIriToDifferentResourceThanProductVariant(): void
    {
        $file = new SplFileInfo(__FILE__);

        $this->productRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['code' => 'CODE'])
            ->willReturn($this->product);

        $this->iriConverter->expects(self::once())
            ->method('getResourceFromIri')
            ->with('/api/v2/products/CODE')
            ->willReturn($this->product);

        $this->productImageFactory->expects(self::once())->method('createNew')->willReturn($this->productImage);

        $this->productImage->expects(self::once())->method('setFile')->with($file);

        $this->productImage->expects(self::once())->method('setType')->with('banner');

        $this->productImage->expects(self::never())->method('addProductVariant');

        $this->imageUploader->expects(self::never())->method('upload');

        self::expectException(InvalidArgumentException::class);

        $this->productImageCreator->create(
            'CODE',
            $file,
            'banner',
            ['productVariants' => ['/api/v2/products/CODE']],
        );
    }
}
