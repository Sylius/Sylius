<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Addressing\Comparator;

use Sylius\Component\Addressing\Model\AddressInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class AddressComparator implements AddressComparatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function same(AddressInterface $firstAddress, AddressInterface $secondAddress)
    {
        return self::normalize($firstAddress->getCity()) === self::normalize($secondAddress->getCity())
            && self::normalize($firstAddress->getStreet()) === self::normalize($secondAddress->getStreet())
            && self::normalize($firstAddress->getCompany()) === self::normalize($secondAddress->getCompany())
            && self::normalize($firstAddress->getPostcode()) === self::normalize($secondAddress->getPostcode())
            && self::normalize($firstAddress->getLastName()) === self::normalize($secondAddress->getLastName())
            && self::normalize($firstAddress->getFirstName()) === self::normalize($secondAddress->getFirstName())
            && self::normalize($firstAddress->getPhoneNumber()) === self::normalize($secondAddress->getPhoneNumber())
            && self::normalize($firstAddress->getCountryCode()) === self::normalize($secondAddress->getCountryCode())
            && self::normalize($firstAddress->getProvinceCode()) === self::normalize($secondAddress->getProvinceCode())
            && self::normalize($firstAddress->getProvinceName()) === self::normalize($secondAddress->getProvinceName())
        ;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    private function normalize($value)
    {
        return trim(strtolower($value));
    }
}
