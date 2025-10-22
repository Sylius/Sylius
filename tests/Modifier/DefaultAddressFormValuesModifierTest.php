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

namespace Modifier;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ShopBundle\Modifier\DefaultAddressFormValuesModifier;
use Sylius\Component\Addressing\Model\AddressInterface;

final class DefaultAddressFormValuesModifierTest extends TestCase
{
    public function test_it_modifies_address_data(): void
    {
        $address = $this->createMock(AddressInterface::class);
        $address->method('getFirstName')->willReturn('Helmut');
        $address->method('getLastName')->willReturn('Grokenberger');
        $address->method('getPhoneNumber')->willReturn('+123 123 555 0000');
        $address->method('getCompany')->willReturn('Berlin Taxi Cooperative');
        $address->method('getCountryCode')->willReturn('DE');
        $address->method('getProvinceCode')->willReturn('BE');
        $address->method('getProvinceName')->willReturn('Berlin');
        $address->method('getStreet')->willReturn('Fiktive Straße 7');
        $address->method('getCity')->willReturn('Berlin');
        $address->method('getPostcode')->willReturn('DE-00000');

        $modifier = new DefaultAddressFormValuesModifier();

        $result = $modifier->modify([], $address);

        $this->assertSame([
            'firstName' => 'Helmut',
            'lastName' => 'Grokenberger',
            'phoneNumber' => '+123 123 555 0000',
            'company' => 'Berlin Taxi Cooperative',
            'countryCode' => 'DE',
            'provinceCode' => 'BE',
            'provinceName' => 'Berlin',
            'street' => 'Fiktive Straße 7',
            'city' => 'Berlin',
            'postcode' => 'DE-00000',
        ], $result);
    }
}
