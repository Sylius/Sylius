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

interface UpdatePageInterface extends SymfonyPageInterface
{
    public function fillField(string $field, string $value);

    public function getSpecifiedProvince(): string;

    public function getSelectedProvince(): string;

    public function specifyProvince(string $name);

    public function selectProvince(string $name);

    public function selectCountry(string $name);

    public function saveChanges();
}
