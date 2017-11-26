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
    /**
     * @param string $field
     * @param string $value
     */
    public function fillField(string $field, string $value): void;

    /**
     * @return string
     */
    public function getSpecifiedProvince(): string;

    /**
     * @return string
     */
    public function getSelectedProvince(): string;

    /**
     * @param string $name
     */
    public function specifyProvince(string $name): void;

    /**
     * @param string $name
     */
    public function selectProvince(string $name): void;

    /**
     * @param string $name
     */
    public function selectCountry(string $name): void;

    public function saveChanges(): void;
}
