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
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
interface UpdatePageInterface extends SymfonyPageInterface
{
    /**
     * @param string $field
     * @param string $value
     */
    public function fillField($field, $value);

    /**
     * @return string
     */
    public function getSpecifiedProvince();

    /**
     * @return string
     */
    public function getSelectedProvince();

    /**
     * @param string $name
     */
    public function specifyProvince($name);

    /**
     * @param string $name
     */
    public function selectProvince($name);

    /**
     * @param string $name
     */
    public function selectCountry($name);

    public function saveChanges();
}
