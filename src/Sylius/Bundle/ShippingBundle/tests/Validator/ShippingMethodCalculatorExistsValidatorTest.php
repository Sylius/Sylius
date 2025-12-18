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

namespace Tests\Sylius\Bundle\ShippingBundle\Validator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ShippingBundle\Validator\Constraint\ShippingMethodCalculatorExists;
use Sylius\Bundle\ShippingBundle\Validator\ShippingMethodCalculatorExistsValidator;
use Sylius\Component\Shipping\Model\ShippingMethodRuleInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class ShippingMethodCalculatorExistsValidatorTest extends TestCase
{
    private ExecutionContextInterface&MockObject $executionContext;

    private ShippingMethodCalculatorExistsValidator $shippingMethodCalculatorExistsValidator;

    protected function setUp(): void
    {
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->shippingMethodCalculatorExistsValidator = new ShippingMethodCalculatorExistsValidator([
            'flat_rate' => 'sylius.form.shipping_calculator.flat_rate_configuration.label',
            'per_unit_rate' => 'sylius.form.shipping_calculator.per_unit_rate_configuration.label',
        ]);
        $this->shippingMethodCalculatorExistsValidator->initialize($this->executionContext);
    }

    public function testThrowsAnExceptionIfConstraintIsNotAnInstanceOfShippingMethodCalculatorExists(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        /** @var Constraint&MockObject $constraint */
        $constraint = $this->createMock(Constraint::class);
        /** @var ShippingMethodRuleInterface&MockObject $shippingMethodRule */
        $shippingMethodRule = $this->createMock(ShippingMethodRuleInterface::class);

        $this->shippingMethodCalculatorExistsValidator->validate($shippingMethodRule, $constraint);
    }

    public function testAddsViolationToWrongShippingMethodCalculator(): void
    {
        /** @var ConstraintViolationBuilderInterface&MockObject $constraintViolationBuilder */
        $constraintViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $this->executionContext
            ->expects($this->once())->method('buildViolation')
            ->with((new ShippingMethodCalculatorExists())->invalidShippingCalculator)
            ->willReturn($constraintViolationBuilder)
        ;
        $constraintViolationBuilder->expects($this->once())->method('setParameter')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->expects($this->once())->method('addViolation');

        $this->shippingMethodCalculatorExistsValidator->validate('wrong_calculator', new ShippingMethodCalculatorExists());
    }

    public function testDoesNotAddViolationToCorrectShippingMethodCalculator(): void
    {
        $this->executionContext->expects($this->never())->method('buildViolation')->with($this->any());

        $this->shippingMethodCalculatorExistsValidator->validate('flat_rate', new ShippingMethodCalculatorExists());
    }
}
