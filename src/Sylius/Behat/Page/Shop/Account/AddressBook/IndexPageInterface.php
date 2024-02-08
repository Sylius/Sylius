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

namespace Sylius\Behat\Page\Shop\Account\AddressBook;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;

interface IndexPageInterface extends SymfonyPageInterface
{
    public function getAddressesCount(): int;

    public function hasAddressOf(string $fullName): bool;

    public function hasNoAddresses(): bool;

    public function hasNoDefaultAddress(): bool;

    /**
     * @throws \InvalidArgumentException
     */
    public function getFullNameOfDefaultAddress(): string;

    public function addressOfContains(string $fullName, string $value): bool;

    public function editAddress(string $fullName): void;

    public function deleteAddress(string $fullName): void;

    public function setAsDefault(string $fullName): void;
}
