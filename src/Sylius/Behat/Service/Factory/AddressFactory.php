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

namespace Sylius\Behat\Service\Factory;

use Sylius\Component\Core\Factory\AddressFactory as BaseAddressFactory;
use Sylius\Component\Core\Model\AddressInterface;

final class AddressFactory extends BaseAddressFactory implements AddressFactoryInterface
{
    public function __construct(private readonly BaseAddressFactory $decoratedAddressFactory)
    {
        parent::__construct($decoratedAddressFactory);
    }

    public function createDefault(): AddressInterface
    {
        $address = $this->decoratedAddressFactory->createNew();

        $address->setCity('New York');
        $address->setStreet('Wall Street');
        $address->setPostcode('00-001');
        $address->setCountryCode('US');
        $address->setFirstName('Richy');
        $address->setLastName('Rich');

        return $address;
    }

    public function createDefaultWithCountryCode(string $countryCode): AddressInterface
    {
        $address = $this->decoratedAddressFactory->createNew();

        $address->setCity('New York');
        $address->setStreet('Wall Street');
        $address->setPostcode('00-001');
        $address->setCountryCode($countryCode);
        $address->setFirstName('Richy');
        $address->setLastName('Rich');

        return $address;
    }
}
