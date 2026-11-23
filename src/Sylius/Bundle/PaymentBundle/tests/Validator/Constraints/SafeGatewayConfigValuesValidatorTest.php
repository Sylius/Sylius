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

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\PaymentBundle\Validator\Constraints\SafeGatewayConfigValues;
use Sylius\Bundle\PaymentBundle\Validator\Constraints\SafeGatewayConfigValuesValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

#[AllowMockObjectsWithoutExpectations]
final class SafeGatewayConfigValuesValidatorTest extends TestCase
{
    private ExecutionContextInterface&MockObject $executionContext;

    private SafeGatewayConfigValuesValidator $safeGatewayConfigValuesValidator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->safeGatewayConfigValuesValidator = new SafeGatewayConfigValuesValidator();
        $this->safeGatewayConfigValuesValidator->initialize($this->executionContext);
    }

    public function testThrowsAnExceptionIfConstraintIsNotAnInstanceOfSafeGatewayConfigValues(): void
    {
        /** @var Constraint&MockObject $constraint */
        $constraint = $this->createMock(Constraint::class);

        self::expectException(UnexpectedTypeException::class);

        $this->safeGatewayConfigValuesValidator->validate([], $constraint);
    }

    public function testDoesNothingWhenTheValueIsNotAnArray(): void
    {
        $this->executionContext->expects(self::never())->method('buildViolation');

        $this->safeGatewayConfigValuesValidator->validate(null, new SafeGatewayConfigValues());
    }

    public function testDoesNotAddAViolationForAPlainScalarConfiguration(): void
    {
        $this->executionContext->expects(self::never())->method('buildViolation');

        $this->safeGatewayConfigValuesValidator->validate(
            ['sandbox' => true, 'publishable_key' => 'pk_test_123'],
            new SafeGatewayConfigValues(),
        );
    }

    public function testAddsAViolationForAValueReferencingAContainerParameter(): void
    {
        $this->expectForbiddenValueViolation();

        $this->safeGatewayConfigValuesValidator->validate(
            ['leaked' => '%kernel.secret%'],
            new SafeGatewayConfigValues(),
        );
    }

    public function testAddsAViolationForAValueReferencingAService(): void
    {
        $this->expectForbiddenValueViolation();

        $this->safeGatewayConfigValuesValidator->validate(
            ['injected' => '@security.token_storage'],
            new SafeGatewayConfigValues(),
        );
    }

    public function testAddsAViolationForACallableValue(): void
    {
        $this->expectForbiddenValueViolation();

        $this->safeGatewayConfigValuesValidator->validate(
            ['callback' => 'phpinfo'],
            new SafeGatewayConfigValues(),
        );
    }

    public function testAddsAViolationForACallableArrayValue(): void
    {
        $this->expectForbiddenValueViolation();

        $this->safeGatewayConfigValuesValidator->validate(
            ['callback' => [\DateTime::class, 'getLastErrors']],
            new SafeGatewayConfigValues(),
        );
    }

    public function testDoesNotAddAViolationForANonCallableArrayValue(): void
    {
        $this->executionContext->expects(self::never())->method('buildViolation');

        $this->safeGatewayConfigValuesValidator->validate(
            ['webhook_secret_keys' => ['whsec_first', 'whsec_second']],
            new SafeGatewayConfigValues(),
        );
    }

    private function expectForbiddenValueViolation(): void
    {
        /** @var ConstraintViolationBuilderInterface&MockObject $constraintViolationBuilder */
        $constraintViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $this->executionContext
            ->expects(self::once())
            ->method('buildViolation')
            ->with((new SafeGatewayConfigValues())->forbiddenValueMessage)
            ->willReturn($constraintViolationBuilder);

        $constraintViolationBuilder
            ->expects(self::once())
            ->method('addViolation');
    }
}
