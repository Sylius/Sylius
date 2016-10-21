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
     * @return bool
     */
    public function isSingleAddressOnList();

    /**
     * @param string $fullName
     * 
     * @return bool
     */
    public function hasAddressFullName($fullName);

    /**
     * @return bool
     */
    public function hasNoExistingAddressesMessage();

    /**
     * @param string $fullName
     */
    public function deleteAddress($fullName);
}
