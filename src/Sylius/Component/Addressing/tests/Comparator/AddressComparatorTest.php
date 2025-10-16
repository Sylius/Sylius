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

namespace Tests\Sylius\Component\Addressing\Comparator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Addressing\Comparator\AddressComparator;
use Sylius\Component\Addressing\Comparator\AddressComparatorInterface;
use Sylius\Component\Addressing\Model\AddressInterface;

final class AddressComparatorTest extends TestCase
{
    private AddressComparator $addressComparator;

    protected function setUp(): void
    {
        $this->addressComparator = new AddressComparator();
    }

    public function testImplementsAddressComparatorInterface(): void
    {
        self::assertInstanceOf(AddressComparatorInterface::class, $this->addressComparator);
    }

    public function testReturnsFalseIfAddressesDiffer(): void
    {
        /** @var AddressInterface&MockObject $firstAddressMock */
        $firstAddressMock = $this->createMock(AddressInterface::class);
        /** @var AddressInterface&MockObject $secondAddressMock */
        $secondAddressMock = $this->createMock(AddressInterface::class);
        $firstAddressMock->expects(self::once())->method('getCity')->willReturn('Stoke-On-Trent');
        $firstAddressMock->expects(self::once())->method('getStreet')->willReturn('Villiers St');
        $firstAddressMock->expects(self::once())->method('getCompany')->willReturn('Pizzeria');
        $firstAddressMock->expects(self::once())->method('getPostcode')->willReturn('ST3 4HB');
        $firstAddressMock->expects(self::once())->method('getLastName')->willReturn('Johnson');
        $firstAddressMock->expects(self::once())->method('getFirstName')->willReturn('Gerald');
        $firstAddressMock->expects(self::once())->method('getPhoneNumber')->willReturn('000');
        $firstAddressMock->expects(self::once())->method('getCountryCode')->willReturn('UK');
        $firstAddressMock->expects(self::once())->method('getProvinceCode')->willReturn('UK-WestMidlands');
        $firstAddressMock->expects(self::once())->method('getProvinceName')->willReturn(null);
        $secondAddressMock->expects(self::once())->method('getCity')->willReturn('Toowoomba');
        $secondAddressMock->expects(self::once())->method('getStreet')->willReturn('Ryans Dr');
        $secondAddressMock->expects(self::once())->method('getCompany')->willReturn('Burger');
        $secondAddressMock->expects(self::once())->method('getPostcode')->willReturn('4350');
        $secondAddressMock->expects(self::once())->method('getLastName')->willReturn('Jones');
        $secondAddressMock->expects(self::once())->method('getFirstName')->willReturn('Mia');
        $secondAddressMock->expects(self::once())->method('getPhoneNumber')->willReturn('999');
        $secondAddressMock->expects(self::once())->method('getCountryCode')->willReturn('AU');
        $secondAddressMock->expects(self::once())->method('getProvinceCode')->willReturn(null);
        $secondAddressMock->expects(self::once())->method('getProvinceName')->willReturn('Queensland');
        self::assertFalse($this->addressComparator->equal($firstAddressMock, $secondAddressMock));
    }

    public function testReturnsTrueWhenAddressesAreTheSame(): void
    {
        /** @var AddressInterface&MockObject $addressMock */
        $addressMock = $this->createMock(AddressInterface::class);
        $addressMock->expects(self::atLeastOnce())->method('getCity')->willReturn('Toowoomba');
        $addressMock->expects(self::atLeastOnce())->method('getStreet')->willReturn('Ryans Dr');
        $addressMock->expects(self::atLeastOnce())->method('getCompany')->willReturn('Burger');
        $addressMock->expects(self::atLeastOnce())->method('getPostcode')->willReturn('4350');
        $addressMock->expects(self::atLeastOnce())->method('getLastName')->willReturn('Jones');
        $addressMock->expects(self::atLeastOnce())->method('getFirstName')->willReturn('Mia');
        $addressMock->expects(self::atLeastOnce())->method('getPhoneNumber')->willReturn('999');
        $addressMock->expects(self::atLeastOnce())->method('getCountryCode')->willReturn('AU');
        $addressMock->expects(self::atLeastOnce())->method('getProvinceCode')->willReturn(null);
        $addressMock->expects(self::atLeastOnce())->method('getProvinceName')->willReturn('Queensland');
        self::assertTrue($this->addressComparator->equal($addressMock, $addressMock));
    }

    public function testIgnoresLeadingAndTrailingSpacesOrLetterCases(): void
    {
        /** @var AddressInterface&MockObject $firstAddressMock */
        $firstAddressMock = $this->createMock(AddressInterface::class);
        /** @var AddressInterface&MockObject $secondAddressMock */
        $secondAddressMock = $this->createMock(AddressInterface::class);
        $firstAddressMock->expects(self::once())->method('getCity')->willReturn('TOOWOOMBA');
        $firstAddressMock->expects(self::once())->method('getStreet')->willReturn('Ryans Dr     ');
        $firstAddressMock->expects(self::once())->method('getCompany')->willReturn('   Burger');
        $firstAddressMock->expects(self::once())->method('getPostcode')->willReturn(' 4350 ');
        $firstAddressMock->expects(self::once())->method('getLastName')->willReturn('jones ');
        $firstAddressMock->expects(self::once())->method('getFirstName')->willReturn('mIa');
        $firstAddressMock->expects(self::once())->method('getPhoneNumber')->willReturn(' 999');
        $firstAddressMock->expects(self::once())->method('getCountryCode')->willReturn('au');
        $firstAddressMock->expects(self::once())->method('getProvinceCode')->willReturn(null);
        $firstAddressMock->expects(self::once())->method('getProvinceName')->willReturn('qUEENSLAND');
        $secondAddressMock->expects(self::once())->method('getCity')->willReturn('Toowoomba');
        $secondAddressMock->expects(self::once())->method('getStreet')->willReturn('Ryans Dr');
        $secondAddressMock->expects(self::once())->method('getCompany')->willReturn('Burger');
        $secondAddressMock->expects(self::once())->method('getPostcode')->willReturn('4350');
        $secondAddressMock->expects(self::once())->method('getLastName')->willReturn('Jones');
        $secondAddressMock->expects(self::once())->method('getFirstName')->willReturn('Mia');
        $secondAddressMock->expects(self::once())->method('getPhoneNumber')->willReturn('999');
        $secondAddressMock->expects(self::once())->method('getCountryCode')->willReturn('AU');
        $secondAddressMock->expects(self::once())->method('getProvinceCode')->willReturn(null);
        $secondAddressMock->expects(self::once())->method('getProvinceName')->willReturn('Queensland');
        self::assertTrue($this->addressComparator->equal($firstAddressMock, $secondAddressMock));
    }
}
