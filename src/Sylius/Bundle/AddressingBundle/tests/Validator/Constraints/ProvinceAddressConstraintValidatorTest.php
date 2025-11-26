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

namespace Tests\Sylius\Bundle\AddressingBundle\Validator\Constraints;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AddressingBundle\Validator\Constraints\ProvinceAddressConstraint;
use Sylius\Bundle\AddressingBundle\Validator\Constraints\ProvinceAddressConstraintValidator;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\Country;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\Province;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class ProvinceAddressConstraintValidatorTest extends TestCase
{
    /** @var RepositoryInterface<CountryInterface>&MockObject */
    private MockObject&RepositoryInterface $countryRepository;

    /** @var RepositoryInterface<ProvinceInterface>&MockObject */
    private MockObject&RepositoryInterface $provinceRepository;

    private AddressInterface&MockObject $address;

    private MockObject&ProvinceAddressConstraint $constraint;

    private ExecutionContextInterface&MockObject $context;

    private ProvinceAddressConstraintValidator $provinceAddressConstraintValidator;

    protected function setUp(): void
    {
        $this->countryRepository = $this->createMock(RepositoryInterface::class);
        $this->provinceRepository = $this->createMock(RepositoryInterface::class);
        $this->address = $this->createMock(AddressInterface::class);
        $this->constraint = $this->createMock(ProvinceAddressConstraint::class);
        $this->context = $this->createMock(ExecutionContextInterface::class);

        $this->provinceAddressConstraintValidator = new ProvinceAddressConstraintValidator(
            $this->countryRepository,
            $this->provinceRepository,
        );
    }

    public function testThrowsExceptionIfTheValueIsNotAnAddress(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        /** @var Constraint&MockObject $constraint */
        $constraint = $this->createMock(Constraint::class);

        $this->provinceAddressConstraintValidator->validate('', $constraint);
    }

    public function testDoesNotAddViolationBecauseAViolationExists(): void
    {
        $this->countryRepository->expects($this->never())->method('findOneBy');
        $this->provinceAddressConstraintValidator->initialize($this->context);
        $this->context->expects($this->once())->method('getPropertyPath')->willReturn('property_path');
        $this->context->expects($this->once())->method('getViolations')->willReturn(new ConstraintViolationList([
            new ConstraintViolation('message', 'template', [], 'root', 'property_path', 'invalidValue'),
        ]));
        $this->context->expects($this->never())->method('addViolation');

        $this->provinceAddressConstraintValidator->validate($this->address, $this->constraint);
    }

    public function testDoesNotAddViolationBecauseAViolationExistsWhenAddressIsTheRootObject(): void
    {
        $this->countryRepository->expects($this->never())->method('findOneBy');
        $this->provinceAddressConstraintValidator->initialize($this->context);
        $this->context->expects($this->once())->method('getPropertyPath')->willReturn('');
        $this->context->expects($this->once())->method('getViolations')->willReturn(new ConstraintViolationList([
            new ConstraintViolation('message', 'template', [], 'root', 'property_path', 'invalidValue'),
        ]));
        $this->context->expects($this->never())->method('addViolation');

        $this->provinceAddressConstraintValidator->validate($this->address, $this->constraint);
    }

    public function testAddsViolationBecauseAddressHasNoProvince(): void
    {
        /** @var Country&MockObject $country */
        $country = $this->createMock(Country::class);
        /** @var ConstraintViolationBuilderInterface&MockObject $constraintViolationBuilder */
        $constraintViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $this->address->expects($this->once())->method('getCountryCode')->willReturn('IE');
        $this->countryRepository->expects($this->once())->method('findOneBy')->with(['code' => 'IE'])->willReturn($country);
        $country->expects($this->atLeastOnce())->method('hasProvinces')->willReturn(true);
        $this->address->expects($this->once())->method('getProvinceCode')->willReturn(null);
        $this->provinceAddressConstraintValidator->initialize($this->context);
        $this->context->expects($this->once())->method('getPropertyPath')->willReturn('property_path');
        $this->context->expects($this->once())->method('getViolations')->willReturn(new ConstraintViolationList([
            new ConstraintViolation('message', 'template', [], 'root', 'other_property_path', 'invalidValue'),
        ]));
        $this->context->expects($this->once())->method('buildViolation')->with('sylius.address.province.valid')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->expects($this->once())->method('atPath')->with('provinceCode')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->expects($this->once())->method('addViolation');

        $this->provinceAddressConstraintValidator->validate($this->address, $this->constraint);
    }

    public function testAddsViolationBecauseAddressProvinceDoesNotBelongToCountry(): void
    {
        /** @var Country&MockObject $country */
        $country = $this->createMock(Country::class);
        /** @var Province&MockObject $province */
        $province = $this->createMock(Province::class);
        /** @var ConstraintViolationBuilderInterface&MockObject $constraintViolationBuilder */
        $constraintViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $this->address->expects($this->once())->method('getCountryCode')->willReturn('US');
        $this->countryRepository->expects($this->once())->method('findOneBy')->with(['code' => 'US'])->willReturn($country);
        $country->expects($this->atLeastOnce())->method('hasProvinces')->willReturn(true);
        $this->address->expects($this->atLeastOnce())->method('getProvinceCode')->willReturn('US-AK');
        $this->provinceRepository->expects($this->once())->method('findOneBy')->with(['code' => 'US-AK'])->willReturn($province);
        $country->expects($this->once())->method('hasProvince')->with($province)->willReturn(false);
        $this->provinceAddressConstraintValidator->initialize($this->context);
        $this->context->expects($this->once())->method('getPropertyPath')->willReturn('property_path');
        $this->context->expects($this->once())->method('getViolations')->willReturn(new ConstraintViolationList([
            new ConstraintViolation('message', 'template', [], 'root', 'other_property_path', 'invalidValue'),
        ]));
        $this->context->expects($this->once())->method('buildViolation')->with('sylius.address.province.valid')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->expects($this->once())->method('atPath')->with('provinceCode')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->expects($this->once())->method('addViolation');

        $this->provinceAddressConstraintValidator->validate($this->address, $this->constraint);
    }

    public function testAddsViolationBecauseAddressProvinceDoesNotBelongToCountryWithoutProvinces(): void
    {
        /** @var Country&MockObject $country */
        $country = $this->createMock(Country::class);
        /** @var ConstraintViolationBuilderInterface&MockObject $constraintViolationBuilder */
        $constraintViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $this->address->expects($this->once())->method('getCountryCode')->willReturn('US');
        $this->countryRepository->expects($this->once())->method('findOneBy')->with(['code' => 'US'])->willReturn($country);
        $country->expects($this->once())->method('hasProvinces')->willReturn(false);
        $this->address->expects($this->once())->method('getProvinceCode')->willReturn('US-AK');
        $this->provinceRepository->expects($this->never())->method('findOneBy')->with(['code' => 'US-AK']);
        $this->provinceAddressConstraintValidator->initialize($this->context);
        $this->context->expects($this->once())->method('getPropertyPath')->willReturn('property_path');
        $this->context->expects($this->once())->method('getViolations')->willReturn(new ConstraintViolationList([
            new ConstraintViolation('message', 'template', [], 'root', 'other_property_path', 'invalidValue'),
        ]));
        $this->context->expects($this->once())->method('buildViolation')->with('sylius.address.province.valid')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->expects($this->once())->method('atPath')->with('provinceCode')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->expects($this->once())->method('addViolation');

        $this->provinceAddressConstraintValidator->validate($this->address, $this->constraint);
    }

    public function testDoesNotAddAViolationIfProvinceIsValid(): void
    {
        /** @var Country&MockObject $country */
        $country = $this->createMock(Country::class);
        /** @var Province&MockObject $province */
        $province = $this->createMock(Province::class);

        $this->address->expects($this->once())->method('getCountryCode')->willReturn('US');
        $this->countryRepository->expects($this->once())->method('findOneBy')->with(['code' => 'US'])->willReturn($country);
        $country->expects($this->atLeastOnce())->method('hasProvinces')->willReturn(true);
        $this->address->expects($this->atLeastOnce())->method('getProvinceCode')->willReturn('US-AK');
        $this->provinceRepository->expects($this->once())->method('findOneBy')->with(['code' => 'US-AK'])->willReturn($province);
        $country->expects($this->once())->method('hasProvince')->with($province)->willReturn(true);
        $this->provinceAddressConstraintValidator->initialize($this->context);
        $this->context->expects($this->once())->method('getPropertyPath')->willReturn('property_path');
        $this->context->expects($this->once())->method('getViolations')->willReturn(new ConstraintViolationList([
            new ConstraintViolation('message', 'template', [], 'root', 'other_property_path', 'invalidValue'),
        ]));
        $this->context->expects($this->never())->method('buildViolation')->with('sylius.address.province.valid');

        $this->provinceAddressConstraintValidator->validate($this->address, $this->constraint);
    }
}
