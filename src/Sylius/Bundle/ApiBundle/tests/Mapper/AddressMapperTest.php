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

namespace Tests\Sylius\Bundle\ApiBundle\Mapper;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Mapper\AddressMapper;
use Sylius\Component\Core\Model\AddressInterface;

final class AddressMapperTest extends TestCase
{
    private AddressMapper $addressMapper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->addressMapper = new AddressMapper();
    }

    public function testUpdatesAnAddressWithAProvince(): void
    {
        /** @var AddressInterface|MockObject $currentAddressMock */
        $currentAddressMock = $this->createMock(AddressInterface::class);
        /** @var AddressInterface|MockObject $targetAddressMock */
        $targetAddressMock = $this->createMock(AddressInterface::class);

        $targetAddressMock->expects(self::once())->method('getFirstName')->willReturn('John');
        $targetAddressMock->expects(self::once())->method('getLastName')->willReturn('Doe');
        $targetAddressMock->expects(self::once())->method('getCompany')->willReturn('CocaCola');
        $targetAddressMock->expects(self::once())->method('getStreet')->willReturn('Green Avenue');
        $targetAddressMock->expects(self::once())->method('getCountryCode')->willReturn('US');
        $targetAddressMock->expects(self::once())->method('getCity')->willReturn('New York');
        $targetAddressMock->expects(self::once())->method('getPostcode')->willReturn('00000');
        $targetAddressMock->expects(self::once())->method('getPhoneNumber')->willReturn('123456789');
        $targetAddressMock->expects(self::atLeastOnce())->method('getProvinceCode')->willReturn('999');
        $targetAddressMock->expects(self::once())->method('getProvinceName')->willReturn('east');
        $currentAddressMock->expects(self::once())->method('setFirstName')->with('John');
        $currentAddressMock->expects(self::once())->method('setLastName')->with('Doe');
        $currentAddressMock->expects(self::once())->method('setCompany')->with('CocaCola');
        $currentAddressMock->expects(self::once())->method('setStreet')->with('Green Avenue');
        $currentAddressMock->expects(self::once())->method('setCountryCode')->with('US');
        $currentAddressMock->expects(self::once())->method('setCity')->with('New York');
        $currentAddressMock->expects(self::once())->method('setPostcode')->with('00000');
        $currentAddressMock->expects(self::once())->method('setPhoneNumber')->with('123456789');
        $currentAddressMock->expects(self::once())->method('setProvinceCode')->with('999');
        $currentAddressMock->expects(self::once())->method('setProvinceName')->with('east');

        self::assertSame($currentAddressMock, $this->addressMapper->mapExisting($currentAddressMock, $targetAddressMock));
    }

    public function testUpdatesAnAddressWithoutAProvince(): void
    {
        /** @var AddressInterface|MockObject $currentAddressMock */
        $currentAddressMock = $this->createMock(AddressInterface::class);
        /** @var AddressInterface|MockObject $targetAddressMock */
        $targetAddressMock = $this->createMock(AddressInterface::class);

        $targetAddressMock->expects(self::once())->method('getFirstName')->willReturn('John');
        $targetAddressMock->expects(self::once())->method('getLastName')->willReturn('Doe');
        $targetAddressMock->expects(self::once())->method('getCompany')->willReturn('CocaCola');
        $targetAddressMock->expects(self::once())->method('getStreet')->willReturn('Green Avenue');
        $targetAddressMock->expects(self::once())->method('getCountryCode')->willReturn('US');
        $targetAddressMock->expects(self::once())->method('getCity')->willReturn('New York');
        $targetAddressMock->expects(self::once())->method('getPostcode')->willReturn('00000');
        $targetAddressMock->expects(self::once())->method('getPhoneNumber')->willReturn('123456789');
        $targetAddressMock->expects(self::once())->method('getProvinceCode')->willReturn(null);
        $targetAddressMock->expects(self::never())->method('getProvinceName')->willReturn('east');
        $currentAddressMock->expects(self::once())->method('setFirstName')->with('John');
        $currentAddressMock->expects(self::once())->method('setLastName')->with('Doe');
        $currentAddressMock->expects(self::once())->method('setCompany')->with('CocaCola');
        $currentAddressMock->expects(self::once())->method('setStreet')->with('Green Avenue');
        $currentAddressMock->expects(self::once())->method('setCountryCode')->with('US');
        $currentAddressMock->expects(self::once())->method('setCity')->with('New York');
        $currentAddressMock->expects(self::once())->method('setPostcode')->with('00000');
        $currentAddressMock->expects(self::once())->method('setPhoneNumber')->with('123456789');
        $currentAddressMock->expects(self::never())->method('setProvinceCode')->with('999');
        $currentAddressMock->expects(self::never())->method('setProvinceName')->with('east');

        self::assertSame($currentAddressMock, $this->addressMapper->mapExisting($currentAddressMock, $targetAddressMock));
    }
}
