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

namespace spec\Sylius\Bundle\ApiBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\Checkout\CompleteOrder;
use Sylius\Bundle\ApiBundle\Command\Checkout\UpdateCart;
use Sylius\Bundle\ApiBundle\Validator\Constraints\CorrectOrderAddress;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class CorrectOrderAddressValidatorSpec extends ObjectBehavior
{
    function let(RepositoryInterface $countryRepository): void
    {
        $this->beConstructedWith($countryRepository);
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldImplement(ConstraintValidatorInterface::class);
    }

    function it_throws_an_exception_if_value_is_not_an_instance_of_address_order_command(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [new CompleteOrder(), new CorrectOrderAddress()])
        ;
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_adding_eligible_product_variant_to_cart(
        AddressInterface $billingAddress,
        AddressInterface $shippingAddress,
    ): void {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [
                new UpdateCart('john@doe.com', $billingAddress->getWrappedObject(), $shippingAddress->getWrappedObject()),
                new class() extends Constraint {
                },
            ])
        ;
    }

    function it_adds_violation_if_billing_address_has_incorrect_country_code(
        ExecutionContextInterface $executionContext,
        AddressInterface $billingAddress,
    ): void {
        $this->initialize($executionContext);

        $billingAddress->getCountryCode()->willReturn('united_russia');

        $executionContext
            ->addViolation('sylius.country.not_exist', ['%countryCode%' => 'united_russia'])
            ->shouldBeCalled()
        ;

        $this->validate(
            new UpdateCart('john@doe.com', $billingAddress->getWrappedObject()),
            new CorrectOrderAddress(),
        );
    }

    function it_adds_violation_if_billing_address_has_not_country_code(
        ExecutionContextInterface $executionContext,
        AddressInterface $billingAddress,
    ): void {
        $this->initialize($executionContext);

        $billingAddress->getCountryCode()->willReturn(null);

        $executionContext
            ->addViolation('sylius.address.without_country')
            ->shouldBeCalled()
        ;

        $this->validate(
            new UpdateCart('john@doe.com', $billingAddress->getWrappedObject()),
            new CorrectOrderAddress(),
        );
    }

    function it_adds_violation_if_shipping_address_has_incorrect_country_code(
        RepositoryInterface $countryRepository,
        ExecutionContextInterface $executionContext,
        AddressInterface $billingAddress,
        AddressInterface $shippingAddress,
        CountryInterface $usa,
    ): void {
        $this->initialize($executionContext);

        $billingAddress->getCountryCode()->willReturn('US');
        $shippingAddress->getCountryCode()->willReturn('united_russia');

        $countryRepository->findOneBy(['code' => 'US'])->willReturn($usa);
        $countryRepository->findOneBy(['code' => 'united_russia'])->willReturn(null);

        $executionContext
            ->addViolation('sylius.country.not_exist', ['%countryCode%' => 'united_russia'])
            ->shouldBeCalled()
        ;

        $this->validate(
            new UpdateCart('john@doe.com', $billingAddress->getWrappedObject(), $shippingAddress->getWrappedObject()),
            new CorrectOrderAddress(),
        );
    }

    function it_adds_violation_if_shipping_address_and_billing_address_have_incorrect_country_code(
        RepositoryInterface $countryRepository,
        ExecutionContextInterface $executionContext,
        AddressInterface $billingAddress,
        AddressInterface $shippingAddress,
    ): void {
        $this->initialize($executionContext);

        $billingAddress->getCountryCode()->willReturn('euroland');
        $shippingAddress->getCountryCode()->willReturn('united_russia');

        $countryRepository->findOneBy(['code' => 'euroland'])->willReturn(null);
        $countryRepository->findOneBy(['code' => 'united_russia'])->willReturn(null);

        $executionContext
            ->addViolation('sylius.country.not_exist', ['%countryCode%' => 'euroland'])
            ->shouldBeCalled()
        ;

        $executionContext
            ->addViolation('sylius.country.not_exist', ['%countryCode%' => 'united_russia'])
            ->shouldBeCalled()
        ;

        $this->validate(
            new UpdateCart('john@doe.com', $billingAddress->getWrappedObject(), $shippingAddress->getWrappedObject()),
            new CorrectOrderAddress(),
        );
    }
}
