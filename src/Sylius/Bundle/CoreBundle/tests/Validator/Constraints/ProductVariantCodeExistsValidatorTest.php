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
use Sylius\Bundle\CoreBundle\Validator\Constraints\ProductVariantCodeExists;
use Sylius\Bundle\CoreBundle\Validator\Constraints\ProductVariantCodeExistsValidator;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class ProductVariantCodeExistsValidatorTest extends TestCase
{
    private MockObject&ProductVariantRepositoryInterface $productVariantRepository;

    private ExecutionContextInterface&MockObject $context;

    private ProductVariantCodeExistsValidator $validator;

    protected function setUp(): void
    {
        $this->productVariantRepository = $this->createMock(ProductVariantRepositoryInterface::class);
        $this->context = $this->createMock(ExecutionContextInterface::class);

        $this->validator = new ProductVariantCodeExistsValidator($this->productVariantRepository);
        $this->validator->initialize($this->context);
    }

    public function testItIsAConstraintValidator(): void
    {
        $this->assertInstanceOf(ConstraintValidator::class, $this->validator);
    }

    public function testItThrowsExceptionWhenConstraintIsInvalid(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->validator->validate('variant_code', $this->createMock(Constraint::class));
    }

    public function testItDoesNothingWhenValueIsNull(): void
    {
        $this->productVariantRepository->expects($this->never())->method('findOneBy');
        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate(null, new ProductVariantCodeExists());
    }

    public function testItDoesNothingWhenValueIsEmptyString(): void
    {
        $this->productVariantRepository->expects($this->never())->method('findOneBy');
        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate('', new ProductVariantCodeExists());
    }

    public function testItDoesNothingWhenVariantExists(): void
    {
        $variant = $this->createMock(ProductVariantInterface::class);

        $this->productVariantRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => 'test'])
            ->willReturn($variant)
        ;

        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate('test', new ProductVariantCodeExists());
    }

    public function testItAddsViolationWhenVariantDoesNotExist(): void
    {
        $constraint = new ProductVariantCodeExists();
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $this->productVariantRepository
            ->method('findOneBy')
            ->with(['code' => 'test'])
            ->willReturn(null)
        ;

        $this->context
            ->expects($this->once())
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($violationBuilder)
        ;

        $violationBuilder
            ->expects($this->once())
            ->method('setParameter')
            ->with('{{ code }}', 'test')
            ->willReturn($violationBuilder)
        ;

        $violationBuilder->expects($this->once())->method('addViolation');

        $this->validator->validate('test', $constraint);
    }
}
