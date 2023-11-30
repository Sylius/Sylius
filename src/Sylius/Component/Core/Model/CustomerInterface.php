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

namespace Sylius\Component\Core\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Customer\Model\CustomerInterface as BaseCustomerInterface;
use Sylius\Component\User\Model\UserAwareInterface;
use Sylius\Component\User\Model\UserInterface;

interface CustomerInterface extends BaseCustomerInterface, UserAwareInterface, ProductReviewerInterface
{
    /**
     * @return Collection<array-key, OrderInterface>
     */
    public function getOrders(): Collection;

    public function getDefaultAddress(): ?AddressInterface;

    public function setDefaultAddress(?AddressInterface $defaultAddress): void;

    public function addAddress(AddressInterface $address): void;

    public function removeAddress(AddressInterface $address): void;

    public function hasAddress(AddressInterface $address): bool;

    /**
     * @return Collection<array-key, AddressInterface>
     */
    public function getAddresses(): Collection;

    public function hasUser(): bool;

    /**
     * @return ShopUserInterface|UserInterface|null
     */
    public function getUser(): ?UserInterface;

    /**
     * @param ShopUserInterface|UserInterface|null $user
     */
    public function setUser(?UserInterface $user);
}
