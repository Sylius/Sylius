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

namespace Sylius\Behat\Context\Api;

use Behat\Behat\Context\Context;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;

final class AddressContext implements Context
{
    /** @var AddressRepositoryInterface */
    private $addressRepository;

    public function __construct(AddressRepositoryInterface $addressRepository)
    {
        $this->addressRepository = $addressRepository;
    }

    /**
     * @Then /^store should contain only ("[^"]*", "[^"]*", "[^"]*", "[^"]*", "[^"]*" address)$/
     */
    public function storeShouldContainOnlyAddress(AddressInterface $address): void
    {
        /** @var AddressInterface $addressFromDatabase */
        foreach ($this->addressRepository->findAll() as $addressFromDatabase) {
            if (
                $addressFromDatabase->getFirstName() !== $address->getFirstName() ||
                $addressFromDatabase->getLastName() !== $address->getLastName() ||
                $addressFromDatabase->getCity() !== $address->getCity() ||
                $addressFromDatabase->getPostcode() !== $address->getPostcode() ||
                $addressFromDatabase->getCountryCode() !== $address->getCountryCode()
            ) {
                throw new \InvalidArgumentException('The store has other address than should');
            }
        }
    }
}
