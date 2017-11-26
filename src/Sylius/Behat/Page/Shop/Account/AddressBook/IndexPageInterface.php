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

use Sylius\Behat\Page\SymfonyPageInterface;

interface IndexPageInterface extends SymfonyPageInterface
{
    /**
     * @return int
     */
    public function getAddressesCount(): int;

    /**
     * @param string $fullName
     *
     * @return bool
     */
    public function hasAddressOf(string $fullName): bool;

    /**
     * @return bool
     */
    public function hasNoAddresses(): bool;

    /**
     * @return bool
     */
    public function hasNoDefaultAddress(): bool;

    /**
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function getFullNameOfDefaultAddress(): string;

    /**
     * @param string $fullName
     * @param string $value
     *
     * @return bool
     */
    public function addressOfContains(string $fullName, string $value): bool;

    /**
     * @param string $fullName
     */
    public function editAddress(string $fullName): void;

    /**
     * @param string $fullName
     */
    public function deleteAddress(string $fullName): void;

    /**
     * @param string $fullName
     */
    public function setAsDefault(string $fullName): void;
}
