<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AddressingBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\AddressingBundle\Validator\Constraints\ProvinceAddressConstraint;
use Sylius\Component\Addressing\Model\AddressInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class ShippableAddressConstraintValidatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Validator\Constraints\ShippableAddressConstraintValidator');
    }

    function it_throws_exception_if_the_value_is_not_an_address(Constraint $constraint)
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('validate', [
            '',
            $constraint,
        ]);
    }

    function it_does_not_add_violation(
        AddressInterface $address,
        ProvinceAddressConstraint $constraint,
        ExecutionContextInterface $context
    ) {
        $this->initialize($context);

        $context->getPropertyPath()->shouldBeCalled()->willReturn('property_path');
        $context->getViolations()->shouldBeCalled()->willReturn(new \ArrayIterator(
            $this->createViolation('property_path')
        ));

        $context->addViolation(Argument::any())->shouldNotBeCalled();

        $this->validate($address, $constraint);
    }

    function it_adds_violation_because_address_is_shippable(
        AddressInterface $address,
        ProvinceAddressConstraint $constraint,
        ExecutionContextInterface $context
    ) {
        $this->initialize($context);

        $context->getPropertyPath()->shouldBeCalled()->willReturn('property_path');
        $context->getViolations()->shouldBeCalled()->willReturn(new \ArrayIterator([
            $this->createViolation('other_property_path'),
        ]));

        $context->addViolation(Argument::any())->shouldNotBeCalled();

        $this->validate($address, $constraint);
    }

    private function createViolation($propertyPath)
    {
        return new ConstraintViolation('message', 'template', [], 'root', $propertyPath, 'invalidValue');
    }
}
