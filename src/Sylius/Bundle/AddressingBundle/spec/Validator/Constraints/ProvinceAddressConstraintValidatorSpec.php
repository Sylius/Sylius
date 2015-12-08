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
use Sylius\Component\Addressing\Model\Country;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class ProvinceAddressConstraintValidatorSpec extends ObjectBehavior
{
    function let(RepositoryInterface $repository)
    {
        $this->beConstructedWith($repository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Validator\Constraints\ProvinceAddressConstraintValidator');
    }

    function it_throws_exception_if_the_value_is_not_an_address(Constraint $constraint)
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('validate', array(
            '',
            $constraint
        ));
    }

    function it_does_not_add_violation_because_a_violation_exists(
        AddressInterface $address,
        ProvinceAddressConstraint $constraint,
        ExecutionContextInterface $context
    ) {
        $this->initialize($context);

        $context->getPropertyPath()->willReturn('property_path');
        $context->getViolations()->willReturn(new \ArrayIterator(array(
            $this->createViolation('property_path')
        )));

        $context->addViolation(Argument::any())->shouldNotBeCalled();

        $this->validate($address, $constraint);
    }

    function it_adds_violation_because_address_has_no_province(
        AddressInterface $address,
        Country $country,
        ProvinceAddressConstraint $constraint,
        ExecutionContextInterface $context,
        RepositoryInterface $repository
    ) {
        $country->getCode()->willReturn('IE');
        $address->getCountry()->willreturn('IE');
        $repository->findOneBy(array('code' => 'IE'))->willReturn($country);

        $country->hasProvinces()->willreturn(true);
        $address->getProvince()->willreturn(null);
        $this->initialize($context);

        $context->getPropertyPath()->willReturn('property_path');
        $context->getViolations()->willReturn(new \ArrayIterator(array(
            $this->createViolation('other_property_path')
        )));

        $context->addViolation(Argument::any())->shouldBeCalled();

        $this->validate($address, $constraint);
    }

    private function createViolation($propertyPath)
    {
        return new ConstraintViolation('message', 'template', array(), 'root', $propertyPath, 'invalidValue');
    }
}
