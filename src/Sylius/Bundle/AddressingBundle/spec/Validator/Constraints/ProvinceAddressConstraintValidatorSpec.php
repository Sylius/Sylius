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

namespace spec\Sylius\Bundle\AddressingBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\AddressingBundle\Checker\ProvinceAddressCheckerInterface;
use Sylius\Bundle\AddressingBundle\Validator\Constraints\ProvinceAddressConstraint;
use Sylius\Bundle\AddressingBundle\Validator\Constraints\ProvinceAddressConstraintValidator;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\Country;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\Province;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ProvinceAddressConstraintValidatorSpec extends ObjectBehavior
{
    function let(RepositoryInterface $countryRepository, RepositoryInterface $provinceRepository): void
    {
        $this->beConstructedWith($countryRepository, $provinceRepository);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ProvinceAddressConstraintValidator::class);
    }

    function it_throws_exception_if_the_value_is_not_an_address(Constraint $constraint): void
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('validate', [
            '',
            $constraint,
        ]);
    }

    function it_does_not_add_violation_because_a_violation_exists(
        ProvinceAddressCheckerInterface $provinceAddressChecker,
        AddressInterface $address,
        ProvinceAddressConstraint $constraint,
        ExecutionContextInterface $context
    ): void {
        $this->beConstructedWith($provinceAddressChecker);

        $this->initialize($context);

        $context->getPropertyPath()->willReturn('property_path');
        $context->getViolations()->willReturn(new \ArrayIterator([
            $this->createViolation('property_path'),
        ]));

        $provinceAddressChecker->isValid($address)->shouldNotBeCalled();
        $context->addViolation(Argument::any())->shouldNotBeCalled();

        $this->validate($address, $constraint);
    }

    function it_does_not_add_violation_because_a_violation_exists_when_address_is_the_root_object(
        ProvinceAddressCheckerInterface $provinceAddressChecker,
        AddressInterface $address,
        ProvinceAddressConstraint $constraint,
        ExecutionContextInterface $context
    ): void {
        $this->beConstructedWith($provinceAddressChecker);

        $this->initialize($context);

        $context->getPropertyPath()->willReturn('');
        $context->getViolations()->willReturn(new \ArrayIterator([
            $this->createViolation('property_path'),
        ]));

        $context->addViolation(Argument::any())->shouldNotBeCalled();
        $provinceAddressChecker->isValid($address)->shouldNotBeCalled();

        $this->validate($address, $constraint);
    }

    function it_adds_violation_because_address_has_no_province(
        RepositoryInterface $countryRepository,
        AddressInterface $address,
        Country $country,
        ProvinceAddressConstraint $constraint,
        ExecutionContextInterface $context
    ): void {
        $country->getCode()->willReturn('IE');
        $address->getCountryCode()->willReturn('IE');
        $countryRepository->findOneBy(['code' => 'IE'])->willReturn($country);

        $country->hasProvinces()->willReturn(true);
        $address->getProvinceCode()->willReturn(null);
        $this->initialize($context);

        $context->getPropertyPath()->willReturn('property_path');
        $context->getViolations()->willReturn(new \ArrayIterator([
            $this->createViolation('other_property_path'),
        ]));

        $context->addViolation(Argument::any())->shouldBeCalled();

        $this->validate($address, $constraint);
    }

    function it_adds_violation_because_address_province_does_not_belong_to_country(
        RepositoryInterface $countryRepository,
        RepositoryInterface $provinceRepository,
        AddressInterface $address,
        Country $country,
        Province $province,
        ProvinceAddressConstraint $constraint,
        ExecutionContextInterface $context
    ): void {
        $country->getCode()->willReturn('US');
        $address->getCountryCode()->willReturn('US');
        $countryRepository->findOneBy(['code' => 'US'])->willReturn($country);

        $country->hasProvinces()->willReturn(true);
        $address->getProvinceCode()->willReturn('US-AK');

        $province->getCode()->willReturn('US-AK');
        $provinceRepository->findOneBy(['code' => 'US-AK'])->willReturn($province);
        $country->hasProvince($province)->willReturn(false);

        $this->initialize($context);

        $context->getPropertyPath()->willReturn('property_path');
        $context->getViolations()->willReturn(new \ArrayIterator([
            $this->createViolation('other_property_path'),
        ]));

        $context->addViolation(Argument::any())->shouldBeCalled();

        $this->validate($address, $constraint);
    }

    function it_adds_violation_because_address_province_does_not_belong_to_country_without_provinces(
        RepositoryInterface $countryRepository,
        RepositoryInterface $provinceRepository,
        AddressInterface $address,
        Country $country,
        ProvinceAddressConstraint $constraint,
        ExecutionContextInterface $context
    ): void {
        $country->getCode()->willReturn('US');
        $address->getCountryCode()->willReturn('US');
        $countryRepository->findOneBy(['code' => 'US'])->willReturn($country);

        $country->hasProvinces()->willReturn(false);
        $address->getProvinceCode()->willReturn('US-AK');

        $provinceRepository->findOneBy(['code' => 'US-AK'])->shouldNotBeCalled();

        $this->initialize($context);

        $context->getPropertyPath()->willReturn('property_path');
        $context->getViolations()->willReturn(new \ArrayIterator([
            $this->createViolation('other_property_path'),
        ]));

        $context->addViolation(Argument::any())->shouldBeCalled();

        $this->validate($address, $constraint);
    }

    function it_does_not_add_a_violation_if_province_is_valid(
        RepositoryInterface $countryRepository,
        RepositoryInterface $provinceRepository,
        AddressInterface $address,
        Country $country,
        Province $province,
        ProvinceAddressConstraint $constraint,
        ExecutionContextInterface $context
    ): void {
        $country->getCode()->willReturn('US');
        $address->getCountryCode()->willReturn('US');
        $countryRepository->findOneBy(['code' => 'US'])->willReturn($country);

        $country->hasProvinces()->willReturn(true);
        $address->getProvinceCode()->willReturn('US-AK');

        $province->getCode()->willReturn('US-AK');
        $provinceRepository->findOneBy(['code' => 'US-AK'])->willReturn($province);
        $country->hasProvince($province)->willReturn(true);

        $this->initialize($context);

        $context->getPropertyPath()->willReturn('property_path');
        $context->getViolations()->willReturn(new \ArrayIterator([
            $this->createViolation('other_property_path'),
        ]));

        $context->addViolation(Argument::any())->shouldNotBeCalled();

        $this->validate($address, $constraint);
    }

    function it_adds_violation_because_address_has_invalid_province(
        ProvinceAddressCheckerInterface $provinceAddressChecker,
        AddressInterface $address,
        ProvinceAddressConstraint $constraint,
        ExecutionContextInterface $context
    ): void {
        $this->beConstructedWith($provinceAddressChecker);

        $provinceAddressChecker->isValid($address)->willReturn(false);

        $this->initialize($context);

        $context->getPropertyPath()->willReturn('property_path');
        $context->getViolations()->willReturn(new \ArrayIterator([
            $this->createViolation('other_property_path'),
        ]));

        $context->addViolation(Argument::any())->shouldBeCalled();

        $this->validate($address, $constraint);
    }

    function it_does_not_add_a_violation_because_address_has_valid_province(
        ProvinceAddressCheckerInterface $provinceAddressChecker,
        AddressInterface $address,
        ProvinceAddressConstraint $constraint,
        ExecutionContextInterface $context
    ): void {
        $this->beConstructedWith($provinceAddressChecker);

        $provinceAddressChecker->isValid($address)->willReturn(true);

        $this->initialize($context);

        $context->getPropertyPath()->willReturn('property_path');
        $context->getViolations()->willReturn(new \ArrayIterator([
            $this->createViolation('other_property_path'),
        ]));

        $context->addViolation(Argument::any())->shouldNotBeCalled();

        $this->validate($address, $constraint);
    }

    function it_cannot_be_constructed_with_object_different_then_address_province_checked_or_repositories_as_first_argument()
    {
        $this->beConstructedWith(new \stdClass());

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    function it_cannot_be_constructed_with_only_one_repository(RepositoryInterface $repository)
    {
        $this->beConstructedWith($repository);

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    private function createViolation($propertyPath)
    {
        return new ConstraintViolation('message', 'template', [], 'root', $propertyPath, 'invalidValue');
    }
}
