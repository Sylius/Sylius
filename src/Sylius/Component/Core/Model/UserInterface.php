<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Component\Core\Model;

use Doctrine\Common\Collections\Collection;
use FOS\UserBundle\Model\UserInterface as BaseUserInterface;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use FOS\UserBundle\Model\UserInterface as FosUserInterface;
use Sylius\Component\Addressing\Model\AddressAwareInterface;
use Sylius\Component\Addressing\Model\BillingAddressAwareInterface;
use Sylius\Component\Addressing\Model\ShippingAddressAwareInterface;
use Sylius\Component\Order\Model\UserInterface as BaseUserInterface;
use Sylius\Bundle\ResourceBundle\Model\TimestampableInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface UserInterface extends
    FosUserInterface,
    BaseUserInterface,
    AddressAwareInterface,
    BillingAddressAwareInterface,
    ShippingAddressAwareInterface,
    TimestampableInterface
{
    /**
     * @return string
     */
    public function getFirstName();

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName);

    /**
     * @return string
     */
    public function getLastName();

    /**
     * @param string $lastName
     */
    public function setLastName($lastName);

    /**
     * @return string
     */
    public function getCurrency();

    /**
     * @param string $currency
     */
    public function setCurrency($currency);

    /**
     * @return Collection|OrderInterface[]
     */
    public function getOrders();
}
