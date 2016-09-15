<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Order;

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
interface UpdateShippingAddressPageInterface extends UpdatePageInterface
{
    /**
     * @param string $firstName
     */
    public function specifyFirstName($firstName);

    /**
     * @param string $lastName
     */
    public function specifyLastName($lastName);

    /**
     * @param string $street
     */
    public function specifyStreet($street);

    /**
     * @param string $city
     */
    public function specifyCity($city);

    /**
     * @param string $postcode
     */
    public function specifyPostcode($postcode);

    /**
     * @param string $country
     */
    public function chooseCountry($country);

    /**
     * @param string $city
     * @param string $street
     * @param string $postcode
     * @param string $country
     * @param string $firstAndLastName
     */
    public function specifyShippingAddress($city, $street, $postcode, $country, $firstAndLastName);
}
