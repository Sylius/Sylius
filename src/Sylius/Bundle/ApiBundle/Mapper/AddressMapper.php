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

namespace Sylius\Bundle\ApiBundle\Mapper;

use Sylius\Component\Core\Model\AddressInterface;

final class AddressMapper implements AddressMapperInterface
{
    public function mapExisting(AddressInterface $currentAddress, AddressInterface $targetAddress): AddressInterface
    {
        $currentAddress->setFirstName($targetAddress->getFirstName());
        $currentAddress->setLastName($targetAddress->getLastName());
        $currentAddress->setCompany($targetAddress->getCompany());
        $currentAddress->setStreet($targetAddress->getStreet());
        $currentAddress->setCountryCode($targetAddress->getCountryCode());
        $currentAddress->setCity($targetAddress->getCity());
        $currentAddress->setPostcode($targetAddress->getPostcode());
        $currentAddress->setPhoneNumber($targetAddress->getPhoneNumber());

        if (null !== $targetAddress->getProvinceCode()) {
            $currentAddress->setProvinceCode($targetAddress->getProvinceCode());
            $currentAddress->setProvinceName($targetAddress->getProvinceName());
        }

        return $currentAddress;
    }
}
