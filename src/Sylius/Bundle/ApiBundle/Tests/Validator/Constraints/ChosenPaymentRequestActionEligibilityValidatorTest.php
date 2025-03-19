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

namespace Sylius\Bundle\ApiBundle\Tests\Validator\Constraints;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Command\Payment\AddPaymentRequest;
use Sylius\Bundle\ApiBundle\Validator\Constraints\ChosenPaymentRequestActionEligibility;
use Sylius\Bundle\ApiBundle\Validator\Constraints\ChosenPaymentRequestActionEligibilityValidator;
use Sylius\Bundle\PaymentBundle\CommandProvider\ServiceProviderAwareCommandProviderInterface;
use Sylius\Bundle\PaymentBundle\Provider\GatewayFactoryNameProviderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Payment\Model\GatewayConfigInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ChosenPaymentRequestActionEligibilityValidatorTest extends TestCase
{
    private MockObject|PaymentMethodRepositoryInterface $paymentMethodRepositoryMock;

    private MockObject|ServiceProviderAwareCommandProviderInterface $gatewayFactoryCommandProviderMock;

    private GatewayFactoryNameProviderInterface|MockObject $gatewayFactoryNameProviderMock;

    private ExecutionContextInterface|MockObject $executionContextMock;

    private ChosenPaymentRequestActionEligibilityValidator $chosenPaymentRequestActionEligibilityValidator;

    protected function setUp(): void
    {
        $this->paymentMethodRepositoryMock = $this->createMock(PaymentMethodRepositoryInterface::class);
        $this->gatewayFactoryCommandProviderMock = $this->createMock(ServiceProviderAwareCommandProviderInterface::class);
        $this->gatewayFactoryNameProviderMock = $this->createMock(GatewayFactoryNameProviderInterface::class);
        $this->executionContextMock = $this->createMock(ExecutionContextInterface::class);
        $this->chosenPaymentRequestActionEligibilityValidator = new ChosenPaymentRequestActionEligibilityValidator($this->paymentMethodRepositoryMock, $this->gatewayFactoryCommandProviderMock, $this->gatewayFactoryNameProviderMock);
        $this->chosenPaymentRequestActionEligibilityValidator->initialize($this->executionContextMock);
    }

    public function test_it_implements(): void
    {
        $this->assertInstanceOf(ConstraintValidatorInterface::class, $this->chosenPaymentRequestActionEligibilityValidator);
    }

    public function test_it_throws_an_exception_if_value_is_not_an_add_payment_request(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->chosenPaymentRequestActionEligibilityValidator->validate('', new ChosenPaymentRequestActionEligibility());
    }

    public function test_it_throws_an_exception_if_constraint_is_not_an_instance_of_chosen_payment_request_action_eligibility(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->chosenPaymentRequestActionEligibilityValidator->validate(
            new AddPaymentRequest('ORDER_TOKEN', 123, 'PAYMENT_METHOD_CODE'),
            new class() extends Constraint {
            },
        );
    }

    public function test_it_does_nothing_if_there_is_no_action_given(): void
    {
        $command = new AddPaymentRequest(
            orderTokenValue: 'ORDER_TOKEN',
            paymentId: 123,
            paymentMethodCode: 'PAYMENT_METHOD_CODE',
        );
        $this->paymentMethodRepositoryMock->expects($this->never())->method('findOneBy')->with(['code' => 'PAYMENT_METHOD_CODE']);
        $this->chosenPaymentRequestActionEligibilityValidator->validate($command, new ChosenPaymentRequestActionEligibility());
    }

    public function test_it_adds_violation_if_the_payment_method_does_not_exist(): void
    {
        $command = new AddPaymentRequest(
            orderTokenValue: 'ORDER_TOKEN',
            paymentId: 123,
            paymentMethodCode: 'PAYMENT_METHOD_CODE',
            action: 'capture',
        );
        $this->paymentMethodRepositoryMock->expects($this->once())->method('findOneBy')->with(['code' => 'PAYMENT_METHOD_CODE'])->willReturn(null);
        $this->executionContextMock->expects($this->once())->method('addViolation')->with('sylius.payment_method.not_exist', ['%code%' => 'PAYMENT_METHOD_CODE'])
        ;
        $this->chosenPaymentRequestActionEligibilityValidator->validate($command, new ChosenPaymentRequestActionEligibility());
    }

    public function test_it_adds_violation_if_there_is_no_gateway_config_on_the_payment_method(): void
    {
        /** @var PaymentMethodInterface|MockObject $paymentMethodMock */
        $paymentMethodMock = $this->createMock(PaymentMethodInterface::class);
        $command = new AddPaymentRequest(
            orderTokenValue: 'ORDER_TOKEN',
            paymentId: 123,
            paymentMethodCode: 'PAYMENT_METHOD_CODE',
            action: 'capture',
        );
        $this->paymentMethodRepositoryMock->expects($this->once())->method('findOneBy')->with(['code' => 'PAYMENT_METHOD_CODE'])->willReturn($paymentMethodMock);
        $paymentMethodMock->expects($this->once())->method('getGatewayConfig')->willReturn(null);
        $this->executionContextMock->expects($this->once())->method('addViolation')->with('sylius.payment_method.not_exist', ['%code%' => 'PAYMENT_METHOD_CODE'])
        ;
        $this->chosenPaymentRequestActionEligibilityValidator->validate($command, new ChosenPaymentRequestActionEligibility());
    }

    public function test_it_adds_violation_if_there_is_no_gateway_command_provider_available(): void
    {
        /** @var PaymentMethodInterface|MockObject $paymentMethodMock */
        $paymentMethodMock = $this->createMock(PaymentMethodInterface::class);
        /** @var GatewayConfigInterface|MockObject $gatewayConfigMock */
        $gatewayConfigMock = $this->createMock(GatewayConfigInterface::class);
        $command = new AddPaymentRequest(
            orderTokenValue: 'ORDER_TOKEN',
            paymentId: 123,
            paymentMethodCode: 'PAYMENT_METHOD_CODE',
            action: 'invalid_action',
        );
        $this->paymentMethodRepositoryMock->expects($this->once())->method('findOneBy')->with(['code' => 'PAYMENT_METHOD_CODE'])->willReturn($paymentMethodMock);
        $paymentMethodMock->expects($this->once())->method('getGatewayConfig')->willReturn($gatewayConfigMock);
        $factoryName = 'offline';
        $this->gatewayFactoryNameProviderMock->expects($this->once())->method('provide')->with($paymentMethodMock)->willReturn($factoryName);
        $this->gatewayFactoryCommandProviderMock->expects($this->once())->method('getCommandProvider')->with($factoryName)->willReturn(null);
        $this->executionContextMock->expects($this->once())->method('addViolation')->with('sylius.payment_request.action_not_available', [
            '%code%' => 'PAYMENT_METHOD_CODE',
            '%id%' => 123,
        ])
        ;
        $this->chosenPaymentRequestActionEligibilityValidator->validate($command, new ChosenPaymentRequestActionEligibility());
    }

    public function test_it_does_nothing_if_the_command_provider_is_not_an_action_command_provider(): void
    {
        /** @var PaymentMethodInterface|MockObject $paymentMethodMock */
        $paymentMethodMock = $this->createMock(PaymentMethodInterface::class);
        /** @var GatewayConfigInterface|MockObject $gatewayConfigMock */
        $gatewayConfigMock = $this->createMock(GatewayConfigInterface::class);
        /** @var \Sylius\Bundle\PaymentBundle\CommandProvider\PaymentRequestCommandProviderInterface|MockObject $commandProviderMock */
        $commandProviderMock = $this->createMock(\Sylius\Bundle\PaymentBundle\CommandProvider\PaymentRequestCommandProviderInterface::class);
        $command = new AddPaymentRequest(
            orderTokenValue: 'ORDER_TOKEN',
            paymentId: 123,
            paymentMethodCode: 'PAYMENT_METHOD_CODE',
            action: 'capture',
        );
        $this->paymentMethodRepositoryMock->expects($this->once())->method('findOneBy')->with(['code' => 'PAYMENT_METHOD_CODE'])->willReturn($paymentMethodMock);
        $paymentMethodMock->expects($this->once())->method('getGatewayConfig')->willReturn($gatewayConfigMock);
        $factoryName = 'offline';
        $this->gatewayFactoryNameProviderMock->expects($this->once())->method('provide')->with($paymentMethodMock)->willReturn($factoryName);
        $this->gatewayFactoryCommandProviderMock->expects($this->once())->method('getCommandProvider')->with($factoryName)->willReturn($commandProviderMock);
        $this->executionContextMock->expects($this->never())->method('addViolation')->with('sylius.payment_request.action_not_available', [
            '%code%' => 'PAYMENT_METHOD_CODE',
            '%id%' => 123,
        ])
        ;
        $this->chosenPaymentRequestActionEligibilityValidator->validate($command, new ChosenPaymentRequestActionEligibility());
    }

    public function test_it_does_nothing_if_payment_request_action_is_eligible(): void
    {
        /** @var PaymentMethodInterface|MockObject $paymentMethodMock */
        $paymentMethodMock = $this->createMock(PaymentMethodInterface::class);
        /** @var GatewayConfigInterface|MockObject $gatewayConfigMock */
        $gatewayConfigMock = $this->createMock(GatewayConfigInterface::class);
        /** @var ServiceProviderAwareCommandProviderInterface|MockObject $actionsCommandProviderMock */
        $actionsCommandProviderMock = $this->createMock(ServiceProviderAwareCommandProviderInterface::class);
        /** @var \Sylius\Bundle\PaymentBundle\CommandProvider\PaymentRequestCommandProviderInterface|MockObject $commandProviderMock */
        $commandProviderMock = $this->createMock(\Sylius\Bundle\PaymentBundle\CommandProvider\PaymentRequestCommandProviderInterface::class);
        $command = new AddPaymentRequest(
            orderTokenValue: 'ORDER_TOKEN',
            paymentId: 123,
            paymentMethodCode: 'PAYMENT_METHOD_CODE',
            action: 'capture',
        );
        $this->paymentMethodRepositoryMock->expects($this->once())->method('findOneBy')->with(['code' => 'PAYMENT_METHOD_CODE'])->willReturn($paymentMethodMock);
        $paymentMethodMock->expects($this->once())->method('getGatewayConfig')->willReturn($gatewayConfigMock);
        $factoryName = 'offline';
        $this->gatewayFactoryNameProviderMock->expects($this->once())->method('provide')->with($paymentMethodMock)->willReturn($factoryName);
        $this->gatewayFactoryCommandProviderMock->expects($this->once())->method('getCommandProvider')->with($factoryName)->willReturn($actionsCommandProviderMock);
        $actionsCommandProviderMock->expects($this->once())->method('getCommandProvider')->with('capture')->willReturn($commandProviderMock);
        $this->executionContextMock->expects($this->never())->method('addViolation')->with('sylius.payment_request.action_not_available', [
            '%code%' => 'PAYMENT_METHOD_CODE',
            '%id%' => 123,
        ])
        ;
        $this->chosenPaymentRequestActionEligibilityValidator->validate(
            $command,
            new ChosenPaymentRequestActionEligibility(),
        );
    }

    public function test_it_adds_violation_if_the_command_provider_does_not_provide_any_command(): void
    {
        /** @var PaymentMethodInterface|MockObject $paymentMethodMock */
        $paymentMethodMock = $this->createMock(PaymentMethodInterface::class);
        /** @var GatewayConfigInterface|MockObject $gatewayConfigMock */
        $gatewayConfigMock = $this->createMock(GatewayConfigInterface::class);
        /** @var ServiceProviderAwareCommandProviderInterface|MockObject $commandProviderMock */
        $commandProviderMock = $this->createMock(ServiceProviderAwareCommandProviderInterface::class);
        $command = new AddPaymentRequest(
            orderTokenValue: 'ORDER_TOKEN',
            paymentId: 123,
            paymentMethodCode: 'PAYMENT_METHOD_CODE',
            action: 'capture',
        );
        $this->paymentMethodRepositoryMock->expects($this->once())->method('findOneBy')->with(['code' => 'PAYMENT_METHOD_CODE'])->willReturn($paymentMethodMock);
        $paymentMethodMock->expects($this->once())->method('getGatewayConfig')->willReturn($gatewayConfigMock);
        $factoryName = 'offline';
        $this->gatewayFactoryNameProviderMock->expects($this->once())->method('provide')->with($paymentMethodMock)->willReturn($factoryName);
        $this->gatewayFactoryCommandProviderMock->expects($this->once())->method('getCommandProvider')->with($factoryName)->willReturn($commandProviderMock);
        $commandProviderMock->expects($this->once())->method('getCommandProvider')->with('capture')->willReturn(null);
        $this->executionContextMock->expects($this->once())->method('addViolation')->with('sylius.payment_request.action_not_available', [
            '%code%' => 'PAYMENT_METHOD_CODE',
            '%id%' => 123,
        ])
        ;
        $this->chosenPaymentRequestActionEligibilityValidator->validate($command, new ChosenPaymentRequestActionEligibility());
    }
}
