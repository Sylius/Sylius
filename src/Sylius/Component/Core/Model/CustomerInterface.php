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

interface CustomerInterface extends BaseCustomerInterface, UserAwareInterface, ProductReviewerInterface
{
    /**
     * @return Collection|OrderInterface[]
     */
    public function getOrders(): Collection;

    /**
     * @return AddressInterface|null
     */
    public function getDefaultAddress(): ?AddressInterface;

    /**
     * @param AddressInterface|null $defaultAddress
     */
    public function setDefaultAddress(?AddressInterface $defaultAddress): void;

    /**
     * @param AddressInterface $address
     */
    public function addAddress(AddressInterface $address): void;

    /**
     * @param AddressInterface $address
     */
    public function removeAddress(AddressInterface $address): void;

    /**
     * @param AddressInterface $address
     *
     * @return bool
     */
    public function hasAddress(AddressInterface $address): bool;

    /**
     * @return Collection|AddressInterface[]
     */
    public function getAddresses(): Collection;

    /**
     * @return bool
     */
    public function hasUser(): bool;
}
