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

namespace Sylius\Component\Core\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Customer\Model\CustomerInterface as BaseCustomerInterface;
use Sylius\Component\User\Model\UserAwareInterface;
use Sylius\Component\User\Model\UserInterface as BaseUserInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
interface CustomerInterface extends BaseCustomerInterface, UserAwareInterface
{
    /**
     * @return Collection|OrderInterface[]
     */
    public function getOrders();

    /**
     * @return AddressInterface
     */
    public function getDefaultAddress();

    /**
     * @param AddressInterface|null $defaultAddress
     */
    public function setDefaultAddress(AddressInterface $defaultAddress = null);

    /**
     * @param AddressInterface $address
     */
    public function addAddress(AddressInterface $address);

    /**
     * @param AddressInterface $address
     */
    public function removeAddress(AddressInterface $address);

    /**
     * @param AddressInterface $address
     *
     * @return bool
     */
    public function hasAddress(AddressInterface $address);

    /**
     * @return Collection|AddressInterface[]
     */
    public function getAddresses();

    /**
     * @return BaseUserInterface
     */
    public function getUser();

    /**
     * @param BaseUserInterface|null $user
     */
    public function setUser(BaseUserInterface $user = null);

    /**
     * @return bool
     */
    public function hasUser();
}
