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

namespace Tests\Sylius\Bundle\ApiBundle\StateProcessor\Admin\ProductImage;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Validator\Exception\ValidationException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Creator\ImageCreatorInterface;
use Sylius\Bundle\ApiBundle\StateProcessor\Admin\ProductImage\PersistProcessor;
use Sylius\Component\Core\Model\ProductImageInterface;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PersistProcessorTest extends TestCase
{
    private MockObject&ProcessorInterface $processor;

    private ImageCreatorInterface&MockObject $productImageCreator;

    private MockObject&ValidatorInterface $validator;

    private PersistProcessor $persistProcessor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->processor = $this->createMock(ProcessorInterface::class);
        $this->productImageCreator = $this->createMock(ImageCreatorInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->persistProcessor = new PersistProcessor($this->processor, $this->productImageCreator, $this->validator);
    }

    public function testImplementsProcessorInterface(): void
    {
        self::assertInstanceOf(ProcessorInterface::class, $this->persistProcessor);
    }

    public function testCreatesAndProcessesAProductImage(): void
    {
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);
        /** @var ParameterBag|MockObject $attributesMock */
        $attributesMock = $this->createMock(ParameterBag::class);
        /** @var FileBag|MockObject $filesMock */
        $filesMock = $this->createMock(FileBag::class);
        /** @var ProductImageInterface|MockObject $productImageMock */
        $productImageMock = $this->createMock(ProductImageInterface::class);
        /** @var ConstraintViolationListInterface|MockObject $constraintViolationListMock */
        $constraintViolationListMock = $this->createMock(ConstraintViolationListInterface::class);

        $operation = new Post(validationContext: ['groups' => ['sylius']]);

        $attributesMock->expects(self::once())->method('get')
            ->with('code', '')
            ->willReturn('code');

        $requestMock->attributes = $attributesMock;

        $file = new \SplFileInfo(__FILE__);

        $filesMock->expects(self::once())->method('get')->with('file')->willReturn($file);

        $requestMock->files = $filesMock;

        $requestParams = new InputBag([
            'type' => 'type',
            'productVariants' => ['/api/v2/admin/product-variants/MUG'],
        ]);

        $requestMock->request = $requestParams;

        $this->productImageCreator->expects(self::once())
            ->method('create')
            ->with('code', $file, 'type', ['productVariants' => ['/api/v2/admin/product-variants/MUG']])
            ->willReturn($productImageMock);

        $this->validator->expects(self::once())
            ->method('validate')
            ->with($productImageMock, null, ['sylius'])
            ->willReturn($constraintViolationListMock);

        $constraintViolationListMock->expects(self::once())->method('count')->willReturn(0);

        $this->processor->expects(self::once())
            ->method('process')
            ->with($productImageMock, $operation, [], ['request' => $requestMock]);

        $this->persistProcessor->process(null, $operation, [], ['request' => $requestMock]);
    }

    public function testThrowsAValidationExceptionIfACreatedProductImageIsNotValid(): void
    {
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);
        /** @var ParameterBag|MockObject $attributesMock */
        $attributesMock = $this->createMock(ParameterBag::class);
        /** @var FileBag|MockObject $filesMock */
        $filesMock = $this->createMock(FileBag::class);
        /** @var ProductImageInterface|MockObject $productImageMock */
        $productImageMock = $this->createMock(ProductImageInterface::class);
        /** @var ConstraintViolationListInterface|MockObject $constraintViolationListMock */
        $constraintViolationListMock = $this->createMock(ConstraintViolationListInterface::class);
        /** @var ConstraintViolationInterface|MockObject $constraintViolationMock */
        $constraintViolationMock = $this->createMock(ConstraintViolationInterface::class);

        $operation = new Post(validationContext: ['groups' => ['sylius']]);

        $attributesMock->expects(self::once())
            ->method('get')
            ->with('code', '')
            ->willReturn('code');

        $requestMock->attributes = $attributesMock;

        $file = new \SplFileInfo(__FILE__);

        $filesMock->expects(self::once())->method('get')->with('file')->willReturn($file);

        $requestMock->files = $filesMock;

        $requestParams = new InputBag([
            'type' => 'type',
            'productVariants' => ['/api/v2/admin/product-variants/MUG'],
        ]);

        $requestMock->request = $requestParams;

        $this->productImageCreator->expects(self::once())
            ->method('create')
            ->with('code', $file, 'type', ['productVariants' => ['/api/v2/admin/product-variants/MUG']])
            ->willReturn($productImageMock);

        $this->validator->expects(self::once())
            ->method('validate')
            ->with($productImageMock, null, ['sylius'])
            ->willReturn($constraintViolationListMock);

        $constraintViolationListMock->expects(self::once())->method('count')->willReturn(1);

        $constraintViolationListMock->expects(self::once())->method('rewind');

        $constraintViolationListMock->expects(self::once())->method('valid')->willReturn(false);

        $constraintViolationListMock->method('current')->willReturn($constraintViolationMock);

        $constraintViolationMock->method('getPropertyPath')->willReturn('productVariants');

        $constraintViolationMock->method('getMessage')->willReturn('message');

        $this->processor->expects(self::never())
            ->method('process')
            ->with($productImageMock, $operation, [], ['request' => $requestMock]);

        $this->expectException(ValidationException::class);

        $this->persistProcessor->process($productImageMock, $operation, [], ['request' => $requestMock]);
    }

    public function testThrowsAnExceptionForDeleteOperation(): void
    {
        $deleteOperation = new Delete();
        /** @var ProductImageInterface|MockObject $productImageMock */
        $productImageMock = $this->createMock(ProductImageInterface::class);

        self::expectException(\InvalidArgumentException::class);

        $this->persistProcessor->process($productImageMock, $deleteOperation, [], []);
    }
}
