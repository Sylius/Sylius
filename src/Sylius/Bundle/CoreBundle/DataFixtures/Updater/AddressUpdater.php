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

namespace Sylius\Bundle\CoreBundle\DataFixtures\Updater;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Util\FindOrCreateCountryTrait;
use Sylius\Bundle\CoreBundle\DataFixtures\Util\RandomOrCreateCountryTrait;
use Sylius\Bundle\CoreBundle\DataFixtures\Util\RandomOrCreateCustomerTrait;
use Sylius\Component\Core\Model\AddressInterface;

final class AddressUpdater implements AddressUpdaterInterface
{
    use FindOrCreateCountryTrait;
    use RandomOrCreateCountryTrait;
    use RandomOrCreateCustomerTrait;

    public function __construct(private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function update(AddressInterface $address, array $attributes): void
    {
        $countryCode = $attributes['country_code'];

        if (null !== $countryCode) {
            $this->findOrCreateCountry($this->eventDispatcher, ['code' => $countryCode]);
        }

        $address->setFirstName($attributes['first_name']);
        $address->setLastName($attributes['last_name']);
        $address->setPhoneNumber($attributes['phone_number']);
        $address->setCompany($attributes['company']);
        $address->setStreet($attributes['street']);
        $address->setCity($attributes['city']);
        $address->setPostcode($attributes['postcode']);
        $address->setCountryCode($countryCode ?? $this->randomOrCreateCountry($this->eventDispatcher)->getCode());
        $address->setProvinceName($attributes['province_name']);
        $address->setProvinceCode($attributes['province_code']);
        $address->setCustomer(
            '' !== $attributes['customer']
                ? $attributes['customer']
                : $this->randomOrCreateCustomer($this->eventDispatcher)->object()
        );
    }
}
