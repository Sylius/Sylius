<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ApiBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Command\Checkout\ChooseShippingMethod;
use Sylius\Bundle\ApiBundle\Command\Checkout\CompleteOrder;
use Sylius\Bundle\ApiBundle\Validator\Constraints\ChosenShippingMethodEligibility;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ChosenShippingMethodEligibilityValidatorSpec extends ObjectBehavior
{
    function let(
        ShipmentRepositoryInterface $shipmentRepository,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShippingMethodsResolverInterface $shippingMethodsResolver,
        ExecutionContextInterface $executionContext
    ): void {
        $this->beConstructedWith($shipmentRepository, $shippingMethodRepository, $shippingMethodsResolver);

        $this->initialize($executionContext);
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldImplement(ConstraintValidatorInterface::class);
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_chosen_shipping_method_eligibility(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [new ChooseShippingMethod('SHIPPING_METHOD_CODE'), new class() extends Constraint {
            }])
        ;
    }

    function it_throws_an_exception_if_value_is_not_an_instance_of_choose_shipping_method_command(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [new CompleteOrder(), new ChosenShippingMethodEligibility()])
        ;
    }

    function it_adds_violation_if_chosen_shipping_method_does_not_match_supported_methods(
        ShipmentRepositoryInterface $shipmentRepository,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShippingMethodsResolverInterface $shippingMethodsResolver,
        ExecutionContextInterface $executionContext,
        ShippingMethodInterface $shippingMethod,
        ShippingMethodInterface $differentShippingMethod,
        ShipmentInterface $shipment,
        OrderInterface $order,
        AddressInterface $shippingAddress
    ): void {
        $command = new ChooseShippingMethod('SHIPPING_METHOD_CODE');
        $command->setOrderTokenValue('ORDER_TOKEN');
        $command->setSubresourceId('123');

        $shippingMethodRepository->findOneBy(['code' => 'SHIPPING_METHOD_CODE'])->willReturn($shippingMethod);
        $shippingMethod->getName()->willReturn('DHL');

        $shipmentRepository->find('123')->willReturn($shipment);

        $shipment->getOrder()->willReturn($order);

        $order->getShippingAddress()->willReturn($shippingAddress);

        $shippingMethodsResolver->getSupportedMethods($shipment)->willReturn([$differentShippingMethod]);

        $executionContext
            ->addViolation('sylius.shipping_method.not_available', ['%name%' => 'DHL'])
            ->shouldBeCalled()
        ;

        $this->validate($command, new ChosenShippingMethodEligibility());
    }

    function it_does_not_add_violation_if_chosen_shipping_method_matches_supported_methods(
        ShipmentRepositoryInterface $shipmentRepository,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShippingMethodsResolverInterface $shippingMethodsResolver,
        ExecutionContextInterface $executionContext,
        ShippingMethodInterface $shippingMethod,
        ShipmentInterface $shipment,
        OrderInterface $order,
        AddressInterface $shippingAddress
    ): void {
        $command = new ChooseShippingMethod('SHIPPING_METHOD_CODE');
        $command->setOrderTokenValue('ORDER_TOKEN');
        $command->setSubresourceId('123');

        $shippingMethodRepository->findOneBy(['code' => 'SHIPPING_METHOD_CODE'])->willReturn($shippingMethod);

        $shipmentRepository->find('123')->willReturn($shipment);


        $shipment->getOrder()->willReturn($order);

        $order->getShippingAddress()->willReturn($shippingAddress);

        $shippingMethodsResolver->getSupportedMethods($shipment)->willReturn([$shippingMethod]);

        $executionContext
            ->addViolation('sylius.shipping_method.not_available', Argument::any())
            ->shouldNotBeCalled()
        ;

        $this->validate($command, new ChosenShippingMethodEligibility());
    }

    function it_adds_a_violation_if_given_shipping_method_is_null(
        ShipmentRepositoryInterface $shipmentRepository,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ExecutionContextInterface $executionContext
    ): void {
        $command = new ChooseShippingMethod('SHIPPING_METHOD_CODE');
        $command->setOrderTokenValue('ORDER_TOKEN');
        $command->setSubresourceId('123');

        $shippingMethodRepository->findOneBy(['code' => 'SHIPPING_METHOD_CODE'])->willReturn(null);

        $shipmentRepository->find('123')->shouldNotBeCalled();
        $executionContext
            ->addViolation('sylius.shipping_method.not_available', Argument::any())
            ->shouldNotBeCalled()
        ;
        $executionContext
            ->addViolation('sylius.shipping_method.not_found', ['%code%' => 'SHIPPING_METHOD_CODE'])
            ->shouldBeCalled()
        ;

        $this->validate($command, new ChosenShippingMethodEligibility());
    }

    function it_adds_violation_if_order_is_not_addressed(
        ShipmentRepositoryInterface $shipmentRepository,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShippingMethodsResolverInterface $shippingMethodsResolver,
        ExecutionContextInterface $executionContext,
        ShippingMethodInterface $shippingMethod,
        ShipmentInterface $shipment,
        OrderInterface $order,
        AddressInterface $shippingAddress
    ): void {
        $command = new ChooseShippingMethod('SHIPPING_METHOD_CODE');
        $command->setOrderTokenValue('ORDER_TOKEN');
        $command->setSubresourceId('123');

        $shippingMethodRepository->findOneBy(['code' => 'SHIPPING_METHOD_CODE'])->willReturn($shippingMethod);

        $shipmentRepository->find('123')->willReturn($shipment);

        $shipment->getOrder()->willReturn($order);

        $order->getShippingAddress()->willReturn(null);

        $shippingMethodsResolver->getSupportedMethods($shipment)->willReturn([$shippingMethod]);

        $executionContext
            ->addViolation('sylius.shipping_method.shipping_address_not_found')
            ->shouldBeCalled()
        ;

        $this->validate($command, new ChosenShippingMethodEligibility());
    }

    function it_throws_an_exception_if_given_shipmnent_is_null(
        ShipmentRepositoryInterface $shipmentRepository,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ExecutionContextInterface $executionContext,
        ShippingMethodInterface $shippingMethod
    ): void {
        $command = new ChooseShippingMethod('SHIPPING_METHOD_CODE');
        $command->setOrderTokenValue('ORDER_TOKEN');
        $command->setSubresourceId('123');

        $shippingMethodRepository->findOneBy(['code' => 'SHIPPING_METHOD_CODE'])->willReturn($shippingMethod);

        $shipmentRepository->find('123')->willReturn(null);
        $executionContext
            ->addViolation('sylius.shipping_method.not_available', Argument::any())
            ->shouldNotBeCalled()
        ;

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [$command, new ChosenShippingMethodEligibility()])
        ;
    }
}
