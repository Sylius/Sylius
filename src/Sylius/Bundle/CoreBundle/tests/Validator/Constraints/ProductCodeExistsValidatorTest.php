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

namespace Tests\Sylius\Bundle\CoreBundle\Validator\Constraints;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Validator\Constraints\ProductCodeExists;
use Sylius\Bundle\CoreBundle\Validator\Constraints\ProductCodeExistsValidator;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class ProductCodeExistsValidatorTest extends TestCase
{
    private MockObject&ProductRepositoryInterface $productRepository;

    private ExecutionContextInterface&MockObject $context;

    private ProductCodeExistsValidator $validator;

    protected function setUp(): void
    {
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->context = $this->createMock(ExecutionContextInterface::class);

        $this->validator = new ProductCodeExistsValidator($this->productRepository);
        $this->validator->initialize($this->context);
    }

    public function testItThrowsExceptionIfConstraintIsNotProductCodeExists(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->validator->validate('product_code', $this->createMock(Constraint::class));
    }

    public function testItDoesNothingIfValueIsEmpty(): void
    {
        $this->productRepository->expects($this->never())->method('findOneByCode');
        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate('', new ProductCodeExists());
    }

    public function testItDoesNothingIfProductExists(): void
    {
        $product = $this->createMock(ProductInterface::class);

        $this->productRepository
            ->expects($this->once())
            ->method('findOneByCode')
            ->with('product_code')
            ->willReturn($product)
        ;

        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate('product_code', new ProductCodeExists());
    }

    public function testItAddsViolationIfProductDoesNotExist(): void
    {
        $constraint = new ProductCodeExists();
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $this->productRepository->method('findOneByCode')->with('product_code')->willReturn(null);

        $violationBuilder
            ->expects($this->once())
            ->method('setParameter')
            ->with('{{ code }}', 'product_code')
            ->willReturn($violationBuilder)
        ;

        $violationBuilder->expects($this->once())->method('addViolation');

        $this->context
            ->expects($this->once())
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($violationBuilder)
        ;

        $this->validator->validate('product_code', $constraint);
    }
}
