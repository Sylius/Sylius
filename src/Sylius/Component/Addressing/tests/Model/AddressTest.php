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

namespace Tests\Sylius\Component\Addressing\Model;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Addressing\Model\Address;
use Sylius\Component\Addressing\Model\AddressInterface;

final class AddressTest extends TestCase
{
    private Address $address;

    protected function setUp(): void
    {
        $this->address = new Address();
    }

    public function testImplementsSyliusAddressInterface(): void
    {
        self::assertInstanceOf(AddressInterface::class, $this->address);
    }

    public function testHasNoIdByDefault(): void
    {
        self::assertNull($this->address->getId());
    }

    public function testHasNoFirstNameByDefault(): void
    {
        self::assertNull($this->address->getFirstName());
    }

    public function testItsFirstNameIsMutable(): void
    {
        $this->address->setFirstName('John');
        self::assertSame('John', $this->address->getFirstName());
    }

    public function testHasNoLastNameByDefault(): void
    {
        self::assertNull($this->address->getLastName());
    }

    public function testItsLastNameIsMutable(): void
    {
        $this->address->setLastName('Doe');
        self::assertSame('Doe', $this->address->getLastName());
    }

    public function testReturnsCorrectFullName(): void
    {
        $this->address->setFirstName('John');
        $this->address->setLastName('Doe');

        self::assertSame('John Doe', $this->address->getFullName());
    }

    public function testHasNoPhoneNumberByDefault(): void
    {
        self::assertNull($this->address->getPhoneNumber());
    }

    public function testItsPhoneNumberIsMutable(): void
    {
        $this->address->setPhoneNumber('+48555123456');
        self::assertSame('+48555123456', $this->address->getPhoneNumber());
    }

    public function testHasNoCountryByDefault(): void
    {
        self::assertNull($this->address->getCountryCode());
    }

    public function testItsCountryCodeIsMutable(): void
    {
        $this->address->setCountryCode('IE');
        self::assertSame('IE', $this->address->getCountryCode());
    }

    public function testAllowsToUnsetTheCountryCode(): void
    {
        $this->address->setCountryCode('IE');
        self::assertSame('IE', $this->address->getCountryCode());

        $this->address->setCountryCode(null);
        self::assertNull($this->address->getCountryCode());
    }

    public function testUnsetsTheProvinceCodeWhenErasingCountryCode(): void
    {
        $this->address->setCountryCode('IE');
        $this->address->setProvinceCode('DU');

        $this->address->setCountryCode(null);

        self::assertNull($this->address->getCountryCode());
        self::assertNull($this->address->getProvinceCode());
    }

    public function testHasNoProvinceCodeByDefault(): void
    {
        self::assertNull($this->address->getProvinceCode());
    }

    public function testSetsProvinceCodeEvenIfThereIsNoCountryCode(): void
    {
        $this->address->setCountryCode(null);
        $this->address->setProvinceCode('DU');
        self::assertSame('DU', $this->address->getProvinceCode());
    }

    public function testItsProvinceCodeIsMutable(): void
    {
        $this->address->setCountryCode('IE');

        $this->address->setProvinceCode('DU');
        self::assertSame('DU', $this->address->getProvinceCode());
    }

    public function testHasNoProvinceNameByDefault(): void
    {
        self::assertNull($this->address->getProvinceName());
    }

    public function testItsProvinceNameIsMutable(): void
    {
        $this->address->setProvinceName('Utah');
        self::assertSame('Utah', $this->address->getProvinceName());
    }

    public function testHasNoCompanyByDefault(): void
    {
        self::assertNull($this->address->getCompany());
    }

    public function testItsCompanyIsMutable(): void
    {
        $this->address->setCompany('Foo Ltd.');
        self::assertSame('Foo Ltd.', $this->address->getCompany());
    }

    public function testHasNoStreetByDefault(): void
    {
        self::assertNull($this->address->getStreet());
    }

    public function testItsStreetIsMutable(): void
    {
        $this->address->setStreet('Foo Street 3/44');
        self::assertSame('Foo Street 3/44', $this->address->getStreet());
    }

    public function testHasNoCityByDefault(): void
    {
        self::assertNull($this->address->getCity());
    }

    public function testItsCityIsMutable(): void
    {
        $this->address->setCity('New York');
        self::assertSame('New York', $this->address->getCity());
    }

    public function testHasNoPostcodeByDefault(): void
    {
        self::assertNull($this->address->getPostcode());
    }

    public function testItsPostcodeIsMutable(): void
    {
        $this->address->setPostcode('24154');
        self::assertSame('24154', $this->address->getPostcode());
    }

    public function testItsLastUpdateTimeIsUndefinedByDefault(): void
    {
        self::assertNull($this->address->getUpdatedAt());
    }
}
