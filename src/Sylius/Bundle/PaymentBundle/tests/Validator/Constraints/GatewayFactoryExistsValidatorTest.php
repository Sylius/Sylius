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

namespace Tests\Sylius\Bundle\PaymentBundle\Validator\Constraints;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\PaymentBundle\Validator\Constraints\GatewayFactoryExists;
use Sylius\Bundle\PaymentBundle\Validator\Constraints\GatewayFactoryExistsValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class GatewayFactoryExistsValidatorTest extends TestCase
{
    private ExecutionContextInterface&MockObject $executionContext;

    private GatewayFactoryExistsValidator $gatewayFactoryExistsValidator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->gatewayFactoryExistsValidator = new GatewayFactoryExistsValidator(
            [
                'paypal' => 'sylius.payum_gateway_factory.paypal',
                'stripe_checkout' => 'sylius.payum_gateway_factory.stripe_checkout'],
        );
        $this->gatewayFactoryExistsValidator->initialize($this->executionContext);
    }

    public function testThrowsAnExceptionIfConstraintIsNotAnInstanceOfGatewayFactoryExists(): void
    {
        /** @var Constraint&MockObject $constraint */
        $constraint = $this->createMock(Constraint::class);

        self::expectException(UnexpectedTypeException::class);

        $this->gatewayFactoryExistsValidator->validate('some_gateway', $constraint);
    }

    public function testAddsViolationToGatewayConfigurationWithWrongName(): void
    {
        /** @var ConstraintViolationBuilderInterface&MockObject $constraintViolationBuilder */
        $constraintViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $this->executionContext
            ->expects(self::once())
            ->method('buildViolation')
            ->with((new GatewayFactoryExists())->invalidGatewayFactory)
            ->willReturn($constraintViolationBuilder);

        $constraintViolationBuilder
            ->expects(self::once())
            ->method('setParameter')
            ->with('{{ available_factories }}', 'paypal, stripe_checkout')
            ->willReturn($constraintViolationBuilder);

        $constraintViolationBuilder
            ->expects(self::once())
            ->method('addViolation');

        $this->gatewayFactoryExistsValidator->validate('wrong_factory', new GatewayFactoryExists());
    }

    public function testDoesNotAddViolationToGatewayConfigurationWithCorrectName(): void
    {
        $this->executionContext->expects(self::never())->method('buildViolation')->with(self::any());

        $this->gatewayFactoryExistsValidator->validate('paypal', new GatewayFactoryExists());
    }
}
