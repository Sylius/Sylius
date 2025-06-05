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

namespace Tests\Sylius\Bundle\ApiBundle\Validator\Constraints;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Validator\Constraints\SingleValueForProductVariantOption;
use Sylius\Bundle\ApiBundle\Validator\Constraints\SingleValueForProductVariantOptionValidator;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class SingleValueForProductVariantOptionValidatorTest extends TestCase
{
    private ExecutionContextInterface&MockObject $executionContext;

    private SingleValueForProductVariantOptionValidator $singleValueForProductVariantOptionValidator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->singleValueForProductVariantOptionValidator = new SingleValueForProductVariantOptionValidator($this->executionContext);
        $this->singleValueForProductVariantOptionValidator->initialize($this->executionContext);
    }

    public function testAConstraintValidator(): void
    {
        self::assertInstanceOf(ConstraintValidatorInterface::class, $this->singleValueForProductVariantOptionValidator);
    }

    public function testThrowsAnExceptionIfValueIsNotAProductVariant(): void
    {
        /** @var ExecutionContextInterface|MockObject $contextMock */
        $contextMock = $this->createMock(ExecutionContextInterface::class);
        $contextMock->expects(self::never())->method('buildViolation');
        self::expectException(\InvalidArgumentException::class);
        $this->singleValueForProductVariantOptionValidator->validate(new \stdClass(), new SingleValueForProductVariantOption());
    }

    public function testThrowsAnExceptionIfConstraintIsNotASingleValueForProductVariantOption(): void
    {
        /** @var ExecutionContextInterface|MockObject $contextMock */
        $contextMock = $this->createMock(ExecutionContextInterface::class);
        /** @var ProductVariantInterface|MockObject $variantMock */
        $variantMock = $this->createMock(ProductVariantInterface::class);
        /** @var Constraint|MockObject $constraintMock */
        $constraintMock = $this->createMock(Constraint::class);
        $contextMock->expects(self::never())->method('buildViolation');
        self::expectException(\InvalidArgumentException::class);
        $this->singleValueForProductVariantOptionValidator->validate($variantMock, $constraintMock);
    }

    public function testAddsViolationIfThereIsMoreThanOneOptionValueToASingleOption(): void
    {
        /** @var ProductVariantInterface|MockObject $variantMock */
        $variantMock = $this->createMock(ProductVariantInterface::class);
        /** @var ProductOptionValueInterface|MockObject $firstProductOptionValueMock */
        $firstProductOptionValueMock = $this->createMock(ProductOptionValueInterface::class);
        /** @var ProductOptionValueInterface|MockObject $secondProductOptionValueMock */
        $secondProductOptionValueMock = $this->createMock(ProductOptionValueInterface::class);
        $constraint = new SingleValueForProductVariantOption();
        $firstProductOptionValueMock->expects(self::once())->method('getOptionCode')->willReturn('OPTION');
        $secondProductOptionValueMock->expects(self::once())->method('getOptionCode')->willReturn('OPTION');
        $variantMock->expects(self::once())->method('getOptionValues')->willReturn(new ArrayCollection([
            $firstProductOptionValueMock,
            $secondProductOptionValueMock,
        ]));
        $this->executionContext->expects(self::once())->method('addViolation')->with('sylius.product_variant.option_values.single_value');
        $this->singleValueForProductVariantOptionValidator->validate($variantMock, $constraint);
    }

    public function testDoesNothingIfEachOptionHasOnlyOneOptionValue(): void
    {
        /** @var ProductVariantInterface|MockObject $variantMock */
        $variantMock = $this->createMock(ProductVariantInterface::class);
        /** @var ProductOptionValueInterface|MockObject $firstProductOptionValueMock */
        $firstProductOptionValueMock = $this->createMock(ProductOptionValueInterface::class);
        /** @var ProductOptionValueInterface|MockObject $secondProductOptionValueMock */
        $secondProductOptionValueMock = $this->createMock(ProductOptionValueInterface::class);
        $constraint = new SingleValueForProductVariantOption();
        $firstProductOptionValueMock->expects(self::once())->method('getOptionCode')->willReturn('OPTION');
        $secondProductOptionValueMock->expects(self::once())->method('getOptionCode')->willReturn('DIFFERENT_OPTION');
        $variantMock->expects(self::once())->method('getOptionValues')->willReturn(new ArrayCollection([
            $firstProductOptionValueMock,
            $secondProductOptionValueMock,
        ]));
        $this->executionContext->expects(self::never())->method('addViolation')->with($constraint->message);
        $this->singleValueForProductVariantOptionValidator->validate($variantMock, $constraint);
    }
}
