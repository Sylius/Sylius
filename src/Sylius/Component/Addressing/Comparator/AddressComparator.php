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

namespace Sylius\Component\Addressing\Comparator;

use Sylius\Component\Addressing\Model\AddressInterface;

final class AddressComparator implements AddressComparatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function equal(AddressInterface $firstAddress, AddressInterface $secondAddress): bool
    {
        return $this->normalizeAddress($firstAddress) === $this->normalizeAddress($secondAddress);
    }

    /**
     * @param AddressInterface $address
     *
     * @return array
     */
    private function normalizeAddress(AddressInterface $address): array
    {
        return array_map(function ($value) {
            return strtolower(trim((string) $value));
        }, [
            $address->getCity(),
            $address->getCompany(),
            $address->getCountryCode(),
            $address->getFirstName(),
            $address->getLastName(),
            $address->getPhoneNumber(),
            $address->getPostcode(),
            $address->getProvinceCode(),
            $address->getProvinceName(),
            $address->getStreet(),
        ]);
    }
}
