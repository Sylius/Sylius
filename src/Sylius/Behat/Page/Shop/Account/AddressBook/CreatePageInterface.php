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

namespace Sylius\Behat\Page\Shop\Account\AddressBook;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;
use Sylius\Component\Core\Model\AddressInterface;

interface CreatePageInterface extends SymfonyPageInterface
{
    public function fillAddressData(AddressInterface $address);

    /**
     * @param string $name
     */
    public function selectCountry($name);

    public function addAddress();

    /**
     * @return bool
     */
    public function hasProvinceValidationMessage();

    /**
     * @return int
     */
    public function countValidationMessages();
}
