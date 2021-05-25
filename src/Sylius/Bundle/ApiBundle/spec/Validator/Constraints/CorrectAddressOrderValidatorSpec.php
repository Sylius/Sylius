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
use Sylius\Bundle\ApiBundle\Command\Cart\AddItemToCart;
use Sylius\Bundle\ApiBundle\Command\Checkout\AddressOrder;
use Sylius\Bundle\ApiBundle\Command\Checkout\CompleteOrder;
use Sylius\Bundle\ApiBundle\Validator\Constraints\AddingEligibleProductVariantToCart;
use Sylius\Bundle\ApiBundle\Validator\Constraints\CorrectAddressOrder;
use Sylius\Bundle\ApiBundle\Validator\Constraints\CorrectAddressOrderValidator;
use Sylius\Component\Core\Model\AddressInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class CorrectAddressOrderValidatorSpec extends ObjectBehavior
{
    function it_is_a_constraint_validator(): void
    {
        $this->shouldImplement(ConstraintValidatorInterface::class);
    }

    function it_throws_an_exception_if_value_is_not_an_instance_of_address_order_command(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [new CompleteOrder(), new AddingEligibleProductVariantToCart()]);
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_adding_eligible_product_variant_to_cart(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [
                new AddItemToCart('productCode', 'productVariantCode', 1),
                new class() extends Constraint {}
            ])
        ;
    }

    function it_adds_violation_if_billing_address_has_incorrect_country_code(
        ExecutionContextInterface $executionContext,
        AddressInterface $billingAddress
    ): void {
        $this->initialize($executionContext);

        $billingAddress->getCountryCode()->willReturn('united_russia');

        $executionContext
            ->addViolation('sylius.country.not_exist', ['%countryCode%' => 'united_russia'])
            ->shouldBeCalled()
        ;

        $this->validate(
            new AddressOrder('john@doe.com', $billingAddress->getWrappedObject()),
            new CorrectAddressOrder()
        );
    }

    function it_adds_violation_if_shipping_address_has_incorrect_country_code(
        ExecutionContextInterface $executionContext,
        AddressInterface $billingAddress,
        AddressInterface $shippingAddress
    ): void {
        $this->initialize($executionContext);

        $billingAddress->getCountryCode()->willReturn('US');
        $shippingAddress->getCountryCode()->willReturn('united_russia');

        $executionContext
            ->addViolation('sylius.country.not_exist', ['%countryCode%' => 'united_russia'])
            ->shouldBeCalled()
        ;

        $this->validate(
            new AddressOrder(
                'john@doe.com',
                $billingAddress->getWrappedObject(),
                $shippingAddress->getWrappedObject()
            ),
            new CorrectAddressOrder()
        );
    }

    function it_adds_violation_if_shipping_address_and_billing_address_have_incorrect_country_code(
        ExecutionContextInterface $executionContext,
        AddressInterface $billingAddress,
        AddressInterface $shippingAddress
    ): void {
        $this->initialize($executionContext);

        $billingAddress->getCountryCode()->willReturn('euroland');
        $shippingAddress->getCountryCode()->willReturn('united_russia');

        $executionContext
            ->addViolation('sylius.country.not_exist', ['%countryCode%' => 'euroland'])
            ->shouldBeCalled()
        ;

        $executionContext
            ->addViolation('sylius.country.not_exist', ['%countryCode%' => 'united_russia'])
            ->shouldBeCalled()
        ;

        $this->validate(
            new AddressOrder('john@doe.com', $billingAddress->getWrappedObject(), $shippingAddress->getWrappedObject()),
            new CorrectAddressOrder()
        );
    }
}
