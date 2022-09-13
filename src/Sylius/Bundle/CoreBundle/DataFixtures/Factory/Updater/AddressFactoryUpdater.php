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

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory\Updater;

use Sylius\Component\Core\Model\AddressInterface;

final class AddressFactoryUpdater implements AddressFactoryUpdaterInterface
{
    public function update(AddressInterface $address, array $attributes): void
    {
        $address->setFirstName($attributes['first_name']);
        $address->setLastName($attributes['last_name']);
        $address->setPhoneNumber($attributes['phone_number']);
        $address->setCompany($attributes['company']);
        $address->setStreet($attributes['street']);
        $address->setCity($attributes['city']);
        $address->setPostcode($attributes['postcode']);
        $address->setCountryCode($attributes['country_code']);
        $address->setProvinceName($attributes['province_name']);
        $address->setProvinceCode($attributes['province_code']);
        $address->setCustomer($attributes['customer']);
    }
}
