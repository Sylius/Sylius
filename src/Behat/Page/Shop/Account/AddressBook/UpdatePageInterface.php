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

interface UpdatePageInterface extends SymfonyPageInterface
{
    public function fillField(string $field, ?string $value): void;

    public function getSpecifiedProvince(): string;

    public function getSelectedProvince(): string;

    public function specifyProvince(string $name): void;

    public function selectProvince(string $name): void;

    public function selectCountry(string $name): void;

    public function waitForFormToStopLoading(): void;

    public function saveChanges(): void;
}
