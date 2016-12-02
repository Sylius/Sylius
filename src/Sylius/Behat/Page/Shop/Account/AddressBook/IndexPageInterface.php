<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Shop\Account\AddressBook;

use Sylius\Behat\Page\SymfonyPageInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
interface IndexPageInterface extends SymfonyPageInterface
{
    /**
     * @return int
     */
    public function getAddressesCount();

    /**
     * @param string $fullName
     *
     * @return bool
     */
    public function hasAddressOf($fullName);

    /**
     * @return bool
     */
    public function hasNoAddresses();

    /**
     * @return bool
     */
    public function hasNoDefaultAddress();

    /**
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function getFullNameOfDefaultAddress();

    /**
     * @param string $fullName
     * @param string $value
     *
     * @return bool
     */
    public function addressOfContains($fullName, $value);

    /**
     * @param string $fullName
     */
    public function editAddress($fullName);

    /**
     * @param string $fullName
     */
    public function deleteAddress($fullName);

    /**
     * @param string $fullName
     */
    public function setAsDefault($fullName);
}
